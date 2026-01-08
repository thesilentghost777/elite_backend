<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackProfileSeeder extends Seeder
{
    public function run()
    {
        $associations = [
            // Pack 1: Bureautique
            ['pack_id' => 1, 'profile_id' => 1, 'priorite' => 100], // Secrétaire Bureautique
            ['pack_id' => 1, 'profile_id' => 2, 'priorite' => 80],  // Assistant(e) de Direction
            ['pack_id' => 1, 'profile_id' => 3, 'priorite' => 70],  // Secrétaire Comptable

            // Pack 2: Assistante de Direction
            ['pack_id' => 2, 'profile_id' => 2, 'priorite' => 100], // Assistant(e) de Direction
            ['pack_id' => 2, 'profile_id' => 1, 'priorite' => 80],  // Secrétaire Bureautique
            ['pack_id' => 2, 'profile_id' => 12, 'priorite' => 60], // Responsable RH

            // Pack 3: Secrétaire Comptable
            ['pack_id' => 3, 'profile_id' => 3, 'priorite' => 100], // Secrétaire Comptable
            ['pack_id' => 3, 'profile_id' => 13, 'priorite' => 90], // Comptable
            ['pack_id' => 3, 'profile_id' => 1, 'priorite' => 70],  // Secrétaire Bureautique

            // Pack 4: Infographie
            ['pack_id' => 4, 'profile_id' => 4, 'priorite' => 100], // Infographe
            ['pack_id' => 4, 'profile_id' => 6, 'priorite' => 80],  // Graphiste 3D
            ['pack_id' => 4, 'profile_id' => 9, 'priorite' => 60],  // Spécialiste Marketing Digital

            // Pack 5: Montage Vidéo
            ['pack_id' => 5, 'profile_id' => 5, 'priorite' => 100], // Monteur Vidéo
            ['pack_id' => 5, 'profile_id' => 4, 'priorite' => 70],  // Infographe
            ['pack_id' => 5, 'profile_id' => 9, 'priorite' => 50],  // Spécialiste Marketing Digital

            // Pack 6: Graphisme 2D/3D
            ['pack_id' => 6, 'profile_id' => 6, 'priorite' => 100], // Graphiste 3D
            ['pack_id' => 6, 'profile_id' => 4, 'priorite' => 90],  // Infographe
            ['pack_id' => 6, 'profile_id' => 8, 'priorite' => 60],  // Décorateur(trice)

            // Pack 7: Esthétique/Cosmétique
            ['pack_id' => 7, 'profile_id' => 7, 'priorite' => 100], // Esthéticien(ne)

            // Pack 8: Décoration
            ['pack_id' => 8, 'profile_id' => 8, 'priorite' => 100], // Décorateur(trice)
            ['pack_id' => 8, 'profile_id' => 4, 'priorite' => 60],  // Infographe

            // Pack 9: Marketing Digital
            ['pack_id' => 9, 'profile_id' => 9, 'priorite' => 100], // Spécialiste Marketing Digital
            ['pack_id' => 9, 'profile_id' => 10, 'priorite' => 70], // Développeur Web et Mobile
            ['pack_id' => 9, 'profile_id' => 4, 'priorite' => 50],  // Infographe

            // Pack 10: Développement Web et App
            ['pack_id' => 10, 'profile_id' => 10, 'priorite' => 100], // Développeur Web et Mobile
            ['pack_id' => 10, 'profile_id' => 9, 'priorite' => 70],   // Spécialiste Marketing Digital
            ['pack_id' => 10, 'profile_id' => 17, 'priorite' => 60],  // Technicien Maintenance Informatique

            // Pack 11: Gestion des Projets
            ['pack_id' => 11, 'profile_id' => 11, 'priorite' => 100], // Chef de Projet
            ['pack_id' => 11, 'profile_id' => 2, 'priorite' => 70],   // Assistant(e) de Direction
            ['pack_id' => 11, 'profile_id' => 31, 'priorite' => 80],  // Entrepreneur

            // Pack 12: Gestion des Ressources Humaines
            ['pack_id' => 12, 'profile_id' => 12, 'priorite' => 100], // Responsable RH
            ['pack_id' => 12, 'profile_id' => 2, 'priorite' => 70],   // Assistant(e) de Direction
            ['pack_id' => 12, 'profile_id' => 11, 'priorite' => 60],  // Chef de Projet

            // Pack 13: Comptabilité Informatisée et Gestion
            ['pack_id' => 13, 'profile_id' => 13, 'priorite' => 100], // Comptable
            ['pack_id' => 13, 'profile_id' => 3, 'priorite' => 90],   // Secrétaire Comptable
            ['pack_id' => 13, 'profile_id' => 14, 'priorite' => 70],  // Banquier

            // Pack 14: Banque et Finance
            ['pack_id' => 14, 'profile_id' => 14, 'priorite' => 100], // Banquier
            ['pack_id' => 14, 'profile_id' => 13, 'priorite' => 80],  // Comptable

            // Pack 15: Douane et Transit
            ['pack_id' => 15, 'profile_id' => 15, 'priorite' => 100], // Agent en Douane et Transit
            ['pack_id' => 15, 'profile_id' => 16, 'priorite' => 80],  // Logisticien

            // Pack 16: Logistique/Transport
            ['pack_id' => 16, 'profile_id' => 16, 'priorite' => 100], // Logisticien
            ['pack_id' => 16, 'profile_id' => 15, 'priorite' => 80],  // Agent en Douane et Transit
            ['pack_id' => 16, 'profile_id' => 26, 'priorite' => 60],  // Chauffeur Professionnel

            // Pack 17: Maintenance Informatique
            ['pack_id' => 17, 'profile_id' => 17, 'priorite' => 100], // Technicien Maintenance Informatique
            ['pack_id' => 17, 'profile_id' => 18, 'priorite' => 80],  // Administrateur Réseau

            // Pack 18: Réseau Informatique
            ['pack_id' => 18, 'profile_id' => 18, 'priorite' => 100], // Administrateur Réseau
            ['pack_id' => 18, 'profile_id' => 17, 'priorite' => 80],  // Technicien Maintenance Informatique
            ['pack_id' => 18, 'profile_id' => 10, 'priorite' => 60],  // Développeur Web et Mobile

            // Pack 19: Sculpture de Fruits et Jus
            ['pack_id' => 19, 'profile_id' => 29, 'priorite' => 100], // Cuisinier/Chef
            ['pack_id' => 19, 'profile_id' => 31, 'priorite' => 70],  // Entrepreneur

            // Pack 20: Élevage Professionnel
            ['pack_id' => 20, 'profile_id' => 32, 'priorite' => 100], // Agronome
            ['pack_id' => 20, 'profile_id' => 31, 'priorite' => 70],  // Entrepreneur

            // Pack 21: Fabrication Produits Cosmétiques
            ['pack_id' => 21, 'profile_id' => 7, 'priorite' => 100],  // Esthéticien(ne)
            ['pack_id' => 21, 'profile_id' => 31, 'priorite' => 80],  // Entrepreneur

            // Pack 22: Fabrication Produits Ménagers
            ['pack_id' => 22, 'profile_id' => 31, 'priorite' => 100], // Entrepreneur
        ];

        foreach ($associations as $association) {
            DB::table('pack_profiles')->insert([
                'pack_id' => $association['pack_id'],
                'profile_id' => $association['profile_id'],
                'priorite' => $association['priorite'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}