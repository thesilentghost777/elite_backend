<?php

namespace App\Services;

use App\Models\CareerProfile;
use App\Models\CorrespondenceAnswer;
use App\Models\CorrespondenceCategory;
use App\Models\CorrespondenceQuestion;
use App\Models\EliteUser;
use App\Models\ProfileMatching;
use App\Models\UserCorrespondence;
use App\Models\UserProfileChoice;
use App\Models\UserProfileResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CorrespondenceService
{
    /**
     * Récupérer toutes les questions du formulaire de correspondance
     */
    public function getQuestions(): array
    {
        $categories = CorrespondenceCategory::with([
            'questions' => function ($query) {
                $query->active()->orderBy('ordre')->with('answers');
            }
        ])->orderBy('ordre')->get();

        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'nom' => $category->nom,
                'description' => $category->description,
                'questions' => $category->questions->map(function ($question) {
                    return [
                        'id' => $question->id,
                        'question' => $question->question,
                        'type' => $question->type,
                        'answers' => $question->answers->map(function ($answer) {
                            return [
                                'id' => $answer->id,
                                'texte' => $answer->texte,
                            ];
                        }),
                    ];
                }),
            ];
        })->toArray();
    }

    /**
     * Soumettre les réponses au formulaire
     */
    public function submitResponses(EliteUser $user, array $responses): array
{
    Log::info('submitResponses called', [
        'user_id' => $user->id,
        'responses_count' => count($responses),
    ]);

    if ($user->correspondence_completed) {
        Log::warning('User already completed correspondence', [
            'user_id' => $user->id,
        ]);

        throw ValidationException::withMessages([
            'correspondence' => ['Vous avez déjà complété le formulaire de correspondance.']
        ]);
    }

    return DB::transaction(function () use ($user, $responses) {
        Log::debug('Starting DB transaction for submitResponses', [
            'user_id' => $user->id,
        ]);

        // Sauvegarder les réponses
        foreach ($responses as $response) {
            Log::debug('Saving user response', [
                'user_id' => $user->id,
                'question_id' => $response['question_id'] ?? null,
                'answer_id' => $response['answer_id'] ?? null,
            ]);

            UserCorrespondence::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'question_id' => $response['question_id'],
                ],
                [
                    'answer_id' => $response['answer_id'],
                ]
            );
        }

        Log::debug('All responses saved', [
            'user_id' => $user->id,
        ]);

        // Calculer les profils correspondants
        Log::debug('Calculating matching profiles', [
            'user_id' => $user->id,
        ]);

        $results = $this->calculateMatchingProfiles($user);

        Log::info('Matching profiles calculated', [
            'user_id' => $user->id,
            'results_count' => count($results),
            'top_result' => $results[0] ?? null,
        ]);

        // Sauvegarder les résultats
        Log::debug('Deleting previous profile results', [
            'user_id' => $user->id,
        ]);

        UserProfileResult::where('user_id', $user->id)->delete();

        $rang = 1;
        foreach ($results as $result) {
            Log::debug('Saving profile result', [
                'user_id' => $user->id,
                'profile_id' => $result['profile_id'],
                'score' => $result['score'],
                'rang' => $rang,
            ]);

            UserProfileResult::create([
                'user_id' => $user->id,
                'profile_id' => $result['profile_id'],
                'score' => $result['score'],
                'rang' => $rang++,
            ]);
        }

        // Marquer le formulaire comme complété
        Log::info('Marking correspondence as completed', [
            'user_id' => $user->id,
        ]);

        $user->update(['correspondence_completed' => true]);

        Log::info('submitResponses completed successfully', [
            'user_id' => $user->id,
        ]);

        return $results;
    });
}


    /**
     * Calculer les profils correspondants basés sur les réponses
     */
    private function calculateMatchingProfiles(EliteUser $user): array
    {
        // Récupérer les réponses de l'utilisateur
        $userAnswerIds = UserCorrespondence::where('user_id', $user->id)
            ->pluck('answer_id')
            ->toArray();

        if (empty($userAnswerIds)) {
            return [];
        }

        // Calculer les scores pour chaque profil
        $profileScores = ProfileMatching::whereIn('answer_id', $userAnswerIds)
            ->select('profile_id', DB::raw('SUM(poids) as total_score'))
            ->groupBy('profile_id')
            ->orderByDesc('total_score')
            ->limit(5)
            ->get();

        // Calculer le score maximum possible
        $maxPossibleScore = ProfileMatching::whereIn('answer_id', $userAnswerIds)
            ->max(DB::raw('(SELECT SUM(pm2.poids) FROM profile_matchings pm2 WHERE pm2.profile_id = profile_matchings.profile_id)'));

        if (!$maxPossibleScore || $maxPossibleScore == 0) {
            $maxPossibleScore = 100;
        }

        // Formater les résultats avec pourcentages
        $results = [];
        foreach ($profileScores as $score) {
            $profile = CareerProfile::find($score->profile_id);
            if ($profile && $profile->active) {
                $percentage = min(100, round(($score->total_score / $maxPossibleScore) * 100, 2));
                $results[] = [
                    'profile_id' => $profile->id,
                    'profile' => [
                        'id' => $profile->id,
                        'nom' => $profile->nom,
                        'slug' => $profile->slug,
                        'description' => $profile->description,
                        'secteur' => $profile->secteur,
                        'image_url' => $profile->image_url,
                        'debouches' => $profile->debouches,
                        'niveau_minimum' => $profile->niveau_minimum,
                    ],
                    'score' => $percentage,
                ];
            }
        }

        // S'assurer qu'on a au moins 5 profils
        if (count($results) < 5) {
            $existingIds = array_column($results, 'profile_id');
            $additionalProfiles = CareerProfile::active()
                ->whereNotIn('id', $existingIds)
                ->inRandomOrder()
                ->limit(5 - count($results))
                ->get();

            foreach ($additionalProfiles as $profile) {
                $results[] = [
                    'profile_id' => $profile->id,
                    'profile' => [
                        'id' => $profile->id,
                        'nom' => $profile->nom,
                        'slug' => $profile->slug,
                        'description' => $profile->description,
                        'secteur' => $profile->secteur,
                        'image_url' => $profile->image_url,
                        'debouches' => $profile->debouches,
                        'niveau_minimum' => $profile->niveau_minimum,
                    ],
                    'score' => rand(20, 50),
                ];
            }
        }

        return $results;
    }

    /**
     * Récupérer les résultats du formulaire
     */
    public function getResults(EliteUser $user): array
    {
        if (!$user->correspondence_completed) {
            throw ValidationException::withMessages([
                'correspondence' => ['Vous devez d\'abord compléter le formulaire de correspondance.']
            ]);
        }

        $results = UserProfileResult::where('user_id', $user->id)
            ->with('profile')
            ->orderBy('rang')
            ->get();

        return [
            'recommended_profiles' => $results->map(function ($result) {
                return [
                    'rang' => $result->rang,
                    'score' => $result->score,
                    'profile' => [
                        'id' => $result->profile->id,
                        'nom' => $result->profile->nom,
                        'slug' => $result->profile->slug,
                        'description' => $result->profile->description,
                        'secteur' => $result->profile->secteur,
                        'image_url' => $result->profile->image_url,
                        'debouches' => $result->profile->debouches,
                        'niveau_minimum' => $result->profile->niveau_minimum,
                    ],
                ];
            }),
            'all_profiles' => $this->getAllProfiles(),
        ];
    }

    /**
     * Récupérer tous les profils disponibles
     */
    public function getAllProfiles(): array
    {
        $profiles = CareerProfile::active()
            ->orderBy('secteur')
            ->orderBy('nom')
            ->get();

        return $profiles->groupBy('secteur')->map(function ($group, $secteur) {
            return [
                'secteur' => $secteur,
                'profiles' => $group->map(function ($profile) {
                    return [
                        'id' => $profile->id,
                        'nom' => $profile->nom,
                        'slug' => $profile->slug,
                        'description' => $profile->description,
                        'image_url' => $profile->image_url,
                        'niveau_minimum' => $profile->niveau_minimum,
                    ];
                })->values(),
            ];
        })->values()->toArray();
    }

    /**
     * Choisir un profil
     */
    public function chooseProfile(EliteUser $user, int $profileId, bool $fromRecommendations = true): array
    {
        if (!$user->correspondence_completed) {
            throw ValidationException::withMessages([
                'correspondence' => ['Vous devez d\'abord compléter le formulaire de correspondance.']
            ]);
        }

        $profile = CareerProfile::findOrFail($profileId);

        // Vérifier si le profil était dans les recommandations
        $wasRecommended = UserProfileResult::where('user_id', $user->id)
            ->where('profile_id', $profileId)
            ->exists();

        UserProfileChoice::updateOrCreate(
            ['user_id' => $user->id],
            [
                'profile_id' => $profileId,
                'from_recommendations' => $wasRecommended,
            ]
        );

        $user->update(['profile_chosen' => true]);

        return [
            'profile' => [
                'id' => $profile->id,
                'nom' => $profile->nom,
                'slug' => $profile->slug,
                'description' => $profile->description,
                'secteur' => $profile->secteur,
                'debouches' => $profile->debouches,
            ],
            'from_recommendations' => $wasRecommended,
        ];
    }

    /**
     * Choisir le mode de parcours
     */
    public function choosePathMode(EliteUser $user, string $mode): void
    {
        if (!in_array($mode, ['en_ligne', 'presentiel', 'externe'])) {
            throw ValidationException::withMessages([
                'mode' => ['Mode de parcours invalide.']
            ]);
        }

        $user->update(['parcours_mode' => $mode]);
    }
}
