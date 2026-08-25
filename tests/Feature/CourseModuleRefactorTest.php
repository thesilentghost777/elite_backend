<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\EliteUser;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Pack;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\UserPack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CourseModuleRefactorTest extends TestCase
{
    use RefreshDatabase;

    private EliteUser $user;
    private Pack $pack;
    private Module $module1NoQuiz;
    private Module $module2WithQuiz;
    private Module $module3;
    private Lesson $m1Lesson1;
    private Lesson $m1Lesson2;
    private Lesson $m2Lesson1;
    private Quiz $m2Quiz;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'nom' => 'Informatique',
            'slug' => 'informatique',
            'icone' => 'code',
            'ordre' => 1,
            'active' => true,
        ]);

        $this->pack = Pack::create([
            'category_id' => $category->id,
            'nom' => 'Fullstack Web',
            'slug' => 'fullstack-web',
            'description' => 'Formation complète',
            'niveau_requis' => 'BAC',
            'durees_disponibles' => ['6_mois'],
            'prix_points' => 50,
            'prix_reel_fcfa' => 250000,
            'active' => true,
        ]);

        // Module 1: No Quiz
        $this->module1NoQuiz = Module::create([
            'pack_id' => $this->pack->id,
            'nom' => 'Module 1 : Bases Web (Sans Quiz)',
            'description' => 'Introduction sans quiz',
            'type' => 'theorique',
            'ordre' => 1,
            'note_passage' => 14,
            'note_parrainage' => 10,
            'parrainages_requis' => 4,
            'active' => true,
        ]);

        $this->m1Lesson1 = Lesson::create([
            'module_id' => $this->module1NoQuiz->id,
            'titre' => 'Leçon 1.1 : HTML Structure',
            'contenu_texte' => 'Introduction HTML',
            'url_video_explication' => 'https://example.com/video1.mp4',
            'url_video_pratique' => 'https://example.com/practice1.mp4',
            'duree_minutes' => 15,
            'ordre' => 1,
            'active' => true,
        ]);

        $this->m1Lesson2 = Lesson::create([
            'module_id' => $this->module1NoQuiz->id,
            'titre' => 'Leçon 1.2 : CSS Styling',
            'contenu_texte' => 'Introduction CSS',
            'url_video_explication' => 'https://example.com/video2.mp4',
            'duree_minutes' => 20,
            'ordre' => 2,
            'active' => true,
        ]);

        // Module 2: With Quiz
        $this->module2WithQuiz = Module::create([
            'pack_id' => $this->pack->id,
            'nom' => 'Module 2 : JavaScript (Avec Quiz)',
            'description' => 'JS Moderne et Quiz 10 Questions',
            'type' => 'pratique',
            'ordre' => 2,
            'note_passage' => 14,
            'note_parrainage' => 10,
            'parrainages_requis' => 3,
            'active' => true,
        ]);

        $this->m2Lesson1 = Lesson::create([
            'module_id' => $this->module2WithQuiz->id,
            'titre' => 'Leçon 2.1 : Fonctions JS',
            'contenu_texte' => 'Les fonctions fléchées',
            'duree_minutes' => 25,
            'ordre' => 1,
            'active' => true,
        ]);

        // 10 Questions for Module 2 Quiz
        $this->m2Quiz = Quiz::create([
            'module_id' => $this->module2WithQuiz->id,
            'titre' => 'Quiz Synthèse Module 2',
            'description' => 'Évaluation 10 questions',
            'duree_minutes' => 20,
            'active' => true,
        ]);

        for ($i = 1; $i <= 10; $i++) {
            $question = QuizQuestion::create([
                'quiz_id' => $this->m2Quiz->id,
                'enonce' => "Question {$i} : JS Concept",
                'type' => 'qcm',
                'points' => 2,
                'ordre' => $i,
                'active' => true,
            ]);

            QuizAnswer::create([
                'question_id' => $question->id,
                'texte' => 'Bonne réponse',
                'est_correcte' => true,
                'ordre' => 1,
            ]);

            QuizAnswer::create([
                'question_id' => $question->id,
                'texte' => 'Mauvaise réponse',
                'est_correcte' => false,
                'ordre' => 2,
            ]);
        }

        // Module 3: Advanced
        $this->module3 = Module::create([
            'pack_id' => $this->pack->id,
            'nom' => 'Module 3 : Backend Laravel',
            'description' => 'Architecture API',
            'type' => 'pratique',
            'ordre' => 3,
            'note_passage' => 14,
            'note_parrainage' => 10,
            'parrainages_requis' => 4,
            'active' => true,
        ]);

        $this->user = EliteUser::create([
            'nom' => 'Test',
            'prenom' => 'User',
            'telephone' => '690111222',
            'email' => 'student@test.com',
            'dernier_diplome' => 'Licence',
            'ville' => 'Yaoundé',
            'password' => bcrypt('secret123'),
            'solde_points' => 200,
            'referral_code' => 'STUDENT01',
        ]);

        // Purchase pack for user
        UserPack::create([
            'user_id' => $this->user->id,
            'pack_id' => $this->pack->id,
            'duree_choisie' => '6_mois',
            'prix_paye' => 50,
            'progression' => 0,
            'statut' => 'actif',
            'date_achat' => now(),
            'date_expiration' => now()->addMonths(6),
        ]);
    }

    public function test_pack_modules_returns_correct_hierarchy_and_initial_locks()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/packs/{$this->pack->id}/modules");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $modules = $response->json('data');
        $this->assertCount(3, $modules);

        // Module 1 (First module) is unlocked automatically
        $this->assertTrue($modules[0]['is_unlocked']);
        $this->assertFalse($modules[0]['has_quiz']);
        $this->assertFalse($modules[0]['is_completed']);
        $this->assertEquals(2, $modules[0]['lessons_count']);

        // Module 2 is initially locked
        $this->assertFalse($modules[1]['is_unlocked']);
        $this->assertTrue($modules[1]['has_quiz']);

        // Module 3 is locked
        $this->assertFalse($modules[2]['is_unlocked']);
    }

    public function test_module_without_quiz_is_automatically_completed_and_unlocks_next_module_when_lessons_done()
    {
        Sanctum::actingAs($this->user);

        // Complete lesson 1 of module 1
        $res1 = $this->postJson("/api/lessons/{$this->m1Lesson1->id}/complete");
        $res1->assertStatus(200)
            ->assertJsonPath('data.all_lessons_completed', false)
            ->assertJsonPath('data.module_completed', false);

        // Module 2 is still locked
        $packCheck = $this->getJson("/api/packs/{$this->pack->id}/modules");
        $this->assertFalse($packCheck->json('data.1.is_unlocked'));

        // Complete last lesson of module 1
        $res2 = $this->postJson("/api/lessons/{$this->m1Lesson2->id}/complete");
        $res2->assertStatus(200)
            ->assertJsonPath('data.all_lessons_completed', true)
            ->assertJsonPath('data.module_completed', true)
            ->assertJsonPath('data.next_module_unlocked', true);

        // Now Module 1 is completed and Module 2 is unlocked
        $packCheck2 = $this->getJson("/api/packs/{$this->pack->id}/modules");
        $this->assertTrue($packCheck2->json('data.0.is_completed'));
        $this->assertTrue($packCheck2->json('data.1.is_unlocked'));
    }

    public function test_module_with_quiz_requires_passing_grade_to_complete_and_unlock_next_module()
    {
        Sanctum::actingAs($this->user);

        // First complete Module 1 so Module 2 is unlocked
        $this->postJson("/api/lessons/{$this->m1Lesson1->id}/complete");
        $this->postJson("/api/lessons/{$this->m1Lesson2->id}/complete");

        // Complete lesson in Module 2
        $resL = $this->postJson("/api/lessons/{$this->m2Lesson1->id}/complete");
        $resL->assertStatus(200)
            ->assertJsonPath('data.all_lessons_completed', true)
            ->assertJsonPath('data.module_completed', false) // because Quiz is required
            ->assertJsonPath('data.has_quiz', true);

        // Module 3 is still locked
        $check = $this->getJson("/api/packs/{$this->pack->id}/modules");
        $this->assertFalse($check->json('data.2.is_unlocked'));

        // Submit quiz with passing score (10/10 correct => 20/20)
        $responses = [];
        foreach ($this->m2Quiz->questions as $q) {
            $correctAnswer = $q->answers()->where('est_correcte', true)->first();
            $responses[] = [
                'question_id' => $q->id,
                'answer_id' => $correctAnswer->id,
            ];
        }

        $resQuiz = $this->postJson("/api/quiz/{$this->m2Quiz->id}/submit", [
            'responses' => $responses,
        ]);

        $resQuiz->assertStatus(200)
            ->assertJsonPath('data.reussi', true)
            ->assertJsonPath('data.palier_atteint', 10)
            ->assertJsonPath('data.module_suivant_debloque', true);

        // Now Module 2 is completed and Module 3 is unlocked
        $check2 = $this->getJson("/api/packs/{$this->pack->id}/modules");
        $this->assertTrue($check2->json('data.1.is_completed'));
        $this->assertTrue($check2->json('data.2.is_unlocked'));
    }

    public function test_quiz_can_be_unlocked_by_referral_if_score_is_above_referral_threshold()
    {
        Sanctum::actingAs($this->user);

        // Complete Module 1
        $this->postJson("/api/lessons/{$this->m1Lesson1->id}/complete");
        $this->postJson("/api/lessons/{$this->m1Lesson2->id}/complete");

        // Complete Module 2 lesson
        $this->postJson("/api/lessons/{$this->m2Lesson1->id}/complete");

        // Submit quiz with 5/10 correct (10/20 => note_parrainage reached, but note_passage=14 not reached)
        $responses = [];
        $i = 0;
        foreach ($this->m2Quiz->questions as $q) {
            $i++;
            if ($i <= 5) {
                $ans = $q->answers()->where('est_correcte', true)->first();
            } else {
                $ans = $q->answers()->where('est_correcte', false)->first();
            }
            $responses[] = [
                'question_id' => $q->id,
                'answer_id' => $ans->id,
            ];
        }

        $resQuiz = $this->postJson("/api/quiz/{$this->m2Quiz->id}/submit", [
            'responses' => $responses,
        ]);

        $resQuiz->assertStatus(200)
            ->assertJsonPath('data.reussi', false)
            ->assertJsonPath('data.options.parrainage_requis', true);

        // Module 3 is still locked
        $check = $this->getJson("/api/packs/{$this->pack->id}/modules");
        $this->assertFalse($check->json('data.2.is_unlocked'));

        // Simuler les 3 parrainages requis pour le module 2
        for ($k = 0; $k < 3; $k++) {
            $refUser = EliteUser::create([
                'nom' => "Referral{$k}",
                'prenom' => 'User',
                'telephone' => "69011123{$k}",
                'email' => "ref{$k}@test.com",
                'dernier_diplome' => 'BAC',
                'ville' => 'Douala',
                'password' => bcrypt('secret123'),
                'referral_code' => "REFUSER{$k}",
                'referred_by' => $this->user->id,
            ]);
            \App\Models\ReferralHistory::create([
                'parrain_id' => $this->user->id,
                'filleul_id' => $refUser->id,
                'points_gagnes' => 10,
                'created_at' => now(),
            ]);
        }

        // Unlock Module 2 via referral
        $resUnlock = $this->postJson("/api/modules/{$this->module2WithQuiz->id}/unlock-by-referral");
        $resUnlock->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.unlocked', true);

        // Now Module 3 is unlocked
        $check2 = $this->getJson("/api/packs/{$this->pack->id}/modules");
        $this->assertTrue($check2->json('data.2.is_unlocked'));
    }
}
