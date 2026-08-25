<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MoneyFusionService;
use App\Models\Transaction;
use App\Models\UserPack;
use App\Models\Pack;
use App\Models\SystemSetting;
use App\Services\PartnerPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private MoneyFusionService $moneyFusionService,
        private PartnerPaymentService $partnerPaymentService,
    ) {}

    /**
    * Achat d'un pack — débit en points après conversion du prix réel en FCFA.
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

        $pack = Pack::findOrFail($packId);

        if ($user->partner_id) {
            $userPack = $this->partnerPaymentService->attachPlanToPack($user, $pack);
            $installments = $userPack->load('installments')->installments;
            $msg = ($installments && $installments->isNotEmpty())
                ? 'Formation ajoutée avec son échéancier partenaire.'
                : 'Formation débloquée avec succès. Vous avez un accès complet.';

            return response()->json([
                'success' => true,
                'message' => $msg,
                'data' => [
                    'user_pack_id' => $userPack->id,
                    'installments' => $installments,
                    'has_schedule' => $installments && $installments->isNotEmpty(),
                ],
            ], 201);
        }

        $montant = $pack->prix_fcfa_effectif;
        $points = (int) ceil($montant / max(1, SystemSetting::getTauxConversionFcfaPoints()));

        if ($user->solde_points < $points) {
            return response()->json([
                'success' => false,
                'message' => 'Solde insuffisant. Veuillez recharger votre wallet.',
                'data'    => [
                    'solde_actuel'   => $user->solde_points,
                    'montant_requis' => $points,
                ],
            ], 422);
        }

        $reference = Transaction::generateReference();

        try {
            DB::transaction(function () use ($user, $packId, $montant, $points, $reference) {
                $user->decrement('solde_points', $points);

                Transaction::create([
                    'user_id'      => $user->id,
                    'type'         => 'achat_pack',
                    'montant_fcfa' => $montant,
                    'points'       => $points,
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

                // Débloquer le premier module
                $firstModule = \App\Models\Module::where('pack_id', $packId)
                    ->where('active', true)->orderBy('ordre')->orderBy('id')->first();
                if ($firstModule) {
                    \App\Models\ModuleUnlock::firstOrCreate(
                        ['user_id' => $user->id, 'module_id' => $firstModule->id],
                        ['unlock_method' => 'score']
                    );
                }

                $this->handleReferralOnPackPurchase($user, $packId);
                $this->partnerPaymentService->markTranche3PaidOnPackPurchase($user, $packId);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pack acheté avec succès.',
                'data'    => [
                    'reference'     => $reference,
                    'montant'       => $montant,
                    'points'        => $points,
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
            'points'       => (int) floor($montant / max(1, SystemSetting::getTauxConversionFcfaPoints())),
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

        // Vérification de la signature du webhook
        $secret = config('services.moneyfusion.webhook_secret');
        if ($secret) {
            $signature = $request->header('X-Moneyfusion-Signature')
                      ?? $request->header('X-Webhook-Signature')
                      ?? $request->header('Signature');

            $computedSignature = hash_hmac('sha256', $request->getContent(), $secret);

            if (!$signature || !hash_equals($computedSignature, (string) $signature)) {
                Log::warning('MoneyFusion webhook signature verification failed', [
                    'received_signature' => $signature,
                    'ip'                 => $request->ip(),
                ]);
                return response()->json(['success' => false, 'message' => 'Signature invalide'], 401);
            }
        }

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
    * Dépôt réussi — convertit le montant payé en points.
     */
    private function processSuccessfulDeposit($user, $transaction): void
    {
        $montant = (float) $transaction->montant_fcfa;

        $points = (int) floor($montant / max(1, SystemSetting::getTauxConversionFcfaPoints()));
        $user->increment('solde_points', $points);

        $transaction->update(['points' => $points, 'statut' => 'complete']);
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