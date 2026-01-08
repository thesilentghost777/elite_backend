<?php

namespace App\Services;

use App\Models\CashCode;
use App\Models\EliteUser;
use App\Models\Pack;
use App\Models\Module;
use App\Models\Chapter;
use App\Models\ChapterUnlock;
use App\Models\SystemSetting;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\UserPack;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Récupérer le solde de l'utilisateur
     */
    public function getBalance(EliteUser $user): array
    {
        $fcfaPerPoint = SystemSetting::getFcfaPerPoint();
        
        return [
            'points' => $user->solde_points,
            'equivalent_fcfa' => $user->solde_points * $fcfaPerPoint,
            'taux_conversion' => "1 point = {$fcfaPerPoint} FCFA",
        ];
    }

    /**
     * Effectuer un dépôt (simulation API de paiement)
     */
    public function deposit(EliteUser $user, float $montantFcfa): array
    {
        $fcfaPerPoint = SystemSetting::getFcfaPerPoint();
        $points = $montantFcfa / $fcfaPerPoint;

        return DB::transaction(function () use ($user, $montantFcfa, $points) {
            $paymentSuccess = true;

            if (!$paymentSuccess) {
                throw ValidationException::withMessages([
                    'payment' => ['Le paiement a échoué. Veuillez réessayer.']
                ]);
            }

            $user->addPoints($points);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'depot',
                'montant_fcfa' => $montantFcfa,
                'points' => $points,
                'reference' => Transaction::generateReference(),
                'description' => "Dépôt de {$montantFcfa} FCFA",
                'statut' => 'complete',
            ]);

            return [
                'success' => true,
                'transaction' => [
                    'reference' => $transaction->reference,
                    'montant_fcfa' => $montantFcfa,
                    'points_credites' => $points,
                ],
                'nouveau_solde' => $user->solde_points,
            ];
        });
    }

    /**
     * Utiliser un code caisse
     */
    public function useCashCode(EliteUser $user, string $code): array
    {
        $cashCode = CashCode::where('code', $code)->first();

        if (!$cashCode) {
            throw ValidationException::withMessages([
                'code' => ['Code caisse invalide.']
            ]);
        }

        if (!$cashCode->canBeUsedBy($user)) {
            if ($cashCode->used_at) {
                throw ValidationException::withMessages([
                    'code' => ['Ce code a déjà été utilisé.']
                ]);
            }
            if ($cashCode->assigned_to && $cashCode->assigned_to !== $user->id) {
                throw ValidationException::withMessages([
                    'code' => ['Ce code est assigné à un autre utilisateur.']
                ]);
            }
            if ($cashCode->expires_at && $cashCode->expires_at->isPast()) {
                throw ValidationException::withMessages([
                    'code' => ['Ce code a expiré.']
                ]);
            }
            throw ValidationException::withMessages([
                'code' => ['Ce code n\'est pas valide.']
            ]);
        }

        return DB::transaction(function () use ($user, $cashCode) {
            $cashCode->update([
                'used_by' => $user->id,
                'used_at' => now(),
                'active' => false,
            ]);

            $user->addPoints($cashCode->points);

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'code_caisse',
                'montant_fcfa' => $cashCode->montant_fcfa,
                'points' => $cashCode->points,
                'reference' => Transaction::generateReference(),
                'description' => "Code caisse: {$cashCode->code}",
                'metadata' => [
                    'cash_code_id' => $cashCode->id,
                    'code' => $cashCode->code,
                ],
                'statut' => 'complete',
            ]);

            return [
                'success' => true,
                'transaction' => [
                    'reference' => $transaction->reference,
                    'montant_fcfa' => $cashCode->montant_fcfa,
                    'points_credites' => $cashCode->points,
                ],
                'nouveau_solde' => $user->solde_points,
            ];
        });
    }

    /**
     * Rechercher un utilisateur pour transfert
     */
    public function findUserForTransfer(string $telephone): array
    {
        $user = EliteUser::where('telephone', $telephone)
            ->select('id', 'nom', 'prenom', 'telephone', 'ville')
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'telephone' => ['Aucun utilisateur trouvé avec ce numéro.']
            ]);
        }

        return [
            'id' => $user->id,
            'nom' => $user->nom,
            'prenom' => $user->prenom,
            'telephone' => $user->telephone,
            'ville' => $user->ville,
        ];
    }

    /**
     * Effectuer un transfert
     */
    public function transfer(EliteUser $sender, string $receiverPhone, float $points, ?string $motif = null): array
    {
        if ($sender->telephone === $receiverPhone) {
            throw ValidationException::withMessages([
                'receiver' => ['Vous ne pouvez pas vous transférer des points à vous-même.']
            ]);
        }

        $receiver = EliteUser::where('telephone', $receiverPhone)->first();

        if (!$receiver) {
            throw ValidationException::withMessages([
                'receiver' => ['Destinataire non trouvé.']
            ]);
        }

        if (!$sender->canAfford($points)) {
            throw ValidationException::withMessages([
                'points' => ['Solde insuffisant pour ce transfert.']
            ]);
        }

        return DB::transaction(function () use ($sender, $receiver, $points, $motif) {
            $sender->deductPoints($points);
            $receiver->addPoints($points);

            $transactionEnvoi = Transaction::create([
                'user_id' => $sender->id,
                'type' => 'transfert_envoi',
                'points' => -$points,
                'reference' => Transaction::generateReference(),
                'description' => "Transfert vers {$receiver->full_name}",
                'metadata' => [
                    'receiver_id' => $receiver->id,
                    'receiver_nom' => $receiver->full_name,
                    'motif' => $motif,
                ],
                'statut' => 'complete',
            ]);

            $transactionRecu = Transaction::create([
                'user_id' => $receiver->id,
                'type' => 'transfert_recu',
                'points' => $points,
                'reference' => Transaction::generateReference(),
                'description' => "Transfert reçu de {$sender->full_name}",
                'metadata' => [
                    'sender_id' => $sender->id,
                    'sender_nom' => $sender->full_name,
                    'motif' => $motif,
                ],
                'statut' => 'complete',
            ]);

            Transfer::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'points' => $points,
                'motif' => $motif,
                'transaction_envoi_id' => $transactionEnvoi->id,
                'transaction_recu_id' => $transactionRecu->id,
            ]);

            return [
                'success' => true,
                'transfer' => [
                    'reference' => $transactionEnvoi->reference,
                    'points' => $points,
                    'destinataire' => [
                        'nom' => $receiver->full_name,
                        'telephone' => $receiver->telephone,
                    ],
                ],
                'nouveau_solde' => $sender->fresh()->solde_points,
            ];
        });
    }

    /**
     * Récupérer l'historique des transactions
     */
    public function getTransactionHistory(EliteUser $user, int $limit = 20): array
    {
        $transactions = Transaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'type_label' => $this->getTransactionTypeLabel($transaction->type),
                'montant_fcfa' => $transaction->montant_fcfa,
                'points' => $transaction->points,
                'reference' => $transaction->reference,
                'description' => $transaction->description,
                'statut' => $transaction->statut,
                'date' => $transaction->created_at->format('d/m/Y H:i'),
            ];
        })->toArray();
    }

   public function purchasePack(EliteUser $user, int $packId): array
    {
        $pack = Pack::findOrFail($packId);

        if ($user->hasPack($packId)) {
            throw ValidationException::withMessages([
                'pack' => ['Vous possédez déjà ce pack.']
            ]);
        }

        if (!$user->canAfford($pack->prix_points)) {
            throw ValidationException::withMessages([
                'solde' => ['Solde insuffisant. Il vous manque ' . ($pack->prix_points - $user->solde_points) . ' points.']
            ]);
        }

        return DB::transaction(function () use ($user, $pack) {
            $user->deductPoints($pack->prix_points);

            // Créer l'accès au pack
            $userPack = UserPack::create([
                'user_id' => $user->id,
                'pack_id' => $pack->id,
                'duree_choisie' => 'illimité',
                'prix_paye' => $pack->prix_points,
                'statut' => 'actif',
                'date_achat' => now(),
                'date_expiration' => null,
            ]);

            // ✅ CORRECTION: Débloquer UNIQUEMENT le premier chapitre du premier module
            $firstModule = Module::where('pack_id', $pack->id)
                ->where('active', true)
                ->orderBy('ordre')
                ->first();

            if ($firstModule) {
                $firstChapter = Chapter::where('module_id', $firstModule->id)
                    ->where('active', true)
                    ->orderBy('ordre')
                    ->first();

                if ($firstChapter) {
                    ChapterUnlock::firstOrCreate([
                        'user_id' => $user->id,
                        'chapter_id' => $firstChapter->id,
                    ],
                    [
                        'unlock_method' => 'score',  // Or 'score' depending on your business logic
                        'unlocked_at' => now(),
                    ]);
                }
            }

            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'achat_pack',
                'points' => -$pack->prix_points,
                'reference' => Transaction::generateReference(),
                'description' => "Achat du pack: {$pack->nom}",
                'metadata' => [
                    'pack_id' => $pack->id,
                    'pack_nom' => $pack->nom,
                ],
                'statut' => 'complete',
            ]);

            return [
                'success' => true,
                'pack' => [
                    'id' => $pack->id,
                    'nom' => $pack->nom,
                    'acces' => 'illimité',
                ],
                'transaction' => [
                    'reference' => $transaction->reference,
                    'points_debites' => $pack->prix_points,
                ],
                'nouveau_solde' => $user->fresh()->solde_points,
            ];
        });
    }

    /**
     * Récupérer les packs de l'utilisateur
     */
    public function getUserPacks(EliteUser $user): array
    {
        $userPacks = UserPack::where('user_id', $user->id)
            ->with('pack.category')
            ->orderByDesc('date_achat')
            ->get();

        return $userPacks->map(function ($userPack) {
            return [
                'id' => $userPack->id,
                'pack' => [
                    'id' => $userPack->pack->id,
                    'nom' => $userPack->pack->nom,
                    'slug' => $userPack->pack->slug,
                    'category' => $userPack->pack->category->nom,
                ],
                'duree_choisie' => $userPack->duree_choisie,
                'statut' => $userPack->statut,
                'progression' => $userPack->progression,
                'date_achat' => $userPack->date_achat->format('d/m/Y'),
                'date_expiration' => $userPack->date_expiration?->format('d/m/Y'),
                'jours_restants' => $userPack->date_expiration ? 
                    max(0, now()->diffInDays($userPack->date_expiration, false)) : null,
            ];
        })->toArray();
    }

    /**
     * Libellé du type de transaction
     */
    private function getTransactionTypeLabel(string $type): string
    {
        return match($type) {
            'depot' => 'Dépôt',
            'achat_pack' => 'Achat de pack',
            'parrainage' => 'Bonus parrainage',
            'transfert_envoi' => 'Transfert envoyé',
            'transfert_recu' => 'Transfert reçu',
            'code_caisse' => 'Code caisse',
            'bourse' => 'Bourse',
            default => $type,
        };
    }
}