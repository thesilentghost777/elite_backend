<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CareerProfileSeeder extends Seeder
{
    /**
     * Seed career profiles - 35 profiles (CFPAM + Popular).
     */
    public function run(): void
    {
        $profiles = [
            // ===== PROFILS CFPAM =====
            // SECRÉTARIAT
            [
                'nom' => 'Secrétaire Bureautique',
                'secteur' => 'Secrétariat',
                'description' => 'Professionnel maîtrisant les outils bureautiques pour la gestion administrative des entreprises.',
                'niveau_minimum' => 'BEPC',
                'debouches' => json_encode([
                    'Assistant administratif',
                    'Agent de saisie',
                    'Secrétaire de direction',
                    'Chargé d\'accueil',
                    'Opérateur de saisie'
                ]),
                'competences' => json_encode(['Word', 'Excel', 'PowerPoint', 'Dactylographie', 'Classement']),
                'salaire_moyen' => '80000-150000',
                'tags' => 'bureautique,administration,saisie,classement',
                'is_cfpam' => true,
            ],
            [
                'nom' => 'Assistant(e) de Direction',
                'secteur' => 'Secrétariat',
                'description' => 'Collaborateur direct des dirigeants, gérant agenda, correspondances et organisation.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Assistant de direction',
                    'Secrétaire de PDG',
                    'Office Manager',
                    'Coordinateur administratif',
                    'Attaché de direction'
                ]),
                'competences' => json_encode(['Gestion agenda', 'Correspondance', 'Organisation événements', 'Protocole']),
                'salaire_moyen' => '150000-300000',
                'tags' => 'direction,organisation,protocole,correspondance',
                'is_cfpam' => true,
            ],
            [
                'nom' => 'Secrétaire Comptable',
                'secteur' => 'Secrétariat',
                'description' => 'Double compétence en secrétariat et en tenue des comptes de l\'entreprise.',
                'niveau_minimum' => 'Probatoire',
                'debouches' => json_encode([
                    'Secrétaire comptable',
                    'Aide-comptable',
                    'Gestionnaire administratif et financier',
                    'Agent de facturation',
                    'Assistant trésorerie'
                ]),
                'competences' => json_encode(['Comptabilité de base', 'Facturation', 'Bureautique', 'Classement']),
                'salaire_moyen' => '100000-200000',
                'tags' => 'comptabilite,secretariat,facturation,gestion',
                'is_cfpam' => true,
            ],

            // AUDIOVISUEL
            [
                'nom' => 'Infographe',
                'secteur' => 'Audiovisuel',
                'description' => 'Créateur de visuels et designs graphiques pour supports print et digitaux.',
                'niveau_minimum' => 'BEPC',
                'debouches' => json_encode([
                    'Infographe',
                    'Graphiste',
                    'Designer graphique',
                    'Maquettiste',
                    'Illustrateur numérique'
                ]),
                'competences' => json_encode(['Photoshop', 'Illustrator', 'InDesign', 'Créativité', 'Sens esthétique']),
                'salaire_moyen' => '80000-200000',
                'tags' => 'design,graphisme,creation,visuel,photoshop',
                'is_cfpam' => true,
            ],
            [
                'nom' => 'Monteur Vidéo',
                'secteur' => 'Audiovisuel',
                'description' => 'Spécialiste du montage et post-production vidéo professionnelle.',
                'niveau_minimum' => 'Probatoire',
                'debouches' => json_encode([
                    'Monteur vidéo',
                    'Éditeur vidéo',
                    'Post-producteur',
                    'Technicien audiovisuel',
                    'Réalisateur junior'
                ]),
                'competences' => json_encode(['Premiere Pro', 'After Effects', 'DaVinci Resolve', 'Storytelling']),
                'salaire_moyen' => '100000-250000',
                'tags' => 'video,montage,cinema,production,postproduction',
                'is_cfpam' => true,
            ],
            [
                'nom' => 'Graphiste 3D',
                'secteur' => 'Audiovisuel',
                'description' => 'Expert en modélisation et animation 3D pour jeux, films et publicités.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Graphiste 3D',
                    'Animateur 3D',
                    'Modélisateur',
                    'Designer de jeux vidéo',
                    'Artiste VFX'
                ]),
                'competences' => json_encode(['Blender', 'Cinema 4D', 'Maya', '3DS Max', 'Animation']),
                'salaire_moyen' => '150000-400000',
                'tags' => '3d,animation,modelisation,jeux,vfx',
                'is_cfpam' => true,
            ],

            // BEAUTÉ
            [
                'nom' => 'Esthéticien(ne)',
                'secteur' => 'Beauté',
                'description' => 'Professionnel des soins de beauté du visage et du corps.',
                'niveau_minimum' => 'BEPC',
                'debouches' => json_encode([
                    'Esthéticienne',
                    'Maquilleuse professionnelle',
                    'Conseillère beauté',
                    'Spa manager',
                    'Formatrice en esthétique'
                ]),
                'competences' => json_encode(['Soins visage', 'Maquillage', 'Manucure', 'Pédicure', 'Conseil client']),
                'salaire_moyen' => '60000-150000',
                'tags' => 'beaute,maquillage,soins,spa,bien-etre',
                'is_cfpam' => true,
            ],
            [
                'nom' => 'Décorateur(trice)',
                'secteur' => 'Beauté',
                'description' => 'Créateur d\'ambiances pour intérieurs et événements.',
                'niveau_minimum' => 'BEPC',
                'debouches' => json_encode([
                    'Décorateur d\'intérieur',
                    'Décorateur événementiel',
                    'Home stager',
                    'Scénographe',
                    'Designer d\'espace'
                ]),
                'competences' => json_encode(['Sens esthétique', 'Créativité', 'Harmonie couleurs', 'Aménagement']),
                'salaire_moyen' => '80000-200000',
                'tags' => 'decoration,interieur,evenementiel,design,amenagement',
                'is_cfpam' => true,
            ],

            // DIGITAL
            [
                'nom' => 'Spécialiste Marketing Digital',
                'secteur' => 'Digital',
                'description' => 'Expert des stratégies marketing sur les canaux numériques.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Community Manager',
                    'Traffic Manager',
                    'Growth Hacker',
                    'Consultant SEO/SEA',
                    'Digital Marketing Manager'
                ]),
                'competences' => json_encode(['Réseaux sociaux', 'SEO', 'Google Ads', 'Analytics', 'Content Marketing']),
                'salaire_moyen' => '150000-400000',
                'tags' => 'marketing,digital,reseaux-sociaux,seo,publicite',
                'is_cfpam' => true,
            ],
            [
                'nom' => 'Développeur Web et Mobile',
                'secteur' => 'Digital',
                'description' => 'Créateur de sites web et applications mobiles modernes.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Développeur Frontend',
                    'Développeur Backend',
                    'Développeur Full Stack',
                    'Développeur Mobile',
                    'Lead Developer'
                ]),
                'competences' => json_encode(['HTML/CSS', 'JavaScript', 'PHP', 'React/Vue', 'Bases de données']),
                'salaire_moyen' => '200000-600000',
                'tags' => 'developpement,web,mobile,programmation,code',
                'is_cfpam' => true,
            ],

            // GESTION
            [
                'nom' => 'Chef de Projet',
                'secteur' => 'Gestion',
                'description' => 'Responsable de la planification et exécution de projets d\'entreprise.',
                'niveau_minimum' => 'Licence',
                'debouches' => json_encode([
                    'Chef de projet',
                    'Project Manager',
                    'Scrum Master',
                    'Product Owner',
                    'Consultant en gestion'
                ]),
                'competences' => json_encode(['Planification', 'Leadership', 'Agile/Scrum', 'Budgétisation', 'Communication']),
                'salaire_moyen' => '300000-600000',
                'tags' => 'gestion,projet,management,agile,leadership',
                'is_cfpam' => true,
            ],
            [
                'nom' => 'Responsable RH',
                'secteur' => 'Gestion',
                'description' => 'Gestionnaire des ressources humaines et du développement des talents.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Assistant RH',
                    'Chargé de recrutement',
                    'Responsable formation',
                    'Gestionnaire de paie',
                    'DRH'
                ]),
                'competences' => json_encode(['Recrutement', 'Droit du travail', 'Gestion de paie', 'Formation', 'GPEC']),
                'salaire_moyen' => '200000-500000',
                'tags' => 'rh,ressources-humaines,recrutement,formation,paie',
                'is_cfpam' => true,
            ],

            // COMPTABILITÉ
            [
                'nom' => 'Comptable',
                'secteur' => 'Comptabilité',
                'description' => 'Spécialiste de la tenue des comptes et de la gestion financière.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Comptable',
                    'Chef comptable',
                    'Auditeur',
                    'Contrôleur de gestion',
                    'Expert-comptable stagiaire'
                ]),
                'competences' => json_encode(['Comptabilité générale', 'Fiscalité', 'Sage/EBP', 'Analyse financière']),
                'salaire_moyen' => '150000-400000',
                'tags' => 'comptabilite,finance,fiscalite,audit,gestion',
                'is_cfpam' => true,
            ],
            [
                'nom' => 'Banquier',
                'secteur' => 'Comptabilité',
                'description' => 'Professionnel des opérations bancaires et du conseil financier.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Conseiller clientèle',
                    'Chargé de clientèle',
                    'Analyste crédit',
                    'Gestionnaire de patrimoine',
                    'Directeur d\'agence'
                ]),
                'competences' => json_encode(['Produits bancaires', 'Analyse financière', 'Relation client', 'Crédit']),
                'salaire_moyen' => '200000-500000',
                'tags' => 'banque,finance,credit,conseiller,patrimoine',
                'is_cfpam' => true,
            ],

            // COMMERCE
            [
                'nom' => 'Agent en Douane et Transit',
                'secteur' => 'Commerce',
                'description' => 'Expert des procédures douanières pour l\'import-export.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Déclarant en douane',
                    'Agent de transit',
                    'Responsable import-export',
                    'Courtier en douane',
                    'Consultant commerce international'
                ]),
                'competences' => json_encode(['Réglementation douanière', 'Incoterms', 'Procédures import/export']),
                'salaire_moyen' => '150000-350000',
                'tags' => 'douane,transit,import,export,commerce-international',
                'is_cfpam' => true,
            ],
            [
                'nom' => 'Logisticien',
                'secteur' => 'Commerce',
                'description' => 'Gestionnaire de la chaîne logistique et du transport de marchandises.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Responsable logistique',
                    'Supply Chain Manager',
                    'Gestionnaire de stocks',
                    'Coordinateur transport',
                    'Chef d\'entrepôt'
                ]),
                'competences' => json_encode(['Gestion de stock', 'Transport', 'Supply Chain', 'ERP']),
                'salaire_moyen' => '150000-400000',
                'tags' => 'logistique,transport,stock,supply-chain,entrepot',
                'is_cfpam' => true,
            ],

            // INFORMATIQUE
            [
                'nom' => 'Technicien Maintenance Informatique',
                'secteur' => 'Informatique',
                'description' => 'Spécialiste du dépannage et de la maintenance des équipements informatiques.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Technicien de maintenance',
                    'Support informatique',
                    'Technicien helpdesk',
                    'Administrateur poste de travail',
                    'Technicien itinérant'
                ]),
                'competences' => json_encode(['Hardware', 'Software', 'Diagnostic', 'Windows/Linux', 'Dépannage']),
                'salaire_moyen' => '100000-250000',
                'tags' => 'maintenance,depannage,hardware,software,support',
                'is_cfpam' => true,
            ],
            [
                'nom' => 'Administrateur Réseau',
                'secteur' => 'Informatique',
                'description' => 'Responsable de l\'infrastructure réseau et de la sécurité informatique.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Administrateur réseau',
                    'Ingénieur réseau',
                    'Responsable sécurité',
                    'Architecte réseau',
                    'Consultant infrastructure'
                ]),
                'competences' => json_encode(['Cisco', 'Sécurité', 'Serveurs', 'Cloud', 'Virtualisation']),
                'salaire_moyen' => '200000-500000',
                'tags' => 'reseau,securite,serveur,cloud,infrastructure',
                'is_cfpam' => true,
            ],

            // ===== PROFILS POPULAIRES CAMEROUN =====
            [
                'nom' => 'Avocat',
                'secteur' => 'Droit',
                'description' => 'Professionnel du droit qui conseille et représente ses clients en justice.',
                'niveau_minimum' => 'Master',
                'debouches' => json_encode([
                    'Avocat au barreau',
                    'Juriste d\'entreprise',
                    'Conseiller juridique',
                    'Notaire',
                    'Magistrat'
                ]),
                'competences' => json_encode(['Droit civil', 'Droit pénal', 'Plaidoirie', 'Rédaction juridique']),
                'salaire_moyen' => '300000-1000000',
                'tags' => 'droit,justice,avocat,juridique,plaidoirie',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Médecin',
                'secteur' => 'Santé',
                'description' => 'Professionnel de santé qui diagnostique et traite les maladies.',
                'niveau_minimum' => 'Master',
                'debouches' => json_encode([
                    'Médecin généraliste',
                    'Médecin spécialiste',
                    'Chirurgien',
                    'Médecin hospitalier',
                    'Médecin de santé publique'
                ]),
                'competences' => json_encode(['Diagnostic', 'Anatomie', 'Pharmacologie', 'Chirurgie', 'Empathie']),
                'salaire_moyen' => '400000-1500000',
                'tags' => 'medecine,sante,hopital,diagnostic,soins',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Infirmier(ère)',
                'secteur' => 'Santé',
                'description' => 'Professionnel qui assure les soins aux patients et assiste les médecins.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Infirmier diplômé d\'État',
                    'Infirmier de bloc opératoire',
                    'Infirmier anesthésiste',
                    'Cadre de santé',
                    'Infirmier libéral'
                ]),
                'competences' => json_encode(['Soins infirmiers', 'Prise de sang', 'Accompagnement', 'Hygiène']),
                'salaire_moyen' => '150000-350000',
                'tags' => 'sante,soins,infirmier,hopital,medical',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Enseignant',
                'secteur' => 'Éducation',
                'description' => 'Professionnel de l\'éducation qui transmet des connaissances aux élèves.',
                'niveau_minimum' => 'Licence',
                'debouches' => json_encode([
                    'Professeur des écoles',
                    'Professeur de lycée',
                    'Formateur professionnel',
                    'Directeur d\'école',
                    'Inspecteur pédagogique'
                ]),
                'competences' => json_encode(['Pédagogie', 'Communication', 'Patience', 'Maîtrise disciplinaire']),
                'salaire_moyen' => '150000-400000',
                'tags' => 'enseignement,education,professeur,formation,pedagogie',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Journaliste',
                'secteur' => 'Communication',
                'description' => 'Professionnel qui collecte, vérifie et diffuse l\'information.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Journaliste reporter',
                    'Présentateur TV/Radio',
                    'Rédacteur en chef',
                    'Journaliste web',
                    'Correspondant étranger'
                ]),
                'competences' => json_encode(['Rédaction', 'Investigation', 'Interview', 'Éthique', 'Réseaux sociaux']),
                'salaire_moyen' => '100000-400000',
                'tags' => 'journalisme,media,redaction,investigation,communication',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Footballeur Professionnel',
                'secteur' => 'Sport',
                'description' => 'Athlète professionnel pratiquant le football à haut niveau.',
                'niveau_minimum' => 'BEPC',
                'debouches' => json_encode([
                    'Footballeur professionnel',
                    'Entraîneur',
                    'Agent de joueurs',
                    'Commentateur sportif',
                    'Préparateur physique'
                ]),
                'competences' => json_encode(['Technique', 'Endurance', 'Tactique', 'Esprit d\'équipe', 'Discipline']),
                'salaire_moyen' => '100000-50000000',
                'tags' => 'football,sport,athlete,competition,equipe',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Musicien',
                'secteur' => 'Art',
                'description' => 'Artiste qui compose, interprète ou produit de la musique.',
                'niveau_minimum' => 'BEPC',
                'debouches' => json_encode([
                    'Chanteur/Artiste solo',
                    'Musicien de studio',
                    'Compositeur',
                    'Producteur musical',
                    'DJ professionnel'
                ]),
                'competences' => json_encode(['Instrument', 'Solfège', 'Composition', 'Créativité', 'Scène']),
                'salaire_moyen' => '50000-5000000',
                'tags' => 'musique,artiste,chant,composition,production',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Chauffeur Professionnel',
                'secteur' => 'Transport',
                'description' => 'Conducteur professionnel de véhicules de transport.',
                'niveau_minimum' => 'BEPC',
                'debouches' => json_encode([
                    'Chauffeur de taxi',
                    'Chauffeur VTC',
                    'Chauffeur poids lourd',
                    'Chauffeur de direction',
                    'Moniteur auto-école'
                ]),
                'competences' => json_encode(['Conduite', 'Code de la route', 'Orientation', 'Service client']),
                'salaire_moyen' => '80000-200000',
                'tags' => 'transport,conduite,taxi,chauffeur,mobilite',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Ingénieur Génie Civil',
                'secteur' => 'Ingénierie',
                'description' => 'Concepteur et superviseur d\'ouvrages de construction.',
                'niveau_minimum' => 'Licence',
                'debouches' => json_encode([
                    'Ingénieur BTP',
                    'Chef de chantier',
                    'Conducteur de travaux',
                    'Bureau d\'études',
                    'Promoteur immobilier'
                ]),
                'competences' => json_encode(['AutoCAD', 'Calcul de structure', 'Gestion de chantier', 'Normes BTP']),
                'salaire_moyen' => '250000-700000',
                'tags' => 'construction,batiment,genie-civil,architecture,btp',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Pharmacien',
                'secteur' => 'Santé',
                'description' => 'Expert en médicaments qui conseille et délivre les traitements.',
                'niveau_minimum' => 'Master',
                'debouches' => json_encode([
                    'Pharmacien d\'officine',
                    'Pharmacien hospitalier',
                    'Délégué médical',
                    'Pharmacien industriel',
                    'Pharmacien inspecteur'
                ]),
                'competences' => json_encode(['Pharmacologie', 'Conseil patient', 'Gestion de stock', 'Réglementation']),
                'salaire_moyen' => '300000-800000',
                'tags' => 'pharmacie,medicament,sante,conseil,officine',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Cuisinier/Chef',
                'secteur' => 'Hôtellerie',
                'description' => 'Artiste culinaire créant des plats pour restaurants et hôtels.',
                'niveau_minimum' => 'BEPC',
                'debouches' => json_encode([
                    'Chef de cuisine',
                    'Chef pâtissier',
                    'Chef traiteur',
                    'Restaurateur',
                    'Consultant culinaire'
                ]),
                'competences' => json_encode(['Cuisine', 'Créativité', 'Gestion cuisine', 'Hygiène alimentaire']),
                'salaire_moyen' => '80000-400000',
                'tags' => 'cuisine,restaurant,gastronomie,chef,patisserie',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Policier/Gendarme',
                'secteur' => 'Sécurité',
                'description' => 'Agent de l\'ordre public assurant la sécurité des citoyens.',
                'niveau_minimum' => 'BAC',
                'debouches' => json_encode([
                    'Gardien de la paix',
                    'Officier de police',
                    'Gendarme',
                    'Commissaire',
                    'Police judiciaire'
                ]),
                'competences' => json_encode(['Droit pénal', 'Self-défense', 'Investigation', 'Sang-froid']),
                'salaire_moyen' => '150000-400000',
                'tags' => 'police,securite,gendarme,ordre,justice',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Entrepreneur',
                'secteur' => 'Business',
                'description' => 'Créateur et gestionnaire de sa propre entreprise.',
                'niveau_minimum' => 'BEPC',
                'debouches' => json_encode([
                    'Chef d\'entreprise',
                    'Startupper',
                    'Consultant indépendant',
                    'Franchisé',
                    'Investisseur'
                ]),
                'competences' => json_encode(['Leadership', 'Gestion', 'Vision', 'Négociation', 'Prise de risque']),
                'salaire_moyen' => '100000-10000000',
                'tags' => 'entrepreneuriat,business,startup,leadership,creation',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Agronome',
                'secteur' => 'Agriculture',
                'description' => 'Expert en sciences agricoles pour optimiser les productions.',
                'niveau_minimum' => 'Licence',
                'debouches' => json_encode([
                    'Ingénieur agronome',
                    'Conseiller agricole',
                    'Chef d\'exploitation',
                    'Chercheur agricole',
                    'Consultant en développement rural'
                ]),
                'competences' => json_encode(['Agronomie', 'Gestion exploitation', 'Environnement', 'Phytosanitaire']),
                'salaire_moyen' => '200000-500000',
                'tags' => 'agriculture,agronomie,exploitation,environnement,rural',
                'is_cfpam' => false,
            ],
            [
                'nom' => 'Architecte',
                'secteur' => 'Architecture',
                'description' => 'Concepteur de bâtiments et d\'espaces urbains.',
                'niveau_minimum' => 'Master',
                'debouches' => json_encode([
                    'Architecte DPLG',
                    'Architecte d\'intérieur',
                    'Urbaniste',
                    'Designer d\'espace',
                    'Maître d\'œuvre'
                ]),
                'competences' => json_encode(['AutoCAD', 'SketchUp', 'Dessin', 'Normes construction', 'Créativité']),
                'salaire_moyen' => '300000-800000',
                'tags' => 'architecture,design,construction,urbanisme,batiment',
                'is_cfpam' => false,
            ],
        ];

        foreach ($profiles as $profile) {
            DB::table('career_profiles')->insert([
                'nom' => $profile['nom'],
                'slug' => Str::slug($profile['nom']),
                'secteur' => $profile['secteur'], // Changé de 'category' à 'secteur'
                'description' => $profile['description'],
                'niveau_minimum' => $profile['niveau_minimum'],
                'debouches' => $profile['debouches'],
                'image_url' => null,
                'is_cfpam' => $profile['is_cfpam'] ?? false,
                'active' => true, // Changé de 'is_active' à 'active'
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}