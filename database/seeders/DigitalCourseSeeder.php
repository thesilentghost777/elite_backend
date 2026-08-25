<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\Pack;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DigitalCourseSeeder extends Seeder
{
    public function run(): void
    {
        $pack = Pack::where('slug', 'developpement-web-et-app')->firstOrFail();

        $modules = [
            [
                'nom' => 'Module 1 - Fondamentaux du développement web',
                'description' => 'Introduction au développement fullstack, aux API, JavaScript et Git.',
                'ordre' => 1,
                'quiz_file' => 'quiz/quiz_module_1.json',
                'lessons' => [
                    ['titre' => 'Introduction à la filière Digital', 'theory' => 'theorique/module1/lecon-01-introduction-filiere-digital.html', 'video' => 'explication/Module1/video.mp4', 'practice' => 'TP/Module1/LECON1_developpemnt fullstack.mp4'],
                    ['titre' => 'Bases du Web, HTTP, JSON et API REST', 'theory' => 'theorique/module1/lecon-02-bases-du-web-http-json-api-rest.html', 'video' => 'explication/Module1/video2.mp4', 'practice' => 'TP/Module1/LECON2_Introduction __ la cr__ation de Web Services (REST, API)2r.mp4'],
                    ['titre' => 'JavaScript moderne et TypeScript', 'theory' => 'theorique/module1/lecon-03-javascript-moderne-typescript.html', 'video' => 'explication/Module1/video3.mp4', 'practice' => 'TP/Module1/LECON3_developpemnt JS.mp4'],
                    ['titre' => 'Git, GitHub et méthodologie de projet', 'theory' => 'theorique/module1/lecon-04 -git- gitHub- méthodologie- projet.html', 'video' => 'explication/Module1/video4.mp4', 'practice' => 'TP/Module1/LECON4_ git et github bonne tp.mp4'],
                    ['titre' => 'Architecture logicielle et environnement de développement', 'theory' => 'theorique/module1/lecon-05-architecture-logicielle-environnement-dev.html', 'video' => 'explication/Module1/video5.mp4', 'practice' => 'TP/Module1/LECON5_ Larchitecture client-serveur (larchitecture 2 tiers) (Informatique de gestion)5.mp4'],
                ],
            ],
            [
                'nom' => 'Module 2 - Bases de données',
                'description' => 'Bases de données relationnelles, NoSQL et modélisation avancée.',
                'ordre' => 2,
                'quiz_file' => 'quiz/quiz_module_2.json',
                'lessons' => [
                    ['titre' => 'Introduction aux bases de données relationnelles', 'theory' => 'theorique/module2/lecon-06-Introduction-aux-bases-données-relationnelles .html', 'video' => 'explication/Module2/video1.mp4', 'practice' => 'TP/Module 2/LECON1_ SQL.mp4'],
                    ['titre' => 'NoSQL, MongoDB et Redis', 'theory' => 'theorique/module2/lecon-7-nosql-mongodb-redis.html', 'video' => 'explication/Module2/video2.mp4', 'practice' => 'TP/Module 2/LECON2_Base de donn__es Introduction au NoSQL2.mp4'],
                    ['titre' => 'Modélisation avancée et bonnes pratiques', 'theory' => 'theorique/module2/lecon-8-modelisation-avancee-bonnes-pratiques.html', 'video' => 'explication/Module2/video3.mp4', 'practice' => 'TP/Module 2/LECON3_Mod__lisation des donn__es - Normalisation et formes normales3.mp4'],
                ],
            ],
        ];

        DB::transaction(function () use ($pack, $modules): void {
            foreach ($modules as $moduleData) {
                $module = Module::updateOrCreate(
                    ['pack_id' => $pack->id, 'ordre' => $moduleData['ordre']],
                    ['nom' => $moduleData['nom'], 'description' => $moduleData['description'], 'type' => 'theorique', 'active' => true]
                );

                foreach ($moduleData['lessons'] as $lessonIndex => $lessonData) {
                    Lesson::updateOrCreate(
                        ['module_id' => $module->id, 'ordre' => $lessonIndex + 1],
                        [
                            'titre' => $lessonData['titre'],
                            'contenu_texte' => 'Cours théorique disponible via le lien HTML.',
                            'url_web' => asset('COURS/' . $lessonData['theory']),
                            'url_video' => asset('COURS/' . $lessonData['practice']),
                            'url_video_explication' => asset('COURS/' . $lessonData['video']),
                            'url_video_pratique' => asset('COURS/' . $lessonData['practice']),
                            'duree_minutes' => 15,
                            'active' => true,
                        ]
                    );
                }

                // Peupler le quiz si le fichier JSON existe
                if (!empty($moduleData['quiz_file'])) {
                    $quizFilePath = public_path('COURS/' . $moduleData['quiz_file']);
                    if (File::exists($quizFilePath)) {
                        $jsonContent = File::get($quizFilePath);
                        $quizData = json_decode($jsonContent, true);

                        if ($quizData && !empty($quizData['questions'])) {
                            $quizTitle = $quizData['module']['description'] ?? ($quizData['module']['title'] ?? ('Quiz - ' . $module->nom));
                            $quizDesc = $quizData['module']['title'] ?? 'Quiz d\'évaluation 10 questions';

                            $quiz = Quiz::updateOrCreate(
                                ['module_id' => $module->id],
                                [
                                    'titre' => $quizTitle,
                                    'description' => $quizDesc,
                                    'note_totale' => 20,
                                    'duree_minutes' => 15,
                                    'ordre' => 1,
                                    'active' => true,
                                ]
                            );

                            // Nettoyer les questions existantes pour éviter les doublons
                            $quiz->questions()->delete();

                            foreach ($quizData['questions'] as $qIndex => $qItem) {
                                $question = $quiz->questions()->create([
                                    'enonce' => $qItem['question'],
                                    'type' => 'qcm',
                                    'explication' => $qItem['explanation'] ?? null,
                                    'points' => 2,
                                    'ordre' => $qIndex + 1,
                                    'active' => true,
                                ]);

                                if (!empty($qItem['answers'])) {
                                    foreach ($qItem['answers'] as $aIndex => $ans) {
                                        $question->answers()->create([
                                            'texte' => $ans['text'],
                                            'est_correcte' => !empty($ans['correct']),
                                            'ordre' => $aIndex + 1,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });

        $this->command?->info('Modules Digital 1 et 2, leurs leçons et leurs quiz configurés avec succès.');
    }
}