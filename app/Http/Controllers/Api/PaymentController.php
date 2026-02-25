<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MoneyFusionService;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private MoneyFusionService $moneyFusionService) {}

    /**
     * Étape 1 : Initier un dépôt — retourne l'URL de paiement MoneyFusion
     */
    public function initiateDeposit(Request $request): JsonResponse
    {
        $request->validate([
            'montant_fcfa' => 'required|numeric|min:500',
        ]);

        $user       = $request->user();
        $montant    = (float) $request->montant_fcfa;
        $reference  = Transaction::generateReference();

        // Créer la transaction en attente AVANT d'appeler MoneyFusion
        Transaction::create([
            'user_id'      => $user->id,
            'type'         => 'depot',
            'montant_fcfa' => $montant,
            'points'       => $this->getDepositPoints($montant),
            'reference'    => $reference,
            'description'  => "Dépôt de {$montant} FCFA",
            'statut'       => 'pending',
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
                // Annuler la transaction en attente
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

            Log::error('Deposit initiation error', [
                'user_id' => $user->id,
                'montant' => $montant,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Étape 2 : Vérifier le statut du paiement et créditer si confirmé
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        $request->validate([
            'payment_token' => 'required|string',
        ]);

        $user  = $request->user();
        $token = $request->payment_token;

        try {
            $result = $this->moneyFusionService->checkPaymentStatus($token);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Impossible de vérifier le paiement',
                ], 400);
            }

            $data       = $result['data'];
            $statut     = $data['statut']    ?? 'pending';
            $reference  = $data['personal_Info'][0]['transactionRef'] ?? null;

            // Retrouver la transaction pending
            $transaction = Transaction::where('reference', $reference)
                ->where('user_id', $user->id)
                ->where('statut', 'pending')
                ->first();

            // Créditer une seule fois si payé
            if (in_array($statut, ['paid', 'complete']) && $transaction) {
                DB::transaction(function () use ($user, $transaction) {
                    $user->addPoints($transaction->points);
                    $transaction->update(['statut' => 'complete']);
                });
            } elseif (in_array($statut, ['failure', 'echoue', 'annule']) && $transaction) {
                $transaction->update(['statut' => 'echoue']);
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'statut'  => $statut,
                    'montant' => $data['montant'] ?? null,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Payment status check error', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Webhook MoneyFusion — appelé automatiquement par MoneyFusion après paiement
     */
    public function webhook(Request $request): JsonResponse
    {
        Log::info('MoneyFusion webhook received', $request->all());

        $token = $request->input('token');

        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Token manquant'], 400);
        }

        try {
            $result = $this->moneyFusionService->checkPaymentStatus($token);

            if (!$result['success']) {
                return response()->json(['success' => false], 400);
            }

            $data      = $result['data'];
            $statut    = $data['statut'] ?? 'pending';
            $reference = $data['personal_Info'][0]['transactionRef'] ?? null;

            $transaction = Transaction::where('reference', $reference)
                ->where('statut', 'pending')
                ->first();

            if (in_array($statut, ['paid', 'complete']) && $transaction) {
                DB::transaction(function () use ($transaction) {
                    $user = $transaction->user;
                    $user->addPoints($transaction->points);
                    $transaction->update(['statut' => 'complete']);
                });
            } elseif (in_array($statut, ['failure', 'echoue', 'annule']) && $transaction) {
                $transaction->update(['statut' => 'echoue']);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * URL de retour après paiement (redirige l'utilisateur)
     */
    public function returnUrl(Request $request)
    {
        // Cette route est ouverte dans la WebView
        // Le frontend détecte cette URL et ferme la WebView
        return response()->json([
            'success' => true,
            'message' => 'Paiement traité',
        ]);
    }

    /**
     * Barème de conversion FCFA → points
     */
    private function getDepositPoints(float $montantFcfa): int
    {
        $bareme = [
            1000   => 3,
            2000   => 7,
            3000   => 10,
            5000   => 17,
            10000  => 35,
            20000  => 72,
            30000  => 110,
            50000  => 185,
            75000  => 280,
            100000 => 375,
        ];

        return $bareme[(int) $montantFcfa] ?? (int) ($montantFcfa / 650);
    }
}