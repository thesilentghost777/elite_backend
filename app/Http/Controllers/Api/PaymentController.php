<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MoneyFusionService;
use App\Models\Transaction;
use App\Models\UserPack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private MoneyFusionService $moneyFusionService) {}

    /**
     * Achat d'un pack — débit direct sur solde_points (= FCFA)
     * Montant fixe : 10 000
     */
    public function initiatePackPayment(Request $request): JsonResponse
    {
        $request->validate([
            'pack_id' => 'required|integer|exists:packs,id',
        ]);

        $user   = $request->user();
        $packId = $request->pack_id;

        if ($user->hasPack($packId)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous possédez déjà ce pack.',
            ], 422);
        }

        $montant = 10000;

        // Vérification du solde — solde_points = FCFA
        if ($user->solde_points < $montant) {
            return response()->json([
                'success' => false,
                'message' => 'Solde insuffisant. Veuillez recharger votre wallet.',
                'data'    => [
                    'solde_actuel'   => $user->solde_points,
                    'montant_requis' => $montant,
                ],
            ], 422);
        }

        $reference = Transaction::generateReference();

        try {
            DB::transaction(function () use ($user, $packId, $montant, $reference) {
                // Débiter solde_points
                $user->decrement('solde_points', $montant);

                Transaction::create([
                    'user_id'      => $user->id,
                    'type'         => 'achat_pack',
                    'montant_fcfa' => $montant,
                    'points'       => $montant,
                    'reference'    => $reference,
                    'description'  => "Achat pack ID {$packId}",
                    'statut'       => 'complete',
                    'metadata'     => ['pack_id' => $packId],
                ]);

                UserPack::create([
                    'user_id'         => $user->id,
                    'pack_id'         => $packId,
                    'duree_choisie'   => 'illimité',
                    'prix_paye'       => $montant,
                    'statut'          => 'actif',
                    'date_achat'      => now(),
                    'date_expiration' => null,
                ]);

                // Débloquer le premier chapitre
                $firstModule = \App\Models\Module::where('pack_id', $packId)
                    ->where('active', true)->orderBy('ordre')->first();
                if ($firstModule) {
                    $firstChapter = \App\Models\Chapter::where('module_id', $firstModule->id)
                        ->where('active', true)->orderBy('ordre')->first();
                    if ($firstChapter) {
                        \App\Models\ChapterUnlock::firstOrCreate(
                            ['user_id' => $user->id, 'chapter_id' => $firstChapter->id],
                            ['unlock_method' => 'score']
                        );
                    }
                }

                $this->handleReferralOnPackPurchase($user, $packId);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pack acheté avec succès.',
                'data'    => [
                    'reference'     => $reference,
                    'montant'       => $montant,
                    'nouveau_solde' => $user->fresh()->solde_points,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Pack purchase error', [
                'user_id' => $user->id,
                'pack_id' => $packId,
                'error'   => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Initier un dépôt via MoneyFusion — crédite solde_points
     */
    public function initiateDeposit(Request $request): JsonResponse
    {
        $request->validate([
            'montant_fcfa' => 'required|numeric|min:500',
        ]);

        $user      = $request->user();
        $montant   = (float) $request->montant_fcfa;
        $reference = Transaction::generateReference();

        Transaction::create([
            'user_id'      => $user->id,
            'type'         => 'depot',
            'montant_fcfa' => $montant,
            'points'       => $montant,
            'reference'    => $reference,
            'description'  => "Dépôt de {$montant} FCFA",
            'statut'       => 'en_attente',
        ]);

        try {
            $paymentData = $this->moneyFusionService->formatPaymentData(
                $montant,
                $user->telephone,
                $user->nom . ' ' . $user->prenom,
                $user->id,
                $reference
            );

            $result = $this->moneyFusionService->initiatePayment($paymentData);

            if (!$result['success'] || empty($result['payment_url'])) {
                Transaction::where('reference', $reference)->update(['statut' => 'echoue']);
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Impossible d\'initier le paiement',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'payment_url'   => $result['payment_url'],
                    'payment_token' => $result['token'],
                    'reference'     => $reference,
                ],
            ]);

        } catch (\Exception $e) {
            Transaction::where('reference', $reference)->update(['statut' => 'echoue']);
            Log::error('Deposit initiation error', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function checkPaymentStatus(Request $request): JsonResponse
    {
        $request->validate(['payment_token' => 'required|string']);

        $user  = $request->user();
        $token = $request->payment_token;

        try {
            $result = $this->moneyFusionService->checkPaymentStatus($token);

            if (!$result['success']) {
                return response()->json(['success' => false, 'message' => $result['message'] ?? 'Impossible de vérifier'], 400);
            }

            $data      = $result['data'];
            $statut    = $data['statut'] ?? 'en_attente';
            $reference = $data['personal_Info'][0]['transactionRef'] ?? null;

            $transaction = Transaction::where('reference', $reference)
                ->where('user_id', $user->id)
                ->where('statut', 'en_attente')
                ->first();

            if (in_array($statut, ['paid', 'complete']) && $transaction) {
                DB::transaction(function () use ($user, $transaction) {
                    $this->processSuccessfulDeposit($user, $transaction);
                });
            } elseif (in_array($statut, ['failure', 'echoue', 'annule']) && $transaction) {
                $transaction->update(['statut' => 'echoue']);
            }

            return response()->json(['success' => true, 'data' => ['statut' => $statut, 'montant' => $data['montant'] ?? null]]);

        } catch (\Exception $e) {
            Log::error('Payment status check error', ['token' => $token, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        Log::info('MoneyFusion webhook received', $request->all());

        $statut    = $request->input('statut');
        $reference = $request->input('personal_Info.0.transactionRef')
                  ?? ($request->input('personal_Info')[0]['transactionRef'] ?? null);

        if (!$reference) {
            Log::error('Webhook: transactionRef manquant', $request->all());
            return response()->json(['success' => false, 'message' => 'Référence manquante'], 400);
        }

        $transaction = Transaction::where('reference', $reference)
            ->where('statut', 'en_attente')
            ->first();

        if (!$transaction) {
            return response()->json(['success' => true]);
        }

        try {
            if (in_array($statut, ['paid', 'complete'])) {
                DB::transaction(function () use ($transaction) {
                    $this->processSuccessfulDeposit($transaction->user, $transaction);
                });
            } elseif (in_array($statut, ['failure', 'echoue', 'annule', 'failed'])) {
                $transaction->update(['statut' => 'echoue']);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook error', ['error' => $e->getMessage(), 'reference' => $reference]);
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Dépôt réussi — crédite solde_points (= FCFA)
     */
    private function processSuccessfulDeposit($user, $transaction): void
    {
        $montant = (float) $transaction->montant_fcfa;

        // Créditer solde_points
        $user->increment('solde_points', $montant);

        $transaction->update(['statut' => 'complete']);
    }

    private function handleReferralOnPackPurchase($user, $packId): void
    {
        if (!$user->referred_by) return;

        $parrain = \App\Models\EliteUser::where('referral_code', $user->referred_by)->first();
        if (!$parrain) return;

        $exists = DB::table('referral_history')
            ->where('parrain_id', $parrain->id)
            ->where('filleul_id', $user->id)
            ->exists();

        if (!$exists) {
            DB::table('referral_history')->insert([
                'parrain_id'    => $parrain->id,
                'filleul_id'    => $user->id,
                'points_gagnes' => 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        DB::table('referral_history')
            ->where('parrain_id', $parrain->id)
            ->where('filleul_id', $user->id)
            ->update(['has_purchased_pack' => true, 'pack_id' => $packId]);
    }

    public function returnUrl(Request $request)
    {
        return response()->json(['success' => true, 'message' => 'Paiement traité']);
    }
}