<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ============================================
        // UTILISATEURS & AUTHENTIFICATION
        // ============================================

        // Table des utilisateurs Elite (apprenants)
        Schema::create('elite_users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone')->unique();
            $table->string('email')->unique()->nullable();
            $table->enum('dernier_diplome', ['BEPC', 'Probatoire', 'BAC', 'Licence', 'Master']);
            $table->string('ville');
            $table->string('password');
            $table->string('referral_code')->unique(); // Code de parrainage personnel
            $table->string('referred_by')->nullable(); // Code du parrain
            $table->decimal('solde_points', 10, 2)->default(0);
            $table->boolean('correspondence_completed')->default(false);
            $table->boolean('profile_chosen')->default(false);
            $table->enum('parcours_mode', ['en_ligne', 'presentiel', 'externe'])->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

    

        // ============================================
        // SYSTÈME DE PARRAINAGE
        // ============================================

        Schema::create('referral_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parrain_id')->constrained('elite_users')->onDelete('cascade');
            $table->foreignId('filleul_id')->constrained('elite_users')->onDelete('cascade');
            $table->integer('points_gagnes')->default(1);
            $table->timestamps();
        });

        // ============================================
        // FORMULAIRE DE CORRESPONDANCE
        // ============================================

        Schema::create('correspondence_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // moral, social, professionnel, personnel
            $table->string('description')->nullable();
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        Schema::create('correspondence_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('correspondence_categories')->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['qcm', 'oui_non', 'echelle'])->default('qcm');
            $table->integer('ordre')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('correspondence_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('correspondence_questions')->onDelete('cascade');
            $table->string('texte');
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        // ============================================
        // PROFILS MÉTIERS
        // ============================================

        Schema::create('career_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->string('secteur'); // Secrétariat, Audiovisuel, Digital, etc.
            $table->json('debouches')->nullable(); // Liste des débouchés
            $table->enum('niveau_minimum', ['BEPC', 'Probatoire', 'BAC', 'Licence', 'Master'])->default('BEPC');
            $table->boolean('is_cfpam')->default(false); // Profil CFPAM ou populaire
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Matching entre réponses et profils (pondération)
        Schema::create('profile_matchings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained('correspondence_answers')->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('career_profiles')->onDelete('cascade');
            $table->integer('poids')->default(1); // Pondération
            $table->timestamps();

            $table->unique(['answer_id', 'profile_id']);
        });

        // Réponses des utilisateurs au formulaire
        Schema::create('user_correspondences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('elite_users')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('correspondence_questions')->onDelete('cascade');
            $table->foreignId('answer_id')->constrained('correspondence_answers')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'question_id']);
        });

        // Résultats de correspondance (top 5 profils)
        Schema::create('user_profile_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('elite_users')->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('career_profiles')->onDelete('cascade');
            $table->decimal('score', 5, 2); // Score en pourcentage
            $table->integer('rang'); // 1 à 5
            $table->timestamps();

            $table->unique(['user_id', 'profile_id']);
        });

        // Choix final du profil par l'utilisateur
        Schema::create('user_profile_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('elite_users')->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('career_profiles')->onDelete('cascade');
            $table->boolean('from_recommendations')->default(true); // A choisi parmi les 5 ou non
            $table->timestamps();

            $table->unique('user_id');
        });

        // ============================================
        // ROADMAPS
        // ============================================

        Schema::create('roadmaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('career_profiles')->onDelete('cascade');
            $table->enum('niveau_depart', ['BEPC', 'Probatoire', 'BAC', 'Licence', 'Master']);
            $table->string('titre');
            $table->text('description')->nullable();
            $table->integer('duree_estimee_mois')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['profile_id', 'niveau_depart']);
        });

        Schema::create('roadmap_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_id')->constrained('roadmaps')->onDelete('cascade');
            $table->string('titre');
            $table->text('description');
            $table->integer('ordre');
            $table->integer('duree_semaines')->nullable();
            $table->enum('type', ['formation', 'certification', 'stage', 'experience', 'diplome', 'projet']);
            $table->foreignId('pack_recommande_id')->nullable(); // Lien vers un pack si applicable
            $table->boolean('obligatoire')->default(true);
            $table->timestamps();
        });

        // ============================================
        // CATÉGORIES & PACKS DE FORMATION
        // ============================================

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('couleur')->nullable(); // Code couleur hex
            $table->integer('ordre')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->enum('niveau_requis', ['BEPC', 'Probatoire', 'BAC', 'Licence', 'Master']);
            $table->json('durees_disponibles');
            $table->json('diplomes_possibles')->nullable(); // ['AQP', 'CQP', 'DQP', 'BTS']
            $table->integer('prix_points')->default(50); // Prix en points
            $table->json('debouches')->nullable();
            $table->integer('ordre')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Liaison packs <-> profils recommandés
        Schema::create('pack_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_id')->constrained('packs')->onDelete('cascade');
            $table->foreignId('profile_id')->constrained('career_profiles')->onDelete('cascade');
            $table->integer('priorite')->default(0); // Plus haut = plus recommandé
            $table->timestamps();

            $table->unique(['pack_id', 'profile_id']);
        });

        // ============================================
        // STRUCTURE DES COURS
        // ============================================

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_id')->constrained('packs')->onDelete('cascade');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->enum('type', ['theorique', 'pratique'])->default('theorique');
            $table->integer('ordre')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->integer('ordre')->default(0);
            $table->integer('note_passage')->default(14); // Note minimale sur 20
            $table->integer('note_parrainage')->default(10); // Entre cette note et note_passage, parrainage requis
            $table->integer('parrainages_requis')->default(4); // Nombre de parrainages si note entre 10 et 14
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            $table->string('titre');
            $table->text('contenu_texte')->nullable();
            $table->string('url_web')->nullable(); // Pour webview
            $table->string('url_video')->nullable();
            $table->integer('ordre')->default(0);
            $table->integer('duree_minutes')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // ============================================
        // QUIZ & ÉVALUATIONS
        // ============================================

        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->integer('note_totale')->default(20);
            $table->integer('duree_minutes')->default(30);
            $table->integer('ordre')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->text('enonce');
            $table->string('image_url')->nullable();
            $table->enum('type', ['qcm', 'vrai_faux'])->default('qcm');
            $table->text('explication')->nullable();
            $table->integer('ordre')->default(0);
            $table->integer('points')->default(1);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('quiz_questions')->onDelete('cascade');
            $table->text('texte');
            $table->boolean('est_correcte')->default(false);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });

        // ============================================
        // PROGRESSION UTILISATEUR
        // ============================================

        Schema::create('user_packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('elite_users')->onDelete('cascade');
            $table->foreignId('pack_id')->constrained('packs')->onDelete('cascade');
            $table->string('duree_choisie'); // '3 mois', '6 mois', etc.
            $table->decimal('prix_paye', 10, 2);
            $table->enum('statut', ['actif', 'complete', 'expire'])->default('actif');
            $table->decimal('progression', 5, 2)->default(0); // Pourcentage
            $table->timestamp('date_achat');
            $table->timestamp('date_expiration')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'pack_id']);
        });

        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('elite_users')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->integer('temps_passe_secondes')->default(0);
            $table->timestamp('date_completion')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']);
        });

        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('elite_users')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quizzes')->onDelete('cascade');
            $table->decimal('note', 5, 2);
            $table->integer('total_questions');
            $table->integer('bonnes_reponses');
            $table->boolean('reussi')->default(false);
            $table->json('reponses_utilisateur')->nullable();
            $table->integer('tentative')->default(1);
            $table->enum('action_requise', ['aucune', 'parrainage', 'recommencer'])->default('aucune');
            $table->integer('parrainages_effectues')->default(0);
            $table->timestamps();
        });

        Schema::create('chapter_unlocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('elite_users')->onDelete('cascade');
            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            $table->enum('unlock_method', ['score', 'parrainage', 'admin'])->default('score');
            $table->timestamps();

            $table->unique(['user_id', 'chapter_id']);
        });

        // ============================================
        // PAIEMENTS & TRANSACTIONS
        // ============================================

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('cash_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('montant_fcfa', 10, 2);
            $table->integer('points');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('elite_users')->onDelete('set null');
            $table->foreignId('used_by')->nullable()->constrained('elite_users')->onDelete('set null');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('elite_users')->onDelete('cascade');
            $table->enum('type', ['depot', 'achat_pack', 'parrainage', 'transfert_envoi', 'transfert_recu', 'code_caisse', 'bourse']);
            $table->decimal('montant_fcfa', 10, 2)->nullable();
            $table->decimal('points', 10, 2);
            $table->string('reference')->unique();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('statut', ['en_attente', 'complete', 'echoue', 'annule'])->default('complete');
            $table->timestamps();
        });

        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('elite_users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('elite_users')->onDelete('cascade');
            $table->decimal('points', 10, 2);
            $table->text('motif')->nullable();
            $table->foreignId('transaction_envoi_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('transaction_recu_id')->constrained('transactions')->onDelete('cascade');
            $table->timestamps();
        });

        // ============================================
        // FAQ
        // ============================================

        Schema::create('faq_categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('icone')->nullable();
            $table->integer('ordre')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('faq_categories')->onDelete('cascade');
            $table->text('question');
            $table->text('reponse');
            $table->integer('ordre')->default(0);
            $table->integer('vues')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // ============================================
        // FONCTIONNALITÉS EN DÉVELOPPEMENT (placeholders)
        // ============================================

        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->string('entreprise');
            $table->string('ville');
            $table->string('type_contrat');
            $table->decimal('salaire_min', 10, 2)->nullable();
            $table->decimal('salaire_max', 10, 2)->nullable();
            $table->date('date_limite')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_telephone')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('concours', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->string('organisateur');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->date('date_limite_inscription');
            $table->text('conditions')->nullable();
            $table->string('lien_inscription')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Ajouter la contrainte foreign key pour roadmap_steps après création de packs
        Schema::table('roadmap_steps', function (Blueprint $table) {
            $table->foreign('pack_recommande_id')->references('id')->on('packs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('roadmap_steps', function (Blueprint $table) {
            $table->dropForeign(['pack_recommande_id']);
        });

        $tables = [
            'concours',
            'job_offers',
            'faqs',
            'faq_categories',
            'transfers',
            'transactions',
            'cash_codes',
            'system_settings',
            'chapter_unlocks',
            'quiz_results',
            'lesson_progress',
            'user_packs',
            'quiz_answers',
            'quiz_questions',
            'quizzes',
            'lessons',
            'chapters',
            'modules',
            'pack_profiles',
            'packs',
            'categories',
            'roadmap_steps',
            'roadmaps',
            'user_profile_choices',
            'user_profile_results',
            'user_correspondences',
            'profile_matchings',
            'career_profiles',
            'correspondence_answers',
            'correspondence_questions',
            'correspondence_categories',
            'referral_history',
            'elite_users',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
