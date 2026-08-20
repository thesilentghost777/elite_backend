<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PackSeeder extends Seeder
{
    /**
     * Seed training packs with modules only.
     */
    public function run(): void
    {
        // Créer les catégories de packs
        $categories = [
            ['nom' => 'Secrétariat', 'slug' => 'secretariat', 'description' => 'Formations en secrétariat et administration', 'ordre' => 1],
            ['nom' => 'Audiovisuel', 'slug' => 'audiovisuel', 'description' => 'Formations en création audiovisuelle et graphique', 'ordre' => 2],
            ['nom' => 'Beauté', 'slug' => 'beaute', 'description' => 'Formations en esthétique et décoration', 'ordre' => 3],
            ['nom' => 'Digital', 'slug' => 'digital', 'description' => 'Formations en marketing digital et développement', 'ordre' => 4],
            ['nom' => 'Gestion', 'slug' => 'gestion', 'description' => 'Formations en gestion de projet et RH', 'ordre' => 5],
            ['nom' => 'Comptabilité', 'slug' => 'comptabilite', 'description' => 'Formations en comptabilité et finance', 'ordre' => 6],
            ['nom' => 'Commerce', 'slug' => 'commerce', 'description' => 'Formations en commerce international', 'ordre' => 7],
            ['nom' => 'Informatique', 'slug' => 'informatique', 'description' => 'Formations en maintenance et réseaux', 'ordre' => 8],
            ['nom' => 'Ateliers Pratiques', 'slug' => 'ateliers-pratiques', 'description' => 'Formations courtes et ateliers', 'ordre' => 9],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->insert([
                'nom' => $cat['nom'],
                'slug' => $cat['slug'],
                'description' => $cat['description'],
                'ordre' => $cat['ordre'],
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Définir les packs
        $packs = [
            // SECRÉTARIAT
            [
                'category_id' => 1,
                'nom' => 'Bureautique',
                'slug' => 'bureautique',
                'description' => 'Maîtrise complète des outils de bureautique: Word, Excel, PowerPoint',
                'niveau_requis' => 'BEPC',
                'durees' => '3 mois,6 mois,12 mois',
                'certifications' => 'AQP,CQP,DQP',
                'prix_points' => 50,
                'modules' => $this->getBureautiqueModules(),
            ],
            [
                'category_id' => 1,
                'nom' => 'Assistante de Direction',
                'slug' => 'assistante-de-direction',
                'description' => 'Formation complète pour devenir assistante de direction professionnelle',
                'niveau_requis' => 'BAC',
                'durees' => '3 mois,6 mois,12 mois',
                'certifications' => 'AQP,CQP,DQP',
                'prix_points' => 75,
                'modules' => $this->getAssistanteDirectionModules(),
            ],
            [
                'category_id' => 1,
                'nom' => 'Secrétaire Comptable',
                'slug' => 'secretaire-comptable',
                'description' => 'Double compétence en secrétariat et comptabilité',
                'niveau_requis' => 'Probatoire',
                'durees' => '3 mois,6 mois,12 mois',
                'certifications' => 'AQP,CQP,DQP',
                'prix_points' => 60,
                'modules' => $this->getSecretaireComptableModules(),
            ],

            // AUDIOVISUEL
            [
                'category_id' => 2,
                'nom' => 'Infographie',
                'slug' => 'infographie',
                'description' => 'Création graphique avec Photoshop, Illustrator, InDesign',
                'niveau_requis' => 'BEPC',
                'durees' => '3 mois,6 mois,12 mois',
                'certifications' => 'AQP,CQP,DQP',
                'prix_points' => 65,
                'modules' => $this->getInfographieModules(),
            ],
            [
                'category_id' => 2,
                'nom' => 'Montage Vidéo',
                'slug' => 'montage-video',
                'description' => 'Techniques professionnelles de montage avec Premiere Pro et After Effects',
                'niveau_requis' => 'Probatoire',
                'durees' => '3 mois,6 mois,12 mois',
                'certifications' => 'AQP,CQP,DQP',
                'prix_points' => 70,
                'modules' => $this->getMontageVideoModules(),
            ],
            [
                'category_id' => 2,
                'nom' => 'Graphisme 2D/3D',
                'slug' => 'graphisme-2d-3d',
                'description' => 'Modélisation et animation 3D avec Blender et Cinema 4D',
                'niveau_requis' => 'BAC',
                'durees' => '3 mois,6 mois,12 mois',
                'certifications' => 'AQP,CQP,DQP',
                'prix_points' => 80,
                'modules' => $this->getGraphisme3DModules(),
            ],

            // BEAUTÉ
            [
                'category_id' => 3,
                'nom' => 'Esthétique/Cosmétique',
                'slug' => 'esthetique-cosmetique',
                'description' => 'Manicure, pédicure, maquillage et soins du visage',
                'niveau_requis' => 'BEPC',
                'durees' => '3 mois,6 mois',
                'certifications' => 'AQP,CQP',
                'prix_points' => 55,
                'modules' => $this->getEsthetiqueModules(),
            ],
            [
                'category_id' => 3,
                'nom' => 'Décoration',
                'slug' => 'decoration',
                'description' => 'Décoration intérieure et événementielle',
                'niveau_requis' => 'BEPC',
                'durees' => '3 mois,6 mois',
                'certifications' => 'AQP,CQP',
                'prix_points' => 50,
                'modules' => $this->getDecorationModules(),
            ],

            // DIGITAL
            [
                'category_id' => 4,
                'nom' => 'Marketing Digital',
                'slug' => 'marketing-digital',
                'description' => 'Stratégies marketing sur les réseaux sociaux, SEO, publicité en ligne',
                'niveau_requis' => 'BAC',
                'durees' => '6 mois,12 mois',
                'certifications' => 'CQP,DQP',
                'prix_points' => 85,
                'modules' => $this->getMarketingDigitalModules(),
            ],
            [
                'category_id' => 4,
                'nom' => 'Développement Web et App',
                'slug' => 'developpement-web-et-app',
                'description' => 'Création de sites web et applications mobiles modernes',
                'niveau_requis' => 'BAC',
                'durees' => '6 mois,12 mois',
                'certifications' => 'CQP,DQP',
                'prix_points' => 100,
                'modules' => $this->getDeveloppementWebModules(),
            ],

            // GESTION
            [
                'category_id' => 5,
                'nom' => 'Gestion des Projets',
                'slug' => 'gestion-projets',
                'description' => 'Méthodologies de gestion de projet: Agile, Scrum, PMP',
                'niveau_requis' => 'Licence',
                'durees' => '6 mois,12 mois,24 mois',
                'certifications' => 'CQP,BTS',
                'prix_points' => 90,
                'modules' => $this->getGestionProjetsModules(),
            ],
            [
                'category_id' => 5,
                'nom' => 'Gestion des Ressources Humaines',
                'slug' => 'gestion-rh',
                'description' => 'Recrutement, formation, gestion des talents et paie',
                'niveau_requis' => 'BAC',
                'durees' => '6 mois,12 mois,24 mois',
                'certifications' => 'CQP,BTS',
                'prix_points' => 80,
                'modules' => $this->getGestionRHModules(),
            ],

            // COMPTABILITÉ
            [
                'category_id' => 6,
                'nom' => 'Comptabilité Informatisée et Gestion',
                'slug' => 'comptabilite-informatisee',
                'description' => 'Comptabilité avec logiciels professionnels: Sage, EBP',
                'niveau_requis' => 'BAC',
                'durees' => '6 mois,12 mois,24 mois',
                'certifications' => 'CQP,DQP',
                'prix_points' => 85,
                'modules' => $this->getComptabiliteModules(),
            ],
            [
                'category_id' => 6,
                'nom' => 'Banque et Finance',
                'slug' => 'banque-finance',
                'description' => 'Opérations bancaires, analyse financière, crédit',
                'niveau_requis' => 'BAC',
                'durees' => '6 mois,12 mois,24 mois',
                'certifications' => 'CQP,DQP',
                'prix_points' => 90,
                'modules' => $this->getBanqueFinanceModules(),
            ],

            // COMMERCE
            [
                'category_id' => 7,
                'nom' => 'Douane et Transit',
                'slug' => 'douane-transit',
                'description' => 'Procédures douanières, import-export, réglementation',
                'niveau_requis' => 'BAC',
                'durees' => '6 mois,12 mois',
                'certifications' => 'CQP',
                'prix_points' => 75,
                'modules' => $this->getDouaneTransitModules(),
            ],
            [
                'category_id' => 7,
                'nom' => 'Logistique/Transport',
                'slug' => 'logistique-transport',
                'description' => 'Gestion de la chaîne logistique et transport de marchandises',
                'niveau_requis' => 'BAC',
                'durees' => '6 mois,12 mois',
                'certifications' => 'CQP',
                'prix_points' => 70,
                'modules' => $this->getLogistiqueModules(),
            ],

            // INFORMATIQUE
            [
                'category_id' => 8,
                'nom' => 'Maintenance Informatique',
                'slug' => 'maintenance-informatique',
                'description' => 'Dépannage matériel et logiciel, installation de systèmes',
                'niveau_requis' => 'BAC',
                'durees' => '6 mois,12 mois',
                'certifications' => 'DQP',
                'prix_points' => 75,
                'modules' => $this->getMaintenanceModules(),
            ],
            [
                'category_id' => 8,
                'nom' => 'Réseau Informatique',
                'slug' => 'reseau-informatique',
                'description' => 'Configuration réseau, administration système, sécurité',
                'niveau_requis' => 'BAC',
                'durees' => '6 mois,12 mois',
                'certifications' => 'DQP',
                'prix_points' => 85,
                'modules' => $this->getReseauModules(),
            ],

            // ATELIERS PRATIQUES
            [
                'category_id' => 9,
                'nom' => 'Sculpture de Fruits et Jus',
                'slug' => 'sculpture-fruits-jus',
                'description' => 'Art de la sculpture de fruits et fabrication de jus naturels',
                'niveau_requis' => 'BEPC',
                'durees' => '2 semaines',
                'certifications' => 'Attestation',
                'prix_points' => 25,
                'modules' => $this->getSculptureFruitsModules(),
            ],
            [
                'category_id' => 9,
                'nom' => 'Élevage Professionnel',
                'slug' => 'elevage-professionnel',
                'description' => 'Techniques d\'élevage de vers blancs et escargots',
                'niveau_requis' => 'BEPC',
                'durees' => '2 semaines',
                'certifications' => 'Attestation',
                'prix_points' => 25,
                'modules' => $this->getElevageModules(),
            ],
            [
                'category_id' => 9,
                'nom' => 'Fabrication Produits Cosmétiques',
                'slug' => 'fabrication-cosmetiques',
                'description' => 'Création de crèmes, savons, lotions et gels',
                'niveau_requis' => 'BEPC',
                'durees' => '2 semaines',
                'certifications' => 'Attestation',
                'prix_points' => 30,
                'modules' => $this->getFabricationCosmetiquesModules(),
            ],
            [
                'category_id' => 9,
                'nom' => 'Fabrication Produits Ménagers',
                'slug' => 'fabrication-menagers',
                'description' => 'Production de détergents, savons et produits d\'entretien',
                'niveau_requis' => 'BEPC',
                'durees' => '2 semaines',
                'certifications' => 'Attestation',
                'prix_points' => 30,
                'modules' => $this->getFabricationMenagersModules(),
            ],
        ];

        foreach ($packs as $pack) {
            $packId = DB::table('packs')->insertGetId([
                'category_id' => $pack['category_id'],
                'nom' => $pack['nom'],
                'slug' => $pack['slug'],
                'description' => $pack['description'],
                'niveau_requis' => $pack['niveau_requis'],
                'durees_disponibles' => json_encode($pack['durees']),
                'prix_points' => $pack['prix_points'],
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insérer les modules seulement (pas de chapitres, leçons ou quiz)
            foreach ($pack['modules'] as $moduleIndex => $module) {
                DB::table('modules')->insert([
                    'pack_id' => $packId,
                    'nom' => $module['titre'],
                    'description' => $module['description'] ?? null,
                    'ordre' => $moduleIndex + 1,
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    // ===== MODULES SECRÉTARIAT =====
    private function getBureautiqueModules(): array
    {
        return [
            [
                'titre' => 'Microsoft Word',
                'description' => 'Traitement de texte professionnel',
            ],
            [
                'titre' => 'Microsoft Excel',
                'description' => 'Tableur et analyse de données',
            ],
            [
                'titre' => 'Microsoft PowerPoint',
                'description' => 'Présentations professionnelles',
            ],
        ];
    }

    private function getAssistanteDirectionModules(): array
    {
        return [
            [
                'titre' => 'Organisation et gestion du temps',
                'description' => 'Maîtrise de l\'agenda et des priorités',
            ],
            [
                'titre' => 'Communication professionnelle',
                'description' => 'Écrits et oral en contexte professionnel',
            ],
            [
                'titre' => 'Organisation d\'événements',
                'description' => 'Réunions, déplacements, événements',
            ],
        ];
    }

    private function getSecretaireComptableModules(): array
    {
        return [
            [
                'titre' => 'Bases de la comptabilité',
                'description' => 'Principes comptables fondamentaux',
            ],
            [
                'titre' => 'Gestion des documents',
                'description' => 'Factures, devis, bons de commande',
            ],
        ];
    }

    // ===== MODULES AUDIOVISUEL =====
    private function getInfographieModules(): array
    {
        return [
            [
                'titre' => 'Adobe Photoshop',
                'description' => 'Retouche photo et création graphique',
            ],
            [
                'titre' => 'Adobe Illustrator',
                'description' => 'Création vectorielle',
            ],
            [
                'titre' => 'Adobe InDesign',
                'description' => 'Mise en page professionnelle',
            ],
        ];
    }

    private function getMontageVideoModules(): array
    {
        return [
            [
                'titre' => 'Adobe Premiere Pro',
                'description' => 'Montage vidéo professionnel',
            ],
            [
                'titre' => 'After Effects',
                'description' => 'Motion design et effets visuels',
            ],
        ];
    }

    private function getGraphisme3DModules(): array
    {
        return [
            [
                'titre' => 'Blender - Fondamentaux',
                'description' => 'Initiation à la 3D avec Blender',
            ],
            [
                'titre' => 'Animation 3D',
                'description' => 'Animer des objets et personnages',
            ],
            [
                'titre' => 'Rendu et compositing',
                'description' => 'Rendu photoréaliste',
            ],
        ];
    }

    // ===== MODULES BEAUTÉ =====
    private function getEsthetiqueModules(): array
    {
        return [
            [
                'titre' => 'Soins du visage',
                'description' => 'Techniques de soins esthétiques',
            ],
            [
                'titre' => 'Maquillage professionnel',
                'description' => 'Techniques de make-up',
            ],
            [
                'titre' => 'Manucure et pédicure',
                'description' => 'Soins des mains et pieds',
            ],
        ];
    }

    private function getDecorationModules(): array
    {
        return [
            [
                'titre' => 'Décoration intérieure',
                'description' => 'Aménagement d\'espaces de vie',
            ],
            [
                'titre' => 'Décoration événementielle',
                'description' => 'Mariages, anniversaires, corporate',
            ],
        ];
    }

    // ===== MODULES DIGITAL =====
    private function getMarketingDigitalModules(): array
    {
        return [
            [
                'titre' => 'Fondamentaux du marketing digital',
                'description' => 'Stratégies et canaux digitaux',
            ],
            [
                'titre' => 'Réseaux sociaux',
                'description' => 'Community management',
            ],
            [
                'titre' => 'SEO et publicité',
                'description' => 'Référencement et ads',
            ],
        ];
    }

    private function getDeveloppementWebModules(): array
    {
        return [
            [
                'titre' => 'HTML & CSS',
                'description' => 'Fondamentaux du web',
            ],
            [
                'titre' => 'JavaScript',
                'description' => 'Programmation front-end',
            ],
            [
                'titre' => 'Back-end & Bases de données',
                'description' => 'Serveur et données',
            ],
            [
                'titre' => 'Développement mobile',
                'description' => 'Applications mobiles',
            ],
        ];
    }

    // ===== MODULES GESTION =====
    private function getGestionProjetsModules(): array
    {
        return [
            [
                'titre' => 'Fondamentaux de la gestion de projet',
                'description' => 'Concepts et méthodologies',
            ],
            [
                'titre' => 'Méthodes Agiles',
                'description' => 'Scrum, Kanban, SAFe',
            ],
            [
                'titre' => 'Gestion des risques',
                'description' => 'Identification et mitigation',
            ],
        ];
    }

    private function getGestionRHModules(): array
    {
        return [
            [
                'titre' => 'Recrutement',
                'description' => 'Processus de recrutement',
            ],
            [
                'titre' => 'Gestion administrative du personnel',
                'description' => 'Contrats, paie, absences',
            ],
            [
                'titre' => 'Droit du travail',
                'description' => 'Législation camerounaise',
            ],
        ];
    }

    // ===== MODULES COMPTABILITÉ =====
    private function getComptabiliteModules(): array
    {
        return [
            [
                'titre' => 'Comptabilité générale',
                'description' => 'Fondamentaux OHADA',
            ],
            [
                'titre' => 'Logiciels comptables',
                'description' => 'Sage et EBP',
            ],
            [
                'titre' => 'Fiscalité',
                'description' => 'Impôts et taxes au Cameroun',
            ],
        ];
    }

    private function getBanqueFinanceModules(): array
    {
        return [
            [
                'titre' => 'Opérations bancaires',
                'description' => 'Produits et services bancaires',
            ],
            [
                'titre' => 'Crédit',
                'description' => 'Analyse et octroi de crédit',
            ],
            [
                'titre' => 'Mobile Money',
                'description' => 'Services financiers mobiles',
            ],
        ];
    }

    // ===== MODULES COMMERCE =====
    private function getDouaneTransitModules(): array
    {
        return [
            [
                'titre' => 'Réglementation douanière',
                'description' => 'Cadre légal CEMAC',
            ],
            [
                'titre' => 'Procédures Import/Export',
                'description' => 'Formalités douanières',
            ],
            [
                'titre' => 'Incoterms',
                'description' => 'Termes du commerce international',
            ],
        ];
    }

    private function getLogistiqueModules(): array
    {
        return [
            [
                'titre' => 'Supply Chain Management',
                'description' => 'Gestion de la chaîne logistique',
            ],
            [
                'titre' => 'Gestion des stocks',
                'description' => 'Inventaire et approvisionnement',
            ],
            [
                'titre' => 'Transport',
                'description' => 'Modes de transport',
            ],
        ];
    }

    // ===== MODULES INFORMATIQUE =====
    private function getMaintenanceModules(): array
    {
        return [
            [
                'titre' => 'Hardware',
                'description' => 'Composants et assemblage',
            ],
            [
                'titre' => 'Systèmes d\'exploitation',
                'description' => 'Windows et Linux',
            ],
            [
                'titre' => 'Dépannage logiciel',
                'description' => 'Résolution de problèmes',
            ],
        ];
    }

    private function getReseauModules(): array
    {
        return [
            [
                'titre' => 'Fondamentaux réseau',
                'description' => 'Concepts de base',
            ],
            [
                'titre' => 'Administration réseau',
                'description' => 'Configuration et maintenance',
            ],
            [
                'titre' => 'Sécurité réseau',
                'description' => 'Firewall et VPN',
            ],
        ];
    }

    // ===== MODULES ATELIERS PRATIQUES =====
    private function getSculptureFruitsModules(): array
    {
        return [
            [
                'titre' => 'Sculpture de fruits',
                'description' => 'Art de la découpe décorative',
            ],
            [
                'titre' => 'Fabrication de jus naturels',
                'description' => 'Jus frais et cocktails',
            ],
        ];
    }

    private function getElevageModules(): array
    {
        return [
            [
                'titre' => 'Élevage de vers blancs',
                'description' => 'Hannetons pour alimentation',
            ],
            [
                'titre' => 'Élevage d\'escargots',
                'description' => 'Héliciculture commerciale',
            ],
        ];
    }

    private function getFabricationCosmetiquesModules(): array
    {
        return [
            [
                'titre' => 'Produits pour le corps',
                'description' => 'Laits et crèmes',
            ],
            [
                'titre' => 'Produits pour le visage',
                'description' => 'Soins ciblés',
            ],
            [
                'titre' => 'Savons et cheveux',
                'description' => 'Savons et soins capillaires',
            ],
        ];
    }

    private function getFabricationMenagersModules(): array
    {
        return [
            [
                'titre' => 'Produits liquides',
                'description' => 'Nettoyants liquides',
            ],
            [
                'titre' => 'Produits solides',
                'description' => 'Savons et détergents',
            ],
            [
                'titre' => 'Conditionnement',
                'description' => 'Emballage et étiquetage',
            ],
        ];
    }
}