<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\EliteUser;
use App\Models\Pack;
use App\Models\Partner;
use App\Models\PartnerPaymentInstallment;
use App\Models\PartnerPaymentPlan;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPack;
use App\Models\UserPaymentInstallment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class Phase5SuperUserCronAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SystemSetting::setTauxConversionFcfaPoints(1);
    }

    private function createPack(Category $category, string $nom, string $slug): Pack
    {
        return Pack::create([
            'category_id' => $category->id,
            'nom' => $nom,
            'slug' => $slug,
            'description' => 'Description ' . $nom,
            'niveau_requis' => 'BAC',
            'durees_disponibles' => ['6_mois', 'partenaire'],
            'prix_points' => 50,
            'prix_reel_fcfa' => 455000,
            'active' => true,
        ]);
    }

    public function test_cron_command_marks_late_installments_and_notifies_partner()
    {
        Mail::fake();

        $category = Category::create(['nom' => 'Informatique', 'slug' => 'info-test']);
        $partner = Partner::create([
            'nom' => 'CFPAM Douala',
            'email' => 'cfpam.douala@test.com',
            'telephone' => '699000001',
            'password' => bcrypt('password123'),
            'code_partenaire' => 'CFPAM-DLA',
        ]);

        $pack = $this->createPack($category, 'Pack Informatique', 'pack-info-test');

        $user = EliteUser::create([
            'nom' => 'Late',
            'prenom' => 'Learner',
            'telephone' => '690000099',
            'email' => 'late.learner@test.com',
            'dernier_diplome' => 'Bac',
            'ville' => 'Douala',
            'password' => bcrypt('password123'),
            'partner_id' => $partner->id,
            'formation_status' => 'active',
            'referral_code' => 'ELLATE01',
        ]);

        $userPack = UserPack::create([
            'user_id' => $user->id,
            'pack_id' => $pack->id,
            'duree_choisie' => 'partenaire',
            'prix_paye' => 0,
            'statut' => 'actif',
            'date_achat' => now(),
        ]);

        $plan = PartnerPaymentPlan::create([
            'partner_id' => $partner->id,
            'pack_id' => $pack->id,
            'nom' => 'Plan CFPAM 5 Tranches',
            'date_fin_formation' => now()->addMonths(6),
            'active' => true,
        ]);

        $planInstallment = PartnerPaymentInstallment::create([
            'plan_id' => $plan->id,
            'libelle' => 'Tranche 1 - Inscription',
            'montant_fcfa' => 10000,
            'delai_jours' => 0,
            'ordre' => 1,
        ]);

        $userInstallment = UserPaymentInstallment::create([
            'user_pack_id' => $userPack->id,
            'plan_installment_id' => $planInstallment->id,
            'montant_fcfa' => 10000,
            'due_at' => now()->subDays(2), // Overdue
            'statut' => 'en_attente',
        ]);

        $exitCode = Artisan::call('partners:check-deadlines');
        $this->assertEquals(0, $exitCode);

        $userInstallment->refresh();
        $this->assertEquals('en_retard', $userInstallment->statut);

        $user->refresh();
        $this->assertEquals('failed', $user->formation_status);
    }

    public function test_webhook_with_valid_signature_processes_deposit()
    {
        config(['services.moneyfusion.webhook_secret' => 'test_secret_key_12345']);

        $user = EliteUser::create([
            'nom' => 'WebHook',
            'prenom' => 'User',
            'telephone' => '690000100',
            'email' => 'webhook@test.com',
            'dernier_diplome' => 'Bac',
            'ville' => 'Douala',
            'password' => bcrypt('password123'),
            'solde_points' => 0,
            'referral_code' => 'ELWH001',
        ]);

        $reference = 'TX-TEST-WEBHOOK-99';
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'depot',
            'montant_fcfa' => 10000,
            'points' => 10000,
            'reference' => $reference,
            'description' => 'Dépôt test webhook',
            'statut' => 'en_attente',
        ]);

        $payload = json_encode([
            'statut' => 'paid',
            'personal_Info' => [
                [
                    'transactionRef' => $reference,
                    'userId' => $user->id,
                ]
            ]
        ]);

        $signature = hash_hmac('sha256', $payload, 'test_secret_key_12345');

        $response = $this->call(
            'POST',
            '/api/payment/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_MONEYFUSION_SIGNATURE' => $signature,
            ],
            $payload
        );

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $user->refresh();
        $this->assertEquals(10000, $user->solde_points);
    }

    public function test_webhook_with_invalid_signature_is_rejected()
    {
        config(['services.moneyfusion.webhook_secret' => 'test_secret_key_12345']);

        $payload = json_encode([
            'statut' => 'paid',
            'personal_Info' => [
                ['transactionRef' => 'TX-FAKE', 'userId' => 1]
            ]
        ]);

        $response = $this->call(
            'POST',
            '/api/payment/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_MONEYFUSION_SIGNATURE' => 'invalid_signature_hash',
            ],
            $payload
        );

        $response->assertStatus(401)
            ->assertJson(['success' => false, 'message' => 'Signature invalide']);
    }

    public function test_admin_dashboard_displays_consolidated_multi_centres_stats()
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
        ]);

        Partner::create([
            'nom' => 'CFPAM Yaounde',
            'email' => 'cfpam.yde@test.com',
            'telephone' => '699000002',
            'password' => bcrypt('password123'),
            'code_partenaire' => 'CFPAM-YDE',
            'active' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200)
            ->assertSee('Supervision Multi-Centres CFPAM', false)
            ->assertSee('CFPAM Yaounde')
            ->assertSee('CFPAM-YDE');
    }

    public function test_admin_can_create_and_toggle_partner_centre()
    {
        $admin = User::create([
            'name' => 'Super Admin 2',
            'email' => 'admin2@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($admin)->post('/admin/partners', [
            'nom' => 'CFPAM Bafoussam',
            'code_partenaire' => 'CFPAM-BAF',
            'email' => 'cfpam.baf@test.com',
            'telephone' => '699000003',
            'password' => 'secret1234',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('partners', [
            'code_partenaire' => 'CFPAM-BAF',
            'nom' => 'CFPAM Bafoussam',
            'active' => 1,
        ]);

        $partner = Partner::where('code_partenaire', 'CFPAM-BAF')->first();

        $toggleResponse = $this->actingAs($admin)->patch("/admin/partners/{$partner->id}/toggle");
        $toggleResponse->assertRedirect();

        $partner->refresh();
        $this->assertEquals(0, $partner->active);
    }
}
