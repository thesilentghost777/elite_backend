<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Pack;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ModuleQuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizzesData = [
            1 => [
                'json_file' => 'quiz/quiz_module_1.json',
                'module_ordre' => 1,
                'module_name_fallback' => 'Module 1 - Fondamentaux du développement web',
                'titre' => 'Quiz - Fondamentaux Web & Outils',
                'description' => 'Quiz de validation du module 01 (10 questions).',
                'note_totale' => 20,
                'duree_minutes' => 15,
                'questions' => [
                    [
                        'question' => "Quel outil est principalement utilisé pour suivre les versions d'un projet et enregistrer les modifications apportées au code ?",
                        'explanation' => "Git est un système de gestion de versions permettant de suivre les modifications du code.",
                        'answers' => [
                            ['text' => 'Git', 'correct' => true],
                            ['text' => 'VS Code', 'correct' => false],
                            ['text' => 'Trello', 'correct' => false],
                            ['text' => 'Node.js', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Dans une architecture client/serveur, que fait généralement le client lorsqu'il souhaite obtenir une ressource depuis un serveur ?",
                        'explanation' => "Le client envoie une requête au serveur, qui traite la demande et renvoie une réponse.",
                        'answers' => [
                            ['text' => 'Il envoie une requête au serveur', 'correct' => true],
                            ['text' => 'Il supprime automatiquement la base de données', 'correct' => false],
                            ['text' => 'Il transforme le serveur en navigateur', 'correct' => false],
                            ['text' => 'Il crée systématiquement une nouvelle API', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Quelle méthode HTTP est normalement utilisée pour récupérer une ressource depuis une API ?",
                        'explanation' => "GET permet de demander ou récupérer une ressource.",
                        'answers' => [
                            ['text' => 'POST', 'correct' => false],
                            ['text' => 'GET', 'correct' => true],
                            ['text' => 'DELETE', 'correct' => false],
                            ['text' => 'PATCH', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Quelle affirmation décrit correctement JSON dans le contexte des API Web ?",
                        'explanation' => "JSON est un format structuré couramment utilisé pour échanger des données entre applications.",
                        'answers' => [
                            ['text' => "C'est un format léger d'échange de données structuré", 'correct' => true],
                            ['text' => "C'est un système de gestion de versions", 'correct' => false],
                            ['text' => "C'est un gestionnaire de paquets JavaScript", 'correct' => false],
                            ['text' => "C'est un protocole de transport réseau", 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Quelle combinaison associe correctement une opération Git et son rôle ?",
                        'explanation' => "Un commit enregistre un état des modifications dans l'historique Git.",
                        'answers' => [
                            ['text' => 'commit : enregistrer un état des modifications', 'correct' => true],
                            ['text' => 'branch : supprimer définitivement Git', 'correct' => false],
                            ['text' => 'merge : installer Node.js', 'correct' => false],
                            ['text' => 'pull request : compiler automatiquement TypeScript', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Quel mécanisme de JavaScript moderne permet d'écrire plus clairement du code qui attend le résultat d'une promesse ?",
                        'explanation' => "async/await facilite l'écriture et la lecture du code asynchrone basé sur les promesses.",
                        'answers' => [
                            ['text' => 'async/await', 'correct' => true],
                            ['text' => 'destructuring', 'correct' => false],
                            ['text' => 'interface', 'correct' => false],
                            ['text' => 'generic', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Une équipe veut modifier une partie d'un projet sans travailler directement sur la version principale. Quelle pratique Git est la plus adaptée ?",
                        'explanation' => "Une branche permet de travailler isolément sur une évolution avant de l'intégrer au projet.",
                        'answers' => [
                            ['text' => 'Créer une branche', 'correct' => true],
                            ['text' => 'Supprimer le dépôt distant', 'correct' => false],
                            ['text' => 'Remplacer tous les commits', 'correct' => false],
                            ['text' => "Modifier directement l'historique principal", 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Une API reçoit une demande de modification partielle d'une ressource existante. Quelle méthode HTTP est la plus appropriée ?",
                        'explanation' => "PATCH est utilisée pour appliquer une modification partielle à une ressource.",
                        'answers' => [
                            ['text' => 'GET', 'correct' => false],
                            ['text' => 'POST', 'correct' => false],
                            ['text' => 'PATCH', 'correct' => true],
                            ['text' => 'DELETE', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Pourquoi les variables d'environnement sont-elles utiles dans un environnement de développement ?",
                        'explanation' => "Les variables d'environnement permettent notamment de gérer des paramètres de configuration séparément du code.",
                        'answers' => [
                            ['text' => 'Elles permettent notamment de séparer certaines configurations du code source', 'correct' => true],
                            ['text' => 'Elles remplacent toujours Git', 'correct' => false],
                            ['text' => 'Elles rendent toutes les API publiques', 'correct' => false],
                            ['text' => "Elles empêchent l'utilisation de Node.js", 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Un développeur doit définir une structure de données attendue par plusieurs fonctions TypeScript tout en bénéficiant d'un typage explicite. Quelle notion répond le mieux à ce besoin ?",
                        'explanation' => "Une interface TypeScript décrit explicitement la structure attendue d'un objet.",
                        'answers' => [
                            ['text' => 'Une interface TypeScript', 'correct' => true],
                            ['text' => 'Une requête GET', 'correct' => false],
                            ['text' => 'Une branche Git', 'correct' => false],
                            ['text' => 'Un fichier JSON uniquement', 'correct' => false],
                        ],
                    ],
                ],
            ],
            2 => [
                'json_file' => 'quiz/quiz_module_2.json',
                'module_ordre' => 2,
                'module_name_fallback' => 'Module 2 - Bases de données',
                'titre' => 'Quiz - Bases de Données',
                'description' => 'Quiz de validation du module 02 (10 questions).',
                'note_totale' => 20,
                'duree_minutes' => 15,
                'questions' => [
                    [
                        'question' => "Dans une base de données relationnelle, quel élément permet d'identifier de manière unique un enregistrement dans une table ?",
                        'explanation' => "La clé primaire identifie de manière unique chaque enregistrement d'une table.",
                        'answers' => [
                            ['text' => 'Une clé primaire', 'correct' => true],
                            ['text' => 'Une clé étrangère', 'correct' => false],
                            ['text' => 'Un index de cache', 'correct' => false],
                            ['text' => 'Une collection', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Quelle commande SQL permet principalement de récupérer des données depuis une table ?",
                        'explanation' => "SELECT permet de récupérer des données dans une base de données SQL.",
                        'answers' => [
                            ['text' => 'SELECT', 'correct' => true],
                            ['text' => 'DELETE', 'correct' => false],
                            ['text' => 'DROP', 'correct' => false],
                            ['text' => 'UPDATE', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Quelle caractéristique distingue principalement une base de données NoSQL d'une base relationnelle SQL ?",
                        'explanation' => "Les bases NoSQL utilisent des modèles non relationnels, comme les documents dans MongoDB.",
                        'answers' => [
                            ['text' => 'Elle peut utiliser des modèles de données non relationnels', 'correct' => true],
                            ['text' => 'Elle ne peut jamais stocker de données', 'correct' => false],
                            ['text' => 'Elle utilise obligatoirement des tables relationnelles', 'correct' => false],
                            ['text' => 'Elle remplace toujours les systèmes de cache', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Quel est le rôle principal d'une clé étrangère dans une base de données relationnelle ?",
                        'explanation' => "Une clé étrangère permet notamment d'établir une relation entre des tables.",
                        'answers' => [
                            ['text' => 'Relier une table à une autre', 'correct' => true],
                            ['text' => 'Chiffrer automatiquement toutes les données', 'correct' => false],
                            ['text' => 'Supprimer les doublons dans toutes les tables', 'correct' => false],
                            ['text' => 'Remplacer les requêtes SELECT', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Quelle clause SQL permet de filtrer les lignes correspondant à une condition donnée ?",
                        'explanation' => "WHERE permet de filtrer les résultats d'une requête selon une condition.",
                        'answers' => [
                            ['text' => 'WHERE', 'correct' => true],
                            ['text' => 'GROUP BY', 'correct' => false],
                            ['text' => 'JOIN', 'correct' => false],
                            ['text' => 'ORDER TABLE', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Dans MongoDB, comment sont principalement organisées les données ?",
                        'explanation' => "MongoDB est une base documentaire dans laquelle les documents sont regroupés au sein de collections.",
                        'answers' => [
                            ['text' => 'En documents regroupés dans des collections', 'correct' => true],
                            ['text' => 'Uniquement en lignes et colonnes relationnelles', 'correct' => false],
                            ['text' => 'Uniquement en fichiers texte indépendants', 'correct' => false],
                            ['text' => 'En clés étrangères uniquement', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Pourquoi utilise-t-on principalement un index dans une base de données ?",
                        'explanation' => "Les index peuvent améliorer les performances des recherches et de certaines requêtes.",
                        'answers' => [
                            ['text' => 'Pour accélérer certaines recherches et requêtes', 'correct' => true],
                            ['text' => 'Pour remplacer complètement les sauvegardes', 'correct' => false],
                            ['text' => 'Pour transformer une base SQL en base NoSQL', 'correct' => false],
                            ['text' => 'Pour supprimer automatiquement toutes les données inutilisées', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Une application doit récupérer des informations provenant de deux tables relationnelles liées entre elles. Quelle notion SQL est particulièrement adaptée à ce besoin ?",
                        'explanation' => "JOIN permet de combiner des données provenant de plusieurs tables selon leurs relations.",
                        'answers' => [
                            ['text' => 'JOIN', 'correct' => true],
                            ['text' => 'CACHE', 'correct' => false],
                            ['text' => 'COLLECTION', 'correct' => false],
                            ['text' => 'DOCUMENT', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Une application doit conserver temporairement des sessions utilisateurs et gérer rapidement certaines données fréquemment utilisées. Quelle technologie présentée dans le module est particulièrement adaptée à ce rôle ?",
                        'explanation' => "Redis peut notamment être utilisé comme cache pour les sessions et certaines données fréquemment utilisées.",
                        'answers' => [
                            ['text' => 'Redis', 'correct' => true],
                            ['text' => 'PostgreSQL uniquement', 'correct' => false],
                            ['text' => 'Git', 'correct' => false],
                            ['text' => 'MongoDB uniquement', 'correct' => false],
                        ],
                    ],
                    [
                        'question' => "Une équipe doit choisir une base de données pour un nouveau projet. Selon le module, quel critère est le plus pertinent pour effectuer ce choix ?",
                        'explanation' => "Le choix entre relationnel, documentaire ou cache dépend des besoins et caractéristiques du projet.",
                        'answers' => [
                            ['text' => 'Les besoins et caractéristiques du projet', 'correct' => true],
                            ['text' => 'Utiliser systématiquement PostgreSQL quel que soit le projet', 'correct' => false],
                            ['text' => 'Utiliser systématiquement MongoDB pour tous les projets', 'correct' => false],
                            ['text' => 'Choisir uniquement la technologie la plus récente', 'correct' => false],
                        ],
                    ],
                ],
            ],
        ];

        DB::transaction(function () use ($quizzesData) {
            $pack = Pack::where('slug', 'developpement-web-et-app')->first();

            foreach ($quizzesData as $moduleNum => $config) {
                // Trouver le module correspondant
                $module = null;
                if ($pack) {
                    $module = Module::where('pack_id', $pack->id)
                        ->where(function ($q) use ($config, $moduleNum) {
                            $q->where('ordre', $config['module_ordre'])
                              ->orWhere('nom', 'like', "%Module {$moduleNum}%");
                        })
                        ->first();
                }

                if (!$module) {
                    $module = Module::where('nom', 'like', "%Module {$moduleNum}%")
                        ->orWhere('ordre', $config['module_ordre'])
                        ->first();
                }

                if (!$module) {
                    // Si le module n'existe pas encore, le créer
                    $module = Module::create([
                        'pack_id' => $pack ? $pack->id : null,
                        'nom' => $config['module_name_fallback'],
                        'description' => $config['description'],
                        'ordre' => $config['module_ordre'],
                        'type' => 'theorique',
                        'note_passage' => 14,
                        'note_parrainage' => 10,
                        'parrainages_requis' => 4,
                        'active' => true,
                    ]);
                }

                // Vérifier si un fichier JSON externe existe avec les données
                $questionsToInsert = $config['questions'];
                $quizTitle = $config['titre'];
                $quizDescription = $config['description'];

                if (!empty($config['json_file'])) {
                    $jsonPath = public_path('COURS/' . $config['json_file']);
                    if (File::exists($jsonPath)) {
                        $content = json_decode(File::get($jsonPath), true);
                        if ($content && !empty($content['questions'])) {
                            $quizTitle = $content['module']['title'] ?? $quizTitle;
                            $quizDescription = $content['module']['description'] ?? $quizDescription;
                            $questionsToInsert = array_map(function ($q) {
                                return [
                                    'question' => $q['question'],
                                    'explanation' => $q['explanation'] ?? null,
                                    'answers' => array_map(function ($a) {
                                        return [
                                            'text' => $a['text'],
                                            'correct' => !empty($a['correct']),
                                        ];
                                    }, $q['answers'] ?? []),
                                ];
                            }, $content['questions']);
                        }
                    }
                }

                // Créer ou mettre à jour le quiz
                $quiz = Quiz::updateOrCreate(
                    ['module_id' => $module->id],
                    [
                        'titre' => $quizTitle,
                        'description' => $quizDescription,
                        'note_totale' => $config['note_totale'],
                        'duree_minutes' => $config['duree_minutes'],
                        'ordre' => 1,
                        'active' => true,
                    ]
                );

                // Supprimer les questions existantes pour réinjecter proprement sans doublons
                $existingQuestionIds = $quiz->questions()->pluck('id');
                if ($existingQuestionIds->isNotEmpty()) {
                    QuizAnswer::whereIn('question_id', $existingQuestionIds)->delete();
                    $quiz->questions()->delete();
                }

                // Insérer les 10 questions et leurs réponses
                $pointsPerQuestion = (int) round($config['note_totale'] / max(1, count($questionsToInsert)));
                if ($pointsPerQuestion === 0) {
                    $pointsPerQuestion = 2;
                }

                foreach ($questionsToInsert as $qIndex => $qData) {
                    $question = $quiz->questions()->create([
                        'enonce' => $qData['question'],
                        'type' => 'qcm',
                        'explication' => $qData['explanation'] ?? null,
                        'points' => $pointsPerQuestion,
                        'ordre' => $qIndex + 1,
                        'active' => true,
                    ]);

                    if (!empty($qData['answers'])) {
                        foreach ($qData['answers'] as $aIndex => $ans) {
                            $question->answers()->create([
                                'texte' => $ans['text'],
                                'est_correcte' => !empty($ans['correct']),
                                'ordre' => $aIndex + 1,
                            ]);
                        }
                    }
                }

                $this->command?->info("✓ Quiz pour le Module {$moduleNum} ({$module->nom}) injecté avec succès (" . count($questionsToInsert) . " questions).");
            }
        });
    }
}
