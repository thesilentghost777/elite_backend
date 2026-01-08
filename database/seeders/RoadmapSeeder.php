<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoadmapSeeder extends Seeder
{
    /**
     * Seed roadmaps for each career profile.
     */
    public function run(): void
    {
        $profiles = DB::table('career_profiles')->get();

        foreach ($profiles as $profile) {
            // Créer la roadmap principale
            $roadmapId = DB::table('roadmaps')->insertGetId([
                'profile_id' => $profile->id,
                'niveau_depart' => $profile->niveau_minimum,
                'titre' => "Parcours pour devenir {$profile->nom}",
                'description' => "Votre feuille de route personnalisée vers le métier de {$profile->nom}",
                'duree_estimee_mois' => $this->getDureeMois($profile->niveau_minimum),
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Créer les étapes selon le niveau minimum requis
            $this->createStepsForLevel($roadmapId, $profile);
        }
    }

    private function getDureeMois(string $niveau): int
    {
        return match($niveau) {
            'BEPC' => 9,
            'Probatoire' => 9,
            'BAC' => 12,
            'Licence' => 18,
            'Master' => 30,
            default => 12,
        };
    }

    private function getDureeSemaines(string $duree): ?int
    {
        return match($duree) {
            '1 semaine' => 1,
            '2 semaines' => 2,
            '1 mois' => 4,
            '2 mois' => 8,
            '3 mois' => 12,
            '4 mois' => 16,
            '6 mois' => 24,
            '12 mois' => 48,
            default => null,
        };
    }

    private function createStepsForLevel(int $roadmapId, object $profile): void
    {
        $niveau = $profile->niveau_minimum;
        $steps = [];

        // Étape 1 : Évaluation initiale (commune à tous)
        $steps[] = [
            'titre' => 'Évaluation de votre profil',
            'description' => 'Analyse de vos compétences actuelles et définition de vos objectifs',
            'type' => 'formation', // Changé de 'evaluation' à 'formation'
            'duree_semaines' => 1,
            'ordre' => 1,
            'ressources' => json_encode([
                'Test de positionnement',
                'Entretien avec un conseiller',
                'Bilan de compétences'
            ]),
        ];

        // Étapes selon le niveau
        switch ($niveau) {
            case 'BEPC':
                $steps = array_merge($steps, $this->getBEPCSteps($profile));
                break;
            case 'Probatoire':
                $steps = array_merge($steps, $this->getProbSteps($profile));
                break;
            case 'BAC':
                $steps = array_merge($steps, $this->getBACSteps($profile));
                break;
            case 'Licence':
                $steps = array_merge($steps, $this->getLicenceSteps($profile));
                break;
            case 'Master':
                $steps = array_merge($steps, $this->getMasterSteps($profile));
                break;
        }

        // Étape finale : Insertion professionnelle
        $steps[] = [
            'titre' => 'Insertion professionnelle',
            'description' => 'Accompagnement vers l\'emploi et le réseau professionnel',
            'type' => 'experience', // Changé de 'insertion' à 'experience'
            'duree_semaines' => 8, // 2 mois = 8 semaines
            'ordre' => count($steps) + 1,
            'ressources' => json_encode([
                'Création de CV professionnel',
                'Préparation aux entretiens',
                'Mise en relation avec des entreprises',
                'Accompagnement création d\'entreprise'
            ]),
        ];

        // Insérer toutes les étapes
        foreach ($steps as $step) {
            DB::table('roadmap_steps')->insert([
                'roadmap_id' => $roadmapId,
                'titre' => $step['titre'],
                'description' => $step['description'],
                'type' => $step['type'],
                'ordre' => $step['ordre'],
                'duree_semaines' => $step['duree_semaines'] ?? null,
                'pack_recommande_id' => $step['pack_id'] ?? null,
                'obligatoire' => $step['obligatoire'] ?? true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function getBEPCSteps(object $profile): array
    {
        return [
            [
                'titre' => 'Formation de base',
                'description' => 'Acquisition des compétences fondamentales du métier',
                'type' => 'formation',
                'duree_semaines' => 12, // 3 mois = 12 semaines
                'ordre' => 2,
                'ressources' => json_encode([
                    'Cours théoriques en ligne',
                    'Exercices pratiques',
                    'Quiz de validation'
                ]),
            ],
            [
                'titre' => 'Spécialisation AQP',
                'description' => 'Approfondissement et obtention de l\'Attestation de Qualification Professionnelle',
                'type' => 'certification',
                'duree_semaines' => 8, // 2 mois = 8 semaines
                'ordre' => 3,
                'ressources' => json_encode([
                    'Modules spécialisés',
                    'Projets pratiques',
                    'Préparation à l\'examen'
                ]),
            ],
            [
                'titre' => 'Stage pratique',
                'description' => 'Immersion en entreprise pour acquérir l\'expérience terrain',
                'type' => 'stage',
                'duree_semaines' => 6, // 1-2 mois = 6 semaines
                'ordre' => 4,
                'ressources' => json_encode([
                    'Stage en entreprise partenaire',
                    'Mentorat professionnel',
                    'Rapport de stage'
                ]),
            ],
        ];
    }

    private function getProbSteps(object $profile): array
    {
        return [
            [
                'titre' => 'Remise à niveau',
                'description' => 'Consolidation des bases nécessaires pour la formation',
                'type' => 'formation',
                'duree_semaines' => 4, // 1 mois = 4 semaines
                'ordre' => 2,
                'ressources' => json_encode([
                    'Tests de niveau',
                    'Modules de renforcement',
                    'Cours de rattrapage'
                ]),
            ],
            [
                'titre' => 'Formation professionnelle',
                'description' => 'Formation complète aux techniques du métier',
                'type' => 'formation',
                'duree_semaines' => 16, // 4 mois = 16 semaines
                'ordre' => 3,
                'ressources' => json_encode([
                    'Cours théoriques',
                    'Travaux pratiques',
                    'Études de cas'
                ]),
            ],
            [
                'titre' => 'Certification CQP',
                'description' => 'Préparation et obtention du Certificat de Qualification Professionnelle',
                'type' => 'certification',
                'duree_semaines' => 8, // 2 mois = 8 semaines
                'ordre' => 4,
                'ressources' => json_encode([
                    'Révisions intensives',
                    'Examens blancs',
                    'Soutenance de projet'
                ]),
            ],
            [
                'titre' => 'Expérience pratique',
                'description' => 'Stage ou alternance en milieu professionnel',
                'type' => 'stage',
                'duree_semaines' => 10, // 2-3 mois = 10 semaines
                'ordre' => 5,
                'ressources' => json_encode([
                    'Stage conventionné',
                    'Suivi tutoral',
                    'Évaluation continue'
                ]),
            ],
        ];
    }

    private function getBACSteps(object $profile): array
    {
        return [
            [
                'titre' => 'Orientation et planification',
                'description' => 'Définition du parcours adapté à vos objectifs',
                'type' => 'formation',
                'duree_semaines' => 2,
                'ordre' => 2,
                'ressources' => json_encode([
                    'Conseil en orientation',
                    'Plan de formation personnalisé',
                    'Objectifs SMART'
                ]),
            ],
            [
                'titre' => 'Formation fondamentale',
                'description' => 'Maîtrise des concepts et techniques de base',
                'type' => 'formation',
                'duree_semaines' => 12, // 3 mois = 12 semaines
                'ordre' => 3,
                'ressources' => json_encode([
                    'Modules e-learning',
                    'Webinaires experts',
                    'Exercices notés'
                ]),
            ],
            [
                'titre' => 'Spécialisation avancée',
                'description' => 'Approfondissement dans votre domaine de spécialité',
                'type' => 'formation',
                'duree_semaines' => 12, // 3 mois = 12 semaines
                'ordre' => 4,
                'ressources' => json_encode([
                    'Cours avancés',
                    'Projets complexes',
                    'Certification intermédiaire'
                ]),
            ],
            [
                'titre' => 'Certification DQP/CQP',
                'description' => 'Obtention du Diplôme de Qualification Professionnelle',
                'type' => 'certification',
                'duree_semaines' => 8, // 2 mois = 8 semaines
                'ordre' => 5,
                'ressources' => json_encode([
                    'Préparation examen national',
                    'Mémoire professionnel',
                    'Jury de certification'
                ]),
            ],
            [
                'titre' => 'Immersion professionnelle',
                'description' => 'Stage de longue durée en entreprise',
                'type' => 'stage',
                'duree_semaines' => 14, // 3-4 mois = 14 semaines
                'ordre' => 6,
                'ressources' => json_encode([
                    'Stage en entreprise',
                    'Missions réelles',
                    'Réseau professionnel'
                ]),
            ],
        ];
    }

    private function getLicenceSteps(object $profile): array
    {
        return [
            [
                'titre' => 'Validation des prérequis',
                'description' => 'Vérification et consolidation des connaissances de base',
                'type' => 'formation',
                'duree_semaines' => 4, // 1 mois = 4 semaines
                'ordre' => 2,
                'ressources' => json_encode([
                    'Tests de positionnement avancés',
                    'Modules de mise à niveau',
                    'Coaching personnalisé'
                ]),
            ],
            [
                'titre' => 'Formation théorique approfondie',
                'description' => 'Maîtrise des concepts avancés du domaine',
                'type' => 'formation',
                'duree_semaines' => 24, // 6 mois = 24 semaines
                'ordre' => 3,
                'ressources' => json_encode([
                    'Cours universitaires',
                    'Travaux de recherche',
                    'Séminaires spécialisés'
                ]),
            ],
            [
                'titre' => 'Projets professionnels',
                'description' => 'Réalisation de projets concrets de niveau professionnel',
                'type' => 'projet',
                'duree_semaines' => 12, // 3 mois = 12 semaines
                'ordre' => 4,
                'ressources' => json_encode([
                    'Projet individuel',
                    'Projet d\'équipe',
                    'Portfolio professionnel'
                ]),
            ],
            [
                'titre' => 'Certification professionnelle',
                'description' => 'Obtention de certifications reconnues par la profession',
                'type' => 'certification',
                'duree_semaines' => 8, // 2 mois = 8 semaines
                'ordre' => 5,
                'ressources' => json_encode([
                    'Certifications sectorielles',
                    'Examens professionnels',
                    'Accréditations'
                ]),
            ],
            [
                'titre' => 'Expérience terrain',
                'description' => 'Stage ou alternance de longue durée',
                'type' => 'stage',
                'duree_semaines' => 24, // 6 mois = 24 semaines
                'ordre' => 6,
                'ressources' => json_encode([
                    'Alternance entreprise',
                    'Missions responsabilisantes',
                    'Mentorat senior'
                ]),
            ],
        ];
    }

    private function getMasterSteps(object $profile): array
    {
        return [
            [
                'titre' => 'Évaluation des compétences',
                'description' => 'Bilan complet de vos acquis et définition du parcours',
                'type' => 'formation',
                'duree_semaines' => 4, // 1 mois = 4 semaines
                'ordre' => 2,
                'ressources' => json_encode([
                    'Évaluation approfondie',
                    'Plan de développement',
                    'Objectifs de carrière'
                ]),
            ],
            [
                'titre' => 'Formation d\'excellence',
                'description' => 'Parcours de formation de haut niveau',
                'type' => 'formation',
                'duree_semaines' => 48, // 12 mois = 48 semaines
                'ordre' => 3,
                'ressources' => json_encode([
                    'Cours de niveau Master',
                    'Recherche appliquée',
                    'Publications'
                ]),
            ],
            [
                'titre' => 'Spécialisation experte',
                'description' => 'Développement d\'une expertise pointue',
                'type' => 'formation',
                'duree_semaines' => 24, // 6 mois = 24 semaines
                'ordre' => 4,
                'ressources' => json_encode([
                    'Formation spécialisée',
                    'Conférences internationales',
                    'Réseau d\'experts'
                ]),
            ],
            [
                'titre' => 'Mémoire/Thèse professionnelle',
                'description' => 'Travail de recherche ou projet d\'envergure',
                'type' => 'diplome',
                'duree_semaines' => 24, // 6 mois = 24 semaines
                'ordre' => 5,
                'ressources' => json_encode([
                    'Direction de recherche',
                    'Méthodologie avancée',
                    'Soutenance'
                ]),
            ],
            [
                'titre' => 'Certifications et accréditations',
                'description' => 'Obtention des titres professionnels requis',
                'type' => 'certification',
                'duree_semaines' => 12, // 3 mois = 12 semaines
                'ordre' => 6,
                'ressources' => json_encode([
                    'Diplôme d\'État',
                    'Certifications internationales',
                    'Inscription à l\'ordre professionnel'
                ]),
            ],
            [
                'titre' => 'Résidence professionnelle',
                'description' => 'Expérience professionnelle supervisée obligatoire',
                'type' => 'stage',
                'duree_semaines' => 48, // 12 mois = 48 semaines
                'ordre' => 7,
                'ressources' => json_encode([
                    'Résidence/Internat',
                    'Supervision par pairs',
                    'Évaluation continue'
                ]),
            ],
        ];
    }
}