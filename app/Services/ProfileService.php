<?php

namespace App\Services;

use App\Models\CareerProfile;
use App\Models\EliteUser;
use App\Models\Roadmap;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    /**
     * Récupérer tous les profils
     */
    public function getAllProfiles(array $filters = []): array
    {
        $query = CareerProfile::active();

        if (isset($filters['secteur'])) {
            $query->where('secteur', $filters['secteur']);
        }

        if (isset($filters['is_cfpam'])) {
            $query->where('is_cfpam', $filters['is_cfpam']);
        }

        if (isset($filters['niveau_minimum'])) {
            $query->where('niveau_minimum', $filters['niveau_minimum']);
        }

        $profiles = $query->orderBy('secteur')->orderBy('nom')->get();

        return $profiles->map(function ($profile) {
            return [
                'id' => $profile->id,
                'nom' => $profile->nom,
                'slug' => $profile->slug,
                'description' => $profile->description,
                'secteur' => $profile->secteur,
                'image_url' => $profile->image_url,
                'debouches' => $profile->debouches,
                'niveau_minimum' => $profile->niveau_minimum,
                'is_cfpam' => $profile->is_cfpam,
            ];
        })->toArray();
    }

    /**
     * Récupérer les secteurs disponibles
     */
    public function getSecteurs(): array
    {
        return CareerProfile::active()
            ->distinct()
            ->pluck('secteur')
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Récupérer un profil par ID
     */
    public function getProfile(int $id): array
    {
        $profile = CareerProfile::with('packs')->findOrFail($id);

        return [
            'id' => $profile->id,
            'nom' => $profile->nom,
            'slug' => $profile->slug,
            'description' => $profile->description,
            'secteur' => $profile->secteur,
            'image_url' => $profile->image_url,
            'debouches' => $profile->debouches,
            'niveau_minimum' => $profile->niveau_minimum,
            'is_cfpam' => $profile->is_cfpam,
            'packs_recommandes' => $profile->packs->map(function ($pack) {
                return [
                    'id' => $pack->id,
                    'nom' => $pack->nom,
                    'slug' => $pack->slug,
                    'description' => $pack->description,
                    'prix_points' => $pack->prix_points,
                    'niveau_requis' => $pack->niveau_requis,
                    'priorite' => $pack->pivot->priorite,
                ];
            })->sortByDesc('priorite')->values(),
        ];
    }

    /**
     * Récupérer la roadmap d'un profil pour un niveau donné
     */
    public function getRoadmap(int $profileId, string $niveau): array
    {
        $profile = CareerProfile::findOrFail($profileId);
        
        $roadmap = Roadmap::where('profile_id', $profileId)
            ->where('niveau_depart', $niveau)
            ->with(['steps' => function ($query) {
                $query->orderBy('ordre')->with('packRecommande');
            }])
            ->first();

        if (!$roadmap) {
            // Chercher une roadmap pour un niveau inférieur
            $niveaux = ['BEPC', 'Probatoire', 'BAC', 'Licence', 'Master'];
            $currentIndex = array_search($niveau, $niveaux);
            
            for ($i = $currentIndex; $i >= 0; $i--) {
                $roadmap = Roadmap::where('profile_id', $profileId)
                    ->where('niveau_depart', $niveaux[$i])
                    ->with(['steps' => function ($query) {
                        $query->orderBy('ordre')->with('packRecommande');
                    }])
                    ->first();
                    
                if ($roadmap) break;
            }
        }

        if (!$roadmap) {
            throw ValidationException::withMessages([
                'roadmap' => ['Aucune roadmap disponible pour ce profil et ce niveau.']
            ]);
        }

        return [
            'profile' => [
                'id' => $profile->id,
                'nom' => $profile->nom,
                'secteur' => $profile->secteur,
            ],
            'roadmap' => [
                'id' => $roadmap->id,
                'titre' => $roadmap->titre,
                'description' => $roadmap->description,
                'niveau_depart' => $roadmap->niveau_depart,
                'duree_estimee_mois' => $roadmap->duree_estimee_mois,
                'steps' => $roadmap->steps->map(function ($step) {
                    return [
                        'id' => $step->id,
                        'ordre' => $step->ordre,
                        'titre' => $step->titre,
                        'description' => $step->description,
                        'type' => $step->type,
                        'duree_semaines' => $step->duree_semaines,
                        'obligatoire' => $step->obligatoire,
                        'pack_recommande' => $step->packRecommande ? [
                            'id' => $step->packRecommande->id,
                            'nom' => $step->packRecommande->nom,
                            'prix_points' => $step->packRecommande->prix_points,
                        ] : null,
                    ];
                }),
            ],
        ];
    }

    /**
     * Récupérer la roadmap pour l'utilisateur connecté
     */
    public function getUserRoadmap(EliteUser $user): array
    {
        if (!$user->profile_chosen) {
            throw ValidationException::withMessages([
                'profile' => ['Vous devez d\'abord choisir un profil.']
            ]);
        }

        $choice = $user->profileChoice()->with('profile')->first();
        
        return $this->getRoadmap($choice->profile_id, $user->dernier_diplome);
    }
}
