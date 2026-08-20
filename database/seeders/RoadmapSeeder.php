<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoadmapSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            1 => 'Secrétaire Bureautique',
            2 => 'Assistant(e) de Direction',
            3 => 'Secrétaire Comptable',
            4 => 'Infographe',
            5 => 'Monteur Vidéo',
            6 => 'Graphiste 3D',
            7 => 'Esthéticien(ne)',
            8 => 'Décorateur(trice)',
            9 => 'Spécialiste Marketing Digital',
            10 => 'Développeur Web et Mobile',
            11 => 'Chef de Projet',
            12 => 'Responsable RH',
            13 => 'Comptable',
            14 => 'Banquier',
            15 => 'Agent en Douane et Transit',
            16 => 'Logisticien',
            17 => 'Technicien Maintenance Informatique',
            18 => 'Administrateur Réseau',
            19 => 'Avocat',
            20 => 'Médecin',
            21 => 'Infirmier(ère)',
            22 => 'Enseignant',
            23 => 'Journaliste',
            24 => 'Footballeur Professionnel',
            25 => 'Musicien',
            26 => 'Chauffeur Professionnel',
            27 => 'Ingénieur Génie Civil',
            28 => 'Pharmacien',
            29 => 'Cuisinier/Chef',
            30 => 'Policier/Gendarme',
            31 => 'Entrepreneur',
            32 => 'Agronome',
            33 => 'Architecte',
        ];

        $levelOrder = [
            'BEPC' => 1,
            'Probatoire' => 2,
            'BAC' => 3,
            'Licence' => 4,
            'Master' => 5,
        ];

        $diplomaSteps = [
            'Probatoire' => [
                'titre' => 'Réussite à l\'examen du Probatoire',
                'description' => 'Inscrivez-vous en lycée général ou technique au Cameroun, étudiez en Seconde et Première, et passez l\'examen du Probatoire.',
                'duree_semaines' => 104, // Environ 2 ans
                'type' => 'diplome',
            ],
            'BAC' => [
                'titre' => 'Réussite à l\'examen du Baccalauréat',
                'description' => 'Étudiez en Terminale au lycée, préparez et passez l\'examen du Baccalauréat (général ou technique) au Cameroun.',
                'duree_semaines' => 52, // Environ 1 an
                'type' => 'diplome',
            ],
            'Licence' => [
                'titre' => 'Obtention de la Licence',
                'description' => 'Inscrivez-vous dans une université camerounaise (comme l\'Université de Yaoundé I ou II), étudiez pendant 3 ans et obtenez la Licence.',
                'duree_semaines' => 156, // Environ 3 ans
                'type' => 'diplome',
            ],
            'Master' => [
                'titre' => 'Obtention du Master',
                'description' => 'Poursuivez vos études universitaires au Cameroun pour obtenir le Master (2 ans supplémentaires).',
                'duree_semaines' => 104, // Environ 2 ans
                'type' => 'diplome',
            ],
        ];

        $nextLevel = [
            'BEPC' => 'Probatoire',
            'Probatoire' => 'BAC',
            'BAC' => 'Licence',
            'Licence' => 'Master',
            'Master' => null,
        ];

        $profileConfigs = [];

        // Configurations pour les profils vocationnels de niveau bas (priorité CFPAM)
        $vocLow = [1, 3, 4, 5, 6, 7, 8, 26, 29];
        foreach ($vocLow as $id) {
            $profileConfigs[$id] = [
                'min_level' => 'BEPC',
                'is_cfpam' => true,
                'base_steps' => [
                    [
                        'type' => 'formation',
                        'titre' => 'Formation professionnelle au CFPAM',
                        'description' => 'Inscrivez-vous au pack de formation adapté pour devenir ' . $profiles[$id] . ' au groupe CFPAM au Cameroun, qui propose des formations pratiques et certifiantes adaptées au marché local.',
                        'duree_semaines' => 52,
                        'obligatoire' => true,
                    ],
                    [
                        'type' => 'stage',
                        'titre' => 'Stage professionnel',
                        'description' => 'Effectuez un stage en entreprise au Cameroun pour appliquer les compétences apprises.',
                        'duree_semaines' => 12,
                        'obligatoire' => true,
                    ],
                    [
                        'type' => 'certification',
                        'titre' => 'Obtention de la certification',
                        'description' => 'Passez l\'examen de certification professionnelle (comme AQP ou CQP) au Cameroun.',
                        'duree_semaines' => 4,
                        'obligatoire' => true,
                    ],
                ],
                'duree_base_mois' => 12,
            ];
        }

        // Configurations pour les profils vocationnels de niveau moyen (priorité CFPAM)
        $vocMid = [2, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18];
        foreach ($vocMid as $id) {
            $profileConfigs[$id] = [
                'min_level' => 'BAC',
                'is_cfpam' => true,
                'base_steps' => [
                    [
                        'type' => 'formation',
                        'titre' => 'Formation professionnelle au CFPAM',
                        'description' => 'Inscrivez-vous au pack de formation adapté pour devenir ' . $profiles[$id] . ' au groupe CFPAM au Cameroun, avec un focus sur les compétences avancées et le marché local.',
                        'duree_semaines' => 78,
                        'obligatoire' => true,
                    ],
                    [
                        'type' => 'stage',
                        'titre' => 'Stage professionnel',
                        'description' => 'Effectuez un stage en entreprise ou organisation au Cameroun pour gagner en expérience.',
                        'duree_semaines' => 12,
                        'obligatoire' => true,
                    ],
                    [
                        'type' => 'certification',
                        'titre' => 'Obtention de la certification',
                        'description' => 'Passez l\'examen de certification (comme DQP ou BTS) reconnu au Cameroun.',
                        'duree_semaines' => 4,
                        'obligatoire' => true,
                    ],
                ],
                'duree_base_mois' => 18,
            ];
        }

        // Configurations personnalisées pour les autres profils
        $profileConfigs[19] = [
            'min_level' => 'BAC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Études en droit',
                    'description' => 'Inscrivez-vous en faculté de sciences juridiques et politiques (comme à l\'Université de Yaoundé II-Soa), obtenez la Licence (3 ans) puis le Master (2 ans) en droit.',
                    'duree_semaines' => 260,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'certification',
                    'titre' => 'Concours du Barreau',
                    'description' => 'Préparez et passez le concours d\'entrée au Barreau du Cameroun.',
                    'duree_semaines' => 52,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'stage',
                    'titre' => 'Stage au Barreau',
                    'description' => 'Effectuez le stage obligatoire de 2 ans dans un cabinet d\'avocats au Cameroun.',
                    'duree_semaines' => 104,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 84,
        ];

        $profileConfigs[20] = [
            'min_level' => 'BAC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Études en médecine',
                    'description' => 'Inscrivez-vous en faculté de médecine (comme à l\'Université de Yaoundé I ou Douala) via concours, étudiez 7 ans pour le Doctorat en Médecine Générale.',
                    'duree_semaines' => 364,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'stage',
                    'titre' => 'Internat et spécialisation optionnelle',
                    'description' => 'Effectuez l\'internat obligatoire, puis une spécialisation si désiré (3-5 ans supplémentaires).',
                    'duree_semaines' => 156,
                    'obligatoire' => false,
                ],
            ],
            'duree_base_mois' => 84,
        ];

        $profileConfigs[21] = [
            'min_level' => 'BAC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Formation en soins infirmiers',
                    'description' => 'Passez le concours d\'entrée dans une école d\'infirmiers (comme les écoles publiques ou privées au Cameroun), formez-vous pendant 3 ans pour le diplôme d\'État.',
                    'duree_semaines' => 156,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'certification',
                    'titre' => 'Obtention du diplôme d\'infirmier',
                    'description' => 'Passez l\'examen d\'État pour être infirmier diplômé d\'État au Cameroun.',
                    'duree_semaines' => 4,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 36,
        ];

        $profileConfigs[22] = [
            'min_level' => 'BAC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Formation à l\'École Normale Supérieure',
                    'description' => 'Passez le concours de l\'ENS (Yaoundé ou Bambili), formez-vous 3 ans pour DIPES I ou 5 ans pour DIPES II.',
                    'duree_semaines' => 156,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'experience',
                    'titre' => 'Enseignement pratique',
                    'description' => 'Intégrez le système éducatif camerounais et gagnez de l\'expérience en classe.',
                    'duree_semaines' => 52,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 36,
        ];

        $profileConfigs[23] = [
            'min_level' => 'BAC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Formation en journalisme',
                    'description' => 'Passez le concours de l\'ESSTIC (Yaoundé), obtenez la Licence en journalisme (3 ans).',
                    'duree_semaines' => 156,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'stage',
                    'titre' => 'Stage en médias',
                    'description' => 'Effectuez des stages dans des médias camerounais (CRT, presse, radio).',
                    'duree_semaines' => 12,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 36,
        ];

        $profileConfigs[24] = [
            'min_level' => 'BEPC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Entraînement dans un centre de formation football',
                    'description' => 'Rejoignez un centre comme l\'Académie des Brasseries ou un club local au Cameroun, entraînez-vous intensivement.',
                    'duree_semaines' => 104,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'experience',
                    'titre' => 'Participation à des compétitions',
                    'description' => 'Jouez en ligues amateurs puis élite au Cameroun, visez la MTN Elite One.',
                    'duree_semaines' => 156,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'projet',
                    'titre' => 'Signature de contrat pro',
                    'description' => 'Signez un contrat avec un club professionnel au Cameroun ou à l\'international.',
                    'duree_semaines' => 0,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 36,
        ];

        $profileConfigs[25] = [
            'min_level' => 'BEPC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Formation en musique',
                    'description' => 'Inscrivez-vous dans une école de musique au Cameroun ou apprenez via des maîtres, pratiquez un instrument ou le chant.',
                    'duree_semaines' => 52,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'projet',
                    'titre' => 'Production musicale',
                    'description' => 'Composez et enregistrez des morceaux, collaborez avec des artistes camerounais.',
                    'duree_semaines' => 52,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'experience',
                    'titre' => 'Performances et networking',
                    'description' => 'Participez à des festivals comme FOMARIC ou concerts à Yaoundé/Douala.',
                    'duree_semaines' => 52,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 24,
        ];

        $profileConfigs[27] = [
            'min_level' => 'BAC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Études en génie civil',
                    'description' => 'Passez le concours de l\'École Nationale Supérieure Polytechnique (Yaoundé ou Douala), formez-vous 5 ans pour le diplôme d\'ingénieur.',
                    'duree_semaines' => 260,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'stage',
                    'titre' => 'Stage en BTP',
                    'description' => 'Travaillez sur des chantiers au Cameroun pour de l\'expérience pratique.',
                    'duree_semaines' => 12,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 60,
        ];

        $profileConfigs[28] = [
            'min_level' => 'BAC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Études en pharmacie',
                    'description' => 'Inscrivez-vous en faculté de pharmacie (comme à l\'Université des Montagnes), étudiez 6 ans pour le Doctorat en Pharmacie.',
                    'duree_semaines' => 312,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'stage',
                    'titre' => 'Stage en officine',
                    'description' => 'Effectuez des stages en pharmacie ou laboratoire au Cameroun.',
                    'duree_semaines' => 26,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 72,
        ];

        $profileConfigs[30] = [
            'min_level' => 'BEPC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Concours d\'entrée à l\'École de Police',
                    'description' => 'Préparez et passez le concours national de la Police ou Gendarmerie au Cameroun (minimum BEPC, BAC préféré).',
                    'duree_semaines' => 4,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'formation',
                    'titre' => 'Formation à l\'École de Police',
                    'description' => 'Suivez la formation de 2 ans à l\'EMIA ou école de police à Yaoundé.',
                    'duree_semaines' => 104,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'experience',
                    'titre' => 'Service actif',
                    'description' => 'Intégrez les forces de l\'ordre et gagnez de l\'expérience sur le terrain.',
                    'duree_semaines' => 52,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 24,
        ];

        $profileConfigs[31] = [
            'min_level' => 'BEPC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Formation en entrepreneuriat',
                    'description' => 'Suivez des cours en gestion d\'entreprise via des programmes comme ceux du MINPMEESA ou formations privées au Cameroun.',
                    'duree_semaines' => 52,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'projet',
                    'titre' => 'Création d\'entreprise',
                    'description' => 'Rédigez un business plan, enregistrez votre entreprise via le CFCE au Cameroun (conforme OHADA).',
                    'duree_semaines' => 12,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'experience',
                    'titre' => 'Gestion et développement',
                    'description' => 'Lancez et gérez votre entreprise, cherchez des financements via PJI ou banques camerounaises.',
                    'duree_semaines' => 52,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 24,
        ];

        $profileConfigs[32] = [
            'min_level' => 'BAC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Études en agronomie',
                    'description' => 'Passez le concours de l\'École Nationale Supérieure d\'Agronomie (FASA à Dschang), formez-vous 5 ans pour ingénieur agronome.',
                    'duree_semaines' => 260,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'stage',
                    'titre' => 'Stage agricole',
                    'description' => 'Travaillez dans une ferme ou institut comme l\'IRAD au Cameroun.',
                    'duree_semaines' => 12,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 60,
        ];

        $profileConfigs[33] = [
            'min_level' => 'BAC',
            'is_cfpam' => false,
            'base_steps' => [
                [
                    'type' => 'formation',
                    'titre' => 'Études en architecture',
                    'description' => 'Inscrivez-vous via concours à une école d\'architecture au Cameroun ou EAMAU (Lomé, mais accessible), étudiez 6 ans.',
                    'duree_semaines' => 312,
                    'obligatoire' => true,
                ],
                [
                    'type' => 'certification',
                    'titre' => 'Inscription à l\'Ordre des Architectes',
                    'description' => 'Obtenez l\'habilitation de l\'Ordre National des Architectes du Cameroun.',
                    'duree_semaines' => 4,
                    'obligatoire' => true,
                ],
            ],
            'duree_base_mois' => 72,
        ];

        // Création des roadmaps et steps
        foreach ($profileConfigs as $profileId => $config) {
            $minLevelIndex = $levelOrder[$config['min_level']];
            foreach ($levelOrder as $niveauDepart => $index) {
                if ($index > $minLevelIndex) {
                    continue;
                }

                $educationSteps = [];
                $currentLevel = $niveauDepart;
                $ordre = 1;
                while ($levelOrder[$currentLevel] < $minLevelIndex) {
                    $next = $nextLevel[$currentLevel];
                    if ($next === null) break;
                    $step = $diplomaSteps[$next];
                    $educationSteps[] = [
                        'titre' => $step['titre'],
                        'description' => $step['description'],
                        'duree_semaines' => $step['duree_semaines'],
                        'type' => $step['type'],
                        'ordre' => $ordre,
                        'obligatoire' => true,
                    ];
                    $ordre++;
                    $currentLevel = $next;
                }

                $baseStepsWithOrdre = [];
                foreach ($config['base_steps'] as $baseStep) {
                    $baseStepsWithOrdre[] = [
                        'titre' => $baseStep['titre'],
                        'description' => $baseStep['description'],
                        'duree_semaines' => $baseStep['duree_semaines'],
                        'type' => $baseStep['type'],
                        'ordre' => $ordre,
                        'obligatoire' => $baseStep['obligatoire'],
                    ];
                    $ordre++;
                }

                $totalDureeSemaines = 0;
                foreach ($educationSteps as $e) {
                    $totalDureeSemaines += $e['duree_semaines'];
                }
                foreach ($baseStepsWithOrdre as $b) {
                    $totalDureeSemaines += $b['duree_semaines'];
                }
                $dureeEstimeeMois = round($totalDureeSemaines / 4.33); // Conversion approximative en mois

                $roadmapId = DB::table('roadmaps')->insertGetId([
                    'profile_id' => $profileId,
                    'niveau_depart' => $niveauDepart,
                    'titre' => 'Roadmap pour devenir ' . $profiles[$profileId] . ' à partir du ' . $niveauDepart,
                    'description' => 'Parcours personnalisé et adapté au contexte camerounais, en priorisant les formations du groupe CFPAM pour les profils concernés.',
                    'duree_estimee_mois' => $dureeEstimeeMois,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($educationSteps as $step) {
                    DB::table('roadmap_steps')->insert([
                        'roadmap_id' => $roadmapId,
                        'titre' => $step['titre'],
                        'description' => $step['description'],
                        'ordre' => $step['ordre'],
                        'duree_semaines' => $step['duree_semaines'],
                        'type' => $step['type'],
                        'pack_recommande_id' => null,
                        'obligatoire' => $step['obligatoire'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                foreach ($baseStepsWithOrdre as $step) {
                    DB::table('roadmap_steps')->insert([
                        'roadmap_id' => $roadmapId,
                        'titre' => $step['titre'],
                        'description' => $step['description'],
                        'ordre' => $step['ordre'],
                        'duree_semaines' => $step['duree_semaines'],
                        'type' => $step['type'],
                        'pack_recommande_id' => null,
                        'obligatoire' => $step['obligatoire'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}