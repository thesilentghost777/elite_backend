<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoneyFusionService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.moneyfusion.api_url');
    }

    /**
     * Initier un paiement
     */
    public function initiatePayment(array $paymentData): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->apiUrl, $paymentData);

            if (!$response->successful()) {
                Log::error('MoneyFusion payment initiation failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                
                throw new \Exception('Échec de l\'initiation du paiement');
            }

            $data = $response->json();

            return [
                'success' => $data['statut'] ?? false,
                'token' => $data['token'] ?? null,
                'message' => $data['message'] ?? 'Erreur inconnue',
                'payment_url' => $data['url'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('MoneyFusion API error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw new \Exception('Erreur de connexion au service de paiement');
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function checkPaymentStatus(string $token): array
    {
        try {
            $response = Http::timeout(30)
                ->get("https://www.pay.moneyfusion.net/paiementNotif/{$token}");

            if (!$response->successful()) {
                throw new \Exception('Impossible de vérifier le statut du paiement');
            }

            $data = $response->json();

            return [
                'success' => $data['statut'] ?? false,
                'data' => $data['data'] ?? null,
                'message' => $data['message'] ?? 'Erreur inconnue',
            ];
        } catch (\Exception $e) {
            Log::error('MoneyFusion status check error', [
                'token' => $token,
                'message' => $e->getMessage(),
            ]);
            
            throw new \Exception('Erreur lors de la vérification du paiement');
        }
    }

    /**
     * Formater les données de paiement
     */
    public function formatPaymentData(
        float $montantFcfa,
        string $telephone,
        string $nomClient,
        int $userId,
        string $transactionReference
    ): array {
        $returnUrl = route('payment.return');
        $webhookUrl = route('payment.webhook');

        return [
            'totalPrice' => (int) $montantFcfa,
            'article' => [
                [
                    'recharge_points' => (int) $montantFcfa,
                ],
            ],
            'personal_Info' => [
                [
                    'userId' => $userId,
                    'transactionRef' => $transactionReference,
                ],
            ],
            'numeroSend' => $telephone,
            'nomclient' => $nomClient,
            'return_url' => $returnUrl,
            'webhook_url' => $webhookUrl,
        ];
    }
}