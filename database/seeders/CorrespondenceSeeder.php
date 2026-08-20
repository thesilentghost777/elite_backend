<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CorrespondenceSeeder extends Seeder
{
    /**
     * Seed correspondence questions and answers.
     */
    public function run(): void
    {
        // D'abord, créons les catégories
        $categories = [
            ['nom' => 'professionnel', 'description' => 'Questions sur le milieu professionnel', 'ordre' => 1],
            ['nom' => 'personnel', 'description' => 'Questions personnelles', 'ordre' => 2],
            ['nom' => 'social', 'description' => 'Questions sociales', 'ordre' => 3],
            ['nom' => 'moral', 'description' => 'Questions morales et valeurs', 'ordre' => 4],
            ['nom' => 'aptitude', 'description' => 'Questions sur les aptitudes', 'ordre' => 5],
            ['nom' => 'aspiration', 'description' => 'Questions sur les aspirations', 'ordre' => 6],
        ];

        // Insérer les catégories et garder leurs IDs
        $categoryIds = [];
        foreach ($categories as $category) {
            $id = DB::table('correspondence_categories')->insertGetId([
                'nom' => $category['nom'],
                'description' => $category['description'],
                'ordre' => $category['ordre'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $categoryIds[$category['nom']] = $id;
        }

        // Questions de correspondance
        $questions = [
            // QUESTIONS PROFESSIONNELLES
            [
                'category' => 'professionnel',
                'question' => 'Quel type d\'environnement de travail préférez-vous ?',
                'type' => 'qcm',
                'ordre' => 1,
                'answers' => [
                    ['text' => 'Bureau calme et organisé'],
                    ['text' => 'Terrain et déplacements fréquents'],
                    ['text' => 'Studio créatif ou artistique'],
                    ['text' => 'Laboratoire technique'],
                    ['text' => 'Contact direct avec le public'],
                ]
            ],
            [
                'category' => 'professionnel',
                'question' => 'Quelle activité vous passionne le plus ?',
                'type' => 'qcm',
                'ordre' => 2,
                'answers' => [
                    ['text' => 'Créer des visuels et des designs'],
                    ['text' => 'Résoudre des problèmes techniques'],
                    ['text' => 'Gérer des chiffres et des finances'],
                    ['text' => 'Communiquer et convaincre'],
                    ['text' => 'Aider et soigner les autres'],
                ]
            ],
            [
                'category' => 'professionnel',
                'question' => 'Préférez-vous travailler seul ou en équipe ?',
                'type' => 'qcm',
                'ordre' => 3,
                'answers' => [
                    ['text' => 'Principalement seul, en autonomie'],
                    ['text' => 'En petite équipe soudée'],
                    ['text' => 'En grande équipe avec hiérarchie'],
                    ['text' => 'Peu importe, je m\'adapte'],
                ]
            ],
            [
                'category' => 'professionnel',
                'question' => 'Quel est votre rapport avec la technologie ?',
                'type' => 'qcm',
                'ordre' => 4,
                'answers' => [
                    ['text' => 'Je suis passionné(e) par la tech'],
                    ['text' => 'J\'utilise la tech comme outil de travail'],
                    ['text' => 'Je préfère le travail manuel'],
                    ['text' => 'Je préfère le contact humain direct'],
                ]
            ],
            [
                'category' => 'professionnel',
                'question' => 'Quel niveau de responsabilité recherchez-vous ?',
                'type' => 'qcm',
                'ordre' => 5,
                'answers' => [
                    ['text' => 'Diriger une équipe ou un projet'],
                    ['text' => 'Être expert dans mon domaine'],
                    ['text' => 'Travailler de manière indépendante'],
                    ['text' => 'Évoluer progressivement'],
                ]
            ],

            // QUESTIONS PERSONNELLES
            [
                'category' => 'personnel',
                'question' => 'Comment gérez-vous le stress ?',
                'type' => 'qcm',
                'ordre' => 6,
                'answers' => [
                    ['text' => 'Je reste calme et méthodique'],
                    ['text' => 'Le stress me motive'],
                    ['text' => 'Je préfère éviter les situations stressantes'],
                    ['text' => 'Je canalise le stress par la créativité'],
                ]
            ],
            [
                'category' => 'personnel',
                'question' => 'Quel est votre rythme de travail idéal ?',
                'type' => 'qcm',
                'ordre' => 7,
                'answers' => [
                    ['text' => 'Horaires fixes et réguliers'],
                    ['text' => 'Flexible selon les projets'],
                    ['text' => 'Travail de nuit possible'],
                    ['text' => 'Je suis mon propre patron'],
                ]
            ],
            [
                'category' => 'personnel',
                'question' => 'Quelle est votre principale force ?',
                'type' => 'qcm',
                'ordre' => 8,
                'answers' => [
                    ['text' => 'Ma créativité'],
                    ['text' => 'Ma rigueur et précision'],
                    ['text' => 'Ma capacité à communiquer'],
                    ['text' => 'Ma force physique et endurance'],
                    ['text' => 'Mon empathie'],
                ]
            ],

            // QUESTIONS SOCIALES
            [
                'category' => 'social',
                'question' => 'Comment préférez-vous interagir avec les autres ?',
                'type' => 'qcm',
                'ordre' => 9,
                'answers' => [
                    ['text' => 'En face à face, individuellement'],
                    ['text' => 'Devant un groupe ou public'],
                    ['text' => 'Par écrit ou à distance'],
                    ['text' => 'Peu d\'interaction, travail solitaire'],
                ]
            ],
            [
                'category' => 'social',
                'question' => 'Êtes-vous à l\'aise pour parler en public ?',
                'type' => 'oui_non',
                'ordre' => 10,
                'answers' => [
                    ['text' => 'Oui'],
                    ['text' => 'Non'],
                ]
            ],
            [
                'category' => 'social',
                'question' => 'Aimez-vous aider les autres à résoudre leurs problèmes ?',
                'type' => 'oui_non',
                'ordre' => 11,
                'answers' => [
                    ['text' => 'Oui, beaucoup'],
                    ['text' => 'Parfois'],
                    ['text' => 'Non, je préfère mes propres défis'],
                ]
            ],

            // QUESTIONS MORALES/VALEURS
            [
                'category' => 'moral',
                'question' => 'Fumez-vous ?',
                'type' => 'oui_non',
                'ordre' => 12,
                'answers' => [
                    ['text' => 'Non, jamais'],
                    ['text' => 'Occasionnellement'],
                    ['text' => 'Oui, régulièrement'],
                ]
            ],
            [
                'category' => 'moral',
                'question' => 'Consommez-vous de l\'alcool ?',
                'type' => 'qcm',
                'ordre' => 13,
                'answers' => [
                    ['text' => 'Jamais'],
                    ['text' => 'Rarement, en occasion'],
                    ['text' => 'Régulièrement mais modérément'],
                    ['text' => 'Fréquemment'],
                ]
            ],
            [
                'category' => 'moral',
                'question' => 'Quelle valeur est la plus importante pour vous ?',
                'type' => 'qcm',
                'ordre' => 14,
                'answers' => [
                    ['text' => 'L\'honnêteté et l\'intégrité'],
                    ['text' => 'La créativité et l\'innovation'],
                    ['text' => 'Le service aux autres'],
                    ['text' => 'La réussite financière'],
                    ['text' => 'La liberté et l\'indépendance'],
                ]
            ],
            [
                'category' => 'moral',
                'question' => 'Êtes-vous prêt(e) à travailler les weekends si nécessaire ?',
                'type' => 'oui_non',
                'ordre' => 15,
                'answers' => [
                    ['text' => 'Oui, sans problème'],
                    ['text' => 'Occasionnellement'],
                    ['text' => 'Non, le weekend est sacré'],
                ]
            ],

            // QUESTIONS APTITUDES
            [
                'category' => 'aptitude',
                'question' => 'Êtes-vous à l\'aise avec les mathématiques ?',
                'type' => 'echelle',
                'ordre' => 16,
                'answers' => [
                    ['text' => 'Très à l\'aise (8-10)'],
                    ['text' => 'Assez à l\'aise (5-7)'],
                    ['text' => 'Peu à l\'aise (1-4)'],
                ]
            ],
            [
                'category' => 'aptitude',
                'question' => 'Avez-vous un bon sens artistique ?',
                'type' => 'echelle',
                'ordre' => 17,
                'answers' => [
                    ['text' => 'Très développé (8-10)'],
                    ['text' => 'Moyen (5-7)'],
                    ['text' => 'Peu développé (1-4)'],
                ]
            ],
            [
                'category' => 'aptitude',
                'question' => 'Comment évaluez-vous votre niveau en langues étrangères ?',
                'type' => 'qcm',
                'ordre' => 18,
                'answers' => [
                    ['text' => 'Excellent (bilingue ou plus)'],
                    ['text' => 'Bon niveau'],
                    ['text' => 'Niveau moyen'],
                    ['text' => 'Niveau faible'],
                ]
            ],

            // QUESTIONS ASPIRATIONS
            [
                'category' => 'aspiration',
                'question' => 'Où vous voyez-vous dans 5 ans ?',
                'type' => 'qcm',
                'ordre' => 19,
                'answers' => [
                    ['text' => 'Chef de ma propre entreprise'],
                    ['text' => 'Manager dans une grande entreprise'],
                    ['text' => 'Expert reconnu dans mon domaine'],
                    ['text' => 'Travailleur indépendant épanoui'],
                    ['text' => 'Au service de la communauté'],
                ]
            ],
            [
                'category' => 'aspiration',
                'question' => 'Quel salaire minimum attendez-vous en début de carrière ?',
                'type' => 'qcm',
                'ordre' => 20,
                'answers' => [
                    ['text' => 'Moins de 100 000 FCFA'],
                    ['text' => '100 000 - 200 000 FCFA'],
                    ['text' => '200 000 - 400 000 FCFA'],
                    ['text' => 'Plus de 400 000 FCFA'],
                ]
            ],
        ];

        foreach ($questions as $q) {
            // Utilisez l'ID de la catégorie au lieu du nom
            $questionId = DB::table('correspondence_questions')->insertGetId([
                'category_id' => $categoryIds[$q['category']],
                'question' => $q['question'],
                'type' => $q['type'],
                'ordre' => $q['ordre'],
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($q['answers'] as $index => $answer) {
                DB::table('correspondence_answers')->insert([
                    'question_id' => $questionId,
                    'texte' => $answer['text'], // CHANGÉ : 'texte' au lieu de 'answer_text'
                    'ordre' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}