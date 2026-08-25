<?php

namespace App\Services;

use App\Models\EliteUser;
use App\Models\Pack;
use App\Models\Module;
use App\Models\Chapter;
use App\Models\ChapterUnlock;
use App\Models\PartnerPaymentPlan;
use App\Models\UserPack;
use App\Models\UserPaymentInstallment;
use App\Models\Transaction;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PartnerPaymentService
{
    public const STANDARD_INSTALLMENTS = [
        ['ordre' => 1, 'libelle' => 'Tranche 1 - Inscription', 'montant_fcfa' => 10000, 'delai_jours' => 0],
        ['ordre' => 2, 'libelle' => 'Tranche 2 - Frais Scolaires', 'montant_fcfa' => 200000, 'delai_jours' => 30],
        ['ordre' => 3, 'libelle' => 'Tranche 3 - Matière d\'œuvre (Pack)', 'montant_fcfa' => 135000, 'delai_jours' => 60],
        ['ordre' => 4, 'libelle' => 'Tranche 4 - Inscription Examens', 'montant_fcfa' => 55000, 'delai_jours' => 90],
        ['ordre' => 5, 'libelle' => 'Tranche 5 - Stage et Soutenance', 'montant_fcfa' => 55000, 'delai_jours' => 120],
    ];

    public function createPlan(PartnerPaymentPlan $plan, array $installments): void
    {
        if (count($installments) !== 5) {
            throw ValidationException::withMessages(['installments' => ['Un plan doit contenir exactement 5 paiements.']]);
        }
        $requiredAmounts = [10000, 200000, 135000, 55000, 55000];
        foreach (array_values($installments) as $index => $installment) {
            if ((float) $installment['montant_fcfa'] !== (float) $requiredAmounts[$index]) {
                throw ValidationException::withMessages([
                    "installments.{$index}.montant_fcfa" => ["Le montant attendu pour la tranche " . ($index + 1) . " est {$requiredAmounts[$index]} FCFA."],
                ]);
            }
        }
        $plan->installments()->delete();
        $plan->installments()->createMany(collect($installments)->values()->map(fn ($item, $index) => [
            'libelle' => $item['libelle'],
            'montant_fcfa' => $item['montant_fcfa'],
            'delai_jours' => $item['delai_jours'],
            'ordre' => $index + 1,
        ])->all());
    }

    public function getOrCreateUserInstallments(EliteUser $user)
    {
        $existing = UserPaymentInstallment::whereHas('userPack', fn ($q) => $q->where('user_id', $user->id))
            ->with('planInstallment')
            ->get()
            ->sortBy(fn($i) => $i->planInstallment?->ordre ?? $i->id);

        if ($existing->isNotEmpty()) {
            $hasActivePack = UserPack::where('user_id', $user->id)->where('statut', 'actif')->exists();
            if ($hasActivePack) {
                foreach ($existing as $inst) {
                    if (($inst->planInstallment?->ordre === 3 || (float) $inst->montant_fcfa === 135000.0) && $inst->statut !== 'paye') {
                        $inst->update(['statut' => 'paye', 'paid_at' => now()]);
                    }
                }
            }
            return $existing->values();
        }

        return DB::transaction(function () use ($user) {
            $userPack = UserPack::where('user_id', $user->id)->first();
            $pack = $userPack ? $userPack->pack : (Pack::active()->orderBy('ordre')->first() ?? Pack::first());

            if (!$pack) {
                $category = \App\Models\Category::firstOrCreate(
                    ['slug' => 'general'],
                    ['nom' => 'Général', 'ordre' => 1, 'active' => true]
                );
                $pack = Pack::create([
                    'category_id' => $category->id,
                    'nom' => 'Pack Professionnel',
                    'slug' => 'pack-professionnel',
                    'description' => 'Formation professionnelle et académique complète',
                    'niveau_requis' => 'BAC',
                    'durees_disponibles' => ['6 mois'],
                    'diplomes_possibles' => ['CQP'],
                    'prix_points' => 50,
                    'prix_fcfa' => 135000,
                    'active' => true,
                ]);
            }

            if (!$userPack) {
                $userPack = UserPack::create([
                    'user_id' => $user->id,
                    'pack_id' => $pack->id,
                    'duree_choisie' => $user->partner_id ? 'partenaire' : 'standard',
                    'prix_paye' => 0,
                    'statut' => 'actif',
                    'date_achat' => now(),
                ]);
            }

            $partnerId = $user->partner_id;
            if (!$partnerId) {
                $partner = \App\Models\Partner::firstOrCreate(
                    ['email' => 'centre.principal@elitetraining.cm'],
                    ['nom' => 'Centre Principal Elite', 'telephone' => '690000000', 'password' => bcrypt('Elite2026!'), 'code_partenaire' => 'ELITE-MAIN']
                );
                $partnerId = $partner->id;
            }

            $plan = PartnerPaymentPlan::firstOrCreate(
                ['partner_id' => $partnerId, 'pack_id' => $pack->id],
                ['nom' => 'Plan Standard 5 Tranches', 'active' => true]
            );

            if ($plan->installments()->count() === 0) {
                foreach (self::STANDARD_INSTALLMENTS as $t) {
                    \App\Models\PartnerPaymentInstallment::create([
                        'plan_id' => $plan->id,
                        'libelle' => $t['libelle'],
                        'montant_fcfa' => $t['montant_fcfa'],
                        'delai_jours' => $t['delai_jours'],
                        'ordre' => $t['ordre'],
                    ]);
                }
            }

            $planInstallments = $plan->installments()->orderBy('ordre')->get();
            $hasActivePack = $userPack->statut === 'actif' && ($userPack->prix_paye > 0 || UserPack::where('user_id', $user->id)->where('statut', 'actif')->exists());

            foreach ($planInstallments as $pi) {
                $isTranche3 = ($pi->ordre === 3 || (float) $pi->montant_fcfa === 135000.0);
                $isPaid = $isTranche3 && $hasActivePack;

                UserPaymentInstallment::firstOrCreate(
                    ['user_pack_id' => $userPack->id, 'plan_installment_id' => $pi->id],
                    [
                        'montant_fcfa' => $pi->montant_fcfa,
                        'due_at' => now()->addDays($pi->delai_jours),
                        'statut' => $isPaid ? 'paye' : 'en_attente',
                        'paid_at' => $isPaid ? now() : null,
                    ]
                );
            }

            return UserPaymentInstallment::where('user_pack_id', $userPack->id)
                ->with('planInstallment')
                ->get()
                ->sortBy(fn($i) => $i->planInstallment?->ordre ?? $i->id)
                ->values();
        });
    }

    public function attachPlanToPack(EliteUser $user, Pack $pack): UserPack
    {
        $plan = PartnerPaymentPlan::with('installments')
            ->where('partner_id', $user->partner_id)
            ->where('pack_id', $pack->id)
            ->where('active', true)
            ->first();

        return DB::transaction(function () use ($user, $pack, $plan) {
            $userPack = UserPack::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'pack_id' => $pack->id,
                ],
                [
                    'duree_choisie' => 'partenaire',
                    'prix_paye' => 0,
                    'statut' => 'actif',
                    'date_achat' => now(),
                    'date_expiration' => $plan?->date_fin_formation,
                ]
            );

            $user->update([
                'formation_deadline' => $plan?->date_fin_formation,
                'formation_status' => 'active',
            ]);

            // Débloquer automatiquement le premier module
            $firstModule = Module::where('pack_id', $pack->id)
                ->where('active', true)
                ->orderBy('ordre')
                ->orderBy('id')
                ->first();

            if ($firstModule) {
                \App\Models\ModuleUnlock::firstOrCreate(
                    ['user_id' => $user->id, 'module_id' => $firstModule->id],
                    ['unlock_method' => 'score']
                );
            }

            // Si un échéancier est configuré, créer les tranches
            if ($plan && $plan->installments && $plan->installments->isNotEmpty()) {
                foreach ($plan->installments as $installment) {
                    UserPaymentInstallment::firstOrCreate(
                        [
                            'user_pack_id' => $userPack->id,
                            'plan_installment_id' => $installment->id,
                        ],
                        [
                            'montant_fcfa' => $installment->montant_fcfa,
                            'due_at' => now()->addDays($installment->delai_jours),
                            'statut' => 'en_attente',
                        ]
                    );
                }
            } else {
                $this->getOrCreateUserInstallments($user);
            }

            return $userPack;
        });
    }

    public function pay(EliteUser $user, UserPaymentInstallment $installment): UserPaymentInstallment
    {
        $installment->load('userPack', 'planInstallment');
        if ($installment->userPack->user_id !== $user->id) abort(403);
        if ($installment->statut === 'paye') return $installment;
        $points = (int) ceil((float) $installment->montant_fcfa / max(1, SystemSetting::getTauxConversionFcfaPoints()));
        return DB::transaction(function () use ($user, $installment, $points) {
            $lockedUser = EliteUser::whereKey($user->id)->lockForUpdate()->first();
            if (!$lockedUser->canAfford($points)) {
                throw ValidationException::withMessages(['points' => ['Solde de points insuffisant.']]);
            }
            $lockedUser->deductPoints($points);
            $installment->update(['statut' => 'paye', 'paid_at' => now()]);
            $installment->userPack()->increment('prix_paye', $installment->montant_fcfa);

            // Si tranche 3 (Matière d'œuvre), activer le pack et débloquer le premier module
            if ($installment->planInstallment?->ordre === 3 || (float) $installment->montant_fcfa === 135000.0) {
                $userPack = $installment->userPack;
                if ($userPack) {
                    $userPack->update(['statut' => 'actif']);
                    $firstModule = Module::where('pack_id', $userPack->pack_id)
                        ->where('active', true)->orderBy('ordre')->orderBy('id')->first();
                    if ($firstModule) {
                        \App\Models\ModuleUnlock::firstOrCreate(
                            ['user_id' => $user->id, 'module_id' => $firstModule->id],
                            ['unlock_method' => 'score']
                        );
                    }
                }
            }

            Transaction::create([
                'user_id' => $lockedUser->id, 'type' => 'achat_pack', 'montant_fcfa' => $installment->montant_fcfa,
                'points' => $points, 'reference' => Transaction::generateReference(),
                'description' => 'Paiement de tranche: ' . ($installment->planInstallment->libelle ?? 'Tranche'),
                'metadata' => ['installment_id' => $installment->id], 'statut' => 'complete',
            ]);
            return $installment->fresh('planInstallment');
        });
    }

    public function markAsPaidByPartner(UserPaymentInstallment $installment, string $partnerNotes = ''): UserPaymentInstallment
    {
        $installment->load('userPack.user', 'planInstallment');
        if ($installment->statut === 'paye') {
            return $installment;
        }

        return DB::transaction(function () use ($installment, $partnerNotes) {
            $installment->update([
                'statut'  => 'paye',
                'paid_at' => now(),
            ]);

            $installment->userPack()->increment('prix_paye', $installment->montant_fcfa);

            // Si tranche 3 (Matière d'œuvre), activer le pack et débloquer le premier module
            if ($installment->planInstallment?->ordre === 3 || (float) $installment->montant_fcfa === 135000.0) {
                $userPack = $installment->userPack;
                if ($userPack) {
                    $userPack->update(['statut' => 'actif']);
                    $firstModule = Module::where('pack_id', $userPack->pack_id)
                        ->where('active', true)->orderBy('ordre')->orderBy('id')->first();
                    if ($firstModule) {
                        \App\Models\ModuleUnlock::firstOrCreate(
                            ['user_id' => $installment->userPack->user_id, 'module_id' => $firstModule->id],
                            ['unlock_method' => 'score']
                        );
                    }
                }
            }

            Transaction::create([
                'user_id'      => $installment->userPack->user_id,
                'type'         => 'achat_pack',
                'montant_fcfa' => $installment->montant_fcfa,
                'points'       => 0,
                'reference'    => Transaction::generateReference(),
                'description'  => 'Paiement comptoir partenaire : ' . ($installment->planInstallment->libelle ?? 'Tranche'),
                'metadata'     => [
                    'installment_id' => $installment->id,
                    'mode'           => 'comptoir_partenaire',
                    'notes'          => $partnerNotes,
                ],
                'statut'       => 'complete',
            ]);

            return $installment->fresh('planInstallment');
        });
    }

    public function markTranche3PaidOnPackPurchase(EliteUser $user, int $packId): void
    {
        $installments = $this->getOrCreateUserInstallments($user);
        foreach ($installments as $installment) {
            if (($installment->planInstallment?->ordre === 3 || (float) $installment->montant_fcfa === 135000.0) && $installment->statut !== 'paye') {
                $installment->update([
                    'statut' => 'paye',
                    'paid_at' => now(),
                ]);
            }
        }
    }
}