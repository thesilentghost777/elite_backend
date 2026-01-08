<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EliteUser;
use App\Models\Transaction;
use App\Services\MoneyFusionService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private MoneyFusionService $moneyFusion,
        private PaymentService $paymentService
    ) {}

    /**
     * Initier un dépôt via MoneyFusion
     */
    public function initiateDeposit(Request $request): JsonResponse
    {
        $request->validate([
            'montant_fcfa' => 'required|numeric|min:650',
        ]);

        try {
            $user = $request->user();
            $montantFcfa = (float) $request->montant_fcfa;
            
            // Créer une transaction en attente
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'depot',
                'montant_fcfa' => $montantFcfa,
                'points' => $montantFcfa / config('app.fcfa_per_point', 650),
                'reference' => Transaction::generateReference(),
                'description' => "Dépôt de {$montantFcfa} FCFA en cours",
                'statut' => 'en_attente',
                'metadata' => [
                    'payment_method' => 'moneyfusion',
                    'initiated_at' => now()->toISOString(),
                ],
            ]);

            // Formater les données pour MoneyFusion
            $paymentData = $this->moneyFusion->formatPaymentData(
                $montantFcfa,
                $user->telephone,
                $user->full_name,
                $user->id,
                $transaction->reference
            );

            // Initier le paiement
            $result = $this->moneyFusion->initiatePayment($paymentData);

            if (!$result['success'] || !$result['payment_url']) {
                $transaction->update(['statut' => 'failed']);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de l\'initiation du paiement',
                ], 400);
            }

            // Sauvegarder le token de paiement
            $transaction->update([
                'metadata' => array_merge($transaction->metadata ?? [], [
                    'payment_token' => $result['token'],
                    'payment_url' => $result['payment_url'],
                ]),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_url' => $result['payment_url'],
                    'payment_token' => $result['token'],
                    'transaction_reference' => $transaction->reference,
                    'montant' => $montantFcfa,
                    'points_a_crediter' => $transaction->points,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Deposit initiation error', [
                'user_id' => $request->user()->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'initiation du dépôt',
            ], 500);
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        $request->validate([
            'payment_token' => 'required|string',
        ]);

        try {
            $result = $this->moneyFusion->checkPaymentStatus($request->payment_token);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de vérifier le statut',
                ], 400);
            }

            $paymentData = $result['data'];

            return response()->json([
                'success' => true,
                'data' => [
                    'statut' => $paymentData['statut'] ?? 'pending',
                    'montant' => $paymentData['Montant'] ?? 0,
                    'frais' => $paymentData['frais'] ?? 0,
                    'moyen' => $paymentData['moyen'] ?? null,
                    'date' => $paymentData['createdAt'] ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Payment status check error', [
                'token' => $request->payment_token,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification',
            ], 500);
        }
    }

    /**
     * Webhook pour recevoir les notifications de paiement
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            Log::info('MoneyFusion webhook received', $request->all());

            $event = $request->input('event');
            $tokenPay = $request->input('tokenPay');
            $personalInfo = $request->input('personal_Info', []);
            $montant = $request->input('Montant');

            // Extraire les infos personnelles
            $userId = $personalInfo[0]['userId'] ?? null;
            $transactionRef = $personalInfo[0]['transactionRef'] ?? null;

            if (!$userId || !$transactionRef) {
                Log::error('Webhook missing required data', $request->all());
                return response()->json(['success' => false], 400);
            }

            // Retrouver la transaction
            $transaction = Transaction::where('reference', $transactionRef)
                ->where('user_id', $userId)
                ->first();

            if (!$transaction) {
                Log::error('Transaction not found', [
                    'reference' => $transactionRef,
                    'user_id' => $userId,
                ]);
                return response()->json(['success' => false], 404);
            }

            // Éviter les doublons
            if ($transaction->statut === 'complete' && $event === 'payin.session.completed') {
                return response()->json(['success' => true, 'message' => 'Already processed']);
            }

            // Traiter selon l'événement
            DB::transaction(function () use ($transaction, $event, $request, $montant) {
                switch ($event) {
                    case 'payin.session.completed':
                        if ($transaction->statut !== 'complete') {
                            $user = EliteUser::find($transaction->user_id);
                            $user->addPoints($transaction->points);

                            $transaction->update([
                                'statut' => 'complete',
                                'metadata' => array_merge($transaction->metadata ?? [], [
                                    'completed_at' => now()->toISOString(),
                                    'webhook_data' => $request->all(),
                                    'payment_method_used' => $request->input('moyen'),
                                    'transaction_number' => $request->input('numeroTransaction'),
                                ]),
                            ]);

                            Log::info('Payment completed', [
                                'transaction_id' => $transaction->id,
                                'user_id' => $user->id,
                                'points_credited' => $transaction->points,
                            ]);
                        }
                        break;

                    case 'payin.session.cancelled':
                        if ($transaction->statut !== 'failed') {
                            $transaction->update([
                                'statut' => 'failed',
                                'metadata' => array_merge($transaction->metadata ?? [], [
                                    'cancelled_at' => now()->toISOString(),
                                    'webhook_data' => $request->all(),
                                ]),
                            ]);
                        }
                        break;

                    case 'payin.session.pending':
                        // Ne rien faire si déjà en pending
                        if ($transaction->statut === 'pending') {
                            Log::info('Payment still pending', [
                                'transaction_id' => $transaction->id,
                            ]);
                        }
                        break;
                }
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Page de retour après paiement
     */
    public function returnUrl(Request $request): JsonResponse
    {
        // Cette route peut rediriger vers l'app mobile ou afficher un message
        $token = $request->query('token');
        
        if ($token) {
            try {
                $result = $this->moneyFusion->checkPaymentStatus($token);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement traité',
                    'statut' => $result['data']['statut'] ?? 'pending',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la vérification',
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Retour du paiement',
        ]);
    }
}