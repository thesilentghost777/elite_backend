<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\EliteUser;
use App\Models\Pack;
use App\Models\Partner;
use App\Models\PartnerPaymentPlan;
use App\Models\PartnerPaymentInstallment;
use App\Models\SystemSetting;
use App\Models\UserPack;
use App\Models\UserPaymentInstallment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WalletAndInstallmentsTest extends TestCase
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

    public function test_wallet_balance_endpoint_returns_dynamic_pricing_data()
    {
        SystemSetting::setTauxConversionFcfaPoints(650);

        $user = EliteUser::create([
            'nom' => 'Doe',
            'prenom' => 'John',
            'telephone' => '690000001',
            'email' => 'john@test.com',
            'dernier_diplome' => 'Bac',
            'ville' => 'Douala',
            'password' => bcrypt('password123'),
            'solde_points' => 100,
            'referral_code' => 'EL123456',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/wallet/balance');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'points' => 100,
                    'equivalent_fcfa' => 65000,
                    'solde_fcfa' => 65000,
                    'taux_conversion' => 650,
                ],
            ]);
    }

    public function test_user_can_fetch_their_installments()
    {
        $category = Category::create([
            'nom' => 'Informatique',
            'slug' => 'informatique',
        ]);

        $partner = Partner::create([
            'nom' => 'CFPAM Centre 1',
            'email' => 'partner1@test.com',
            'telephone' => '699999991',
            'password' => bcrypt('password123'),
            'code_partenaire' => 'CFPAM01',
        ]);

        $pack = $this->createPack($category, 'Pack Pro Informatique', 'pack-pro-informatique');

        $user = EliteUser::create([
            'nom' => 'User',
            'prenom' => 'Partner',
            'telephone' => '690000002',
            'email' => 'user.partner@test.com',
            'dernier_diplome' => 'Licence',
            'ville' => 'Yaounde',
            'password' => bcrypt('password123'),
            'partner_id' => $partner->id,
            'solde_points' => 500000,
            'referral_code' => 'ELUSER02',
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
            'due_at' => now(),
            'statut' => 'en_attente',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/payments/installments');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonFragment([
                'montant_fcfa' => '10000.00',
                'statut' => 'en_attente',
                'libelle' => 'Tranche 1 - Inscription',
            ]);
    }

    public function test_user_can_pay_installment_with_points()
    {
        SystemSetting::setTauxConversionFcfaPoints(1);

        $category = Category::create([
            'nom' => 'Réseaux',
            'slug' => 'reseaux',
        ]);

        $partner = Partner::create([
            'nom' => 'CFPAM Centre 2',
            'email' => 'partner2@test.com',
            'telephone' => '699999992',
            'password' => bcrypt('password123'),
            'code_partenaire' => 'CFPAM02',
        ]);

        $pack = $this->createPack($category, 'Pack Pro Réseau', 'pack-pro-reseau');

        $user = EliteUser::create([
            'nom' => 'UserPay',
            'prenom' => 'Test',
            'telephone' => '690000003',
            'email' => 'userpay@test.com',
            'dernier_diplome' => 'Master',
            'ville' => 'Douala',
            'password' => bcrypt('password123'),
            'partner_id' => $partner->id,
            'solde_points' => 20000,
            'referral_code' => 'ELPAY003',
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
            'due_at' => now(),
            'statut' => 'en_attente',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/payments/installments/{$userInstallment->id}/pay");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'statut' => 'paye',
                ],
            ]);

        $this->assertDatabaseHas('user_payment_installments', [
            'id' => $userInstallment->id,
            'statut' => 'paye',
        ]);

        $user->refresh();
        $this->assertEquals(10000, $user->solde_points);
    }

    public function test_user_cannot_pay_installment_if_insufficient_points()
    {
        SystemSetting::setTauxConversionFcfaPoints(1);

        $category = Category::create([
            'nom' => 'Design',
            'slug' => 'design',
        ]);

        $partner = Partner::create([
            'nom' => 'CFPAM Centre 3',
            'email' => 'partner3@test.com',
            'telephone' => '699999993',
            'password' => bcrypt('password123'),
            'code_partenaire' => 'CFPAM03',
        ]);

        $pack = $this->createPack($category, 'Pack Pro Design', 'pack-pro-design');

        $user = EliteUser::create([
            'nom' => 'Poor',
            'prenom' => 'User',
            'telephone' => '690000004',
            'email' => 'poor@test.com',
            'dernier_diplome' => 'Bac',
            'ville' => 'Douala',
            'password' => bcrypt('password123'),
            'partner_id' => $partner->id,
            'solde_points' => 500, // Insufficient for 10,000 FCFA
            'referral_code' => 'ELPOOR04',
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
            'due_at' => now(),
            'statut' => 'en_attente',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/payments/installments/{$userInstallment->id}/pay");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['points']);
    }

    public function test_standard_5_installments_are_auto_initialized_for_any_learner()
    {
        $user = EliteUser::create([
            'nom' => 'Normal',
            'prenom' => 'Learner',
            'telephone' => '690000005',
            'email' => 'normal.learner@test.com',
            'dernier_diplome' => 'Bac',
            'ville' => 'Bafoussam',
            'password' => bcrypt('password123'),
            'solde_points' => 0,
            'referral_code' => 'ELNORM05',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/payments/installments');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(5, 'data');

        $data = $response->json('data');
        $this->assertEquals(10000, (float) $data[0]['montant_fcfa']);
        $this->assertEquals(200000, (float) $data[1]['montant_fcfa']);
        $this->assertEquals(135000, (float) $data[2]['montant_fcfa']);
        $this->assertEquals(55000, (float) $data[3]['montant_fcfa']);
        $this->assertEquals(55000, (float) $data[4]['montant_fcfa']);
    }

    public function test_purchasing_a_pack_automatically_marks_tranche_3_as_paid()
    {
        $category = Category::create(['nom' => 'Bureautique', 'slug' => 'bureautique']);
        $pack = $this->createPack($category, 'Pack Bureautique Pro', 'pack-bureautique-pro');

        $user = EliteUser::create([
            'nom' => 'Buyer',
            'prenom' => 'Student',
            'telephone' => '690000006',
            'email' => 'buyer@test.com',
            'dernier_diplome' => 'Bac',
            'ville' => 'Douala',
            'password' => bcrypt('password123'),
            'solde_points' => 100, // Sufficient for 50 pts
            'referral_code' => 'ELBUY006',
        ]);

        Sanctum::actingAs($user);

        // Buy pack
        $response = $this->postJson("/api/payment/initiate-pack", ['pack_id' => $pack->id]);
        $response->assertStatus(200)->assertJson(['success' => true]);

        // Verify Tranche 3 is marked paid
        $installmentsRes = $this->getJson('/api/payments/installments');
        $installments = $installmentsRes->json('data');
        $tranche3 = collect($installments)->firstWhere('plan_installment.ordre', 3);

        $this->assertNotNull($tranche3);
        $this->assertEquals('paye', $tranche3['statut']);
    }

    public function test_redeeming_cash_code_with_tranches_and_pack_pays_immediately_and_unlocks_course()
    {
        $category = Category::create(['nom' => 'Secrétariat', 'slug' => 'secretariat']);
        $pack = $this->createPack($category, 'Pack Secrétariat', 'pack-secretariat');

        // Module
        $module = \App\Models\Module::create([
            'pack_id' => $pack->id,
            'nom' => 'Module 1 - Word',
            'ordre' => 1,
            'active' => true,
        ]);

        $user = EliteUser::create([
            'nom' => 'CashCodeUser',
            'prenom' => 'Jean',
            'telephone' => '690000007',
            'email' => 'cashcode@test.com',
            'dernier_diplome' => 'Bac',
            'ville' => 'Yaounde',
            'password' => bcrypt('password123'),
            'solde_points' => 0,
            'referral_code' => 'ELCASH07',
        ]);

        $cashCode = \App\Models\CashCode::create([
            'code' => 'CASH-TEST1234',
            'montant_fcfa' => 210000,
            'points' => 100,
            'pack_id' => $pack->id,
            'tranches' => [1, 2], // Tranche 1 (10k) and Tranche 2 (200k)
            'active' => true,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/wallet/use-cash-code', ['code' => 'CASH-TEST1234']);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'points_credites' => 100,
                ],
            ]);

        $user->refresh();
        $this->assertEquals(100, $user->solde_points);

        // Verify UserPack was created and module unlocked
        $this->assertDatabaseHas('user_packs', [
            'user_id' => $user->id,
            'pack_id' => $pack->id,
            'statut' => 'actif',
        ]);

        $this->assertDatabaseHas('module_unlocks', [
            'user_id' => $user->id,
            'module_id' => $module->id,
        ]);

        // Verify Tranche 1, Tranche 2, and Tranche 3 are paid
        $installmentsRes = $this->getJson('/api/payments/installments');
        $installments = $installmentsRes->json('data');

        $t1 = collect($installments)->firstWhere('plan_installment.ordre', 1);
        $t2 = collect($installments)->firstWhere('plan_installment.ordre', 2);
        $t3 = collect($installments)->firstWhere('plan_installment.ordre', 3);

        $this->assertEquals('paye', $t1['statut']);
        $this->assertEquals('paye', $t2['statut']);
        $this->assertEquals('paye', $t3['statut']);
    }
}
