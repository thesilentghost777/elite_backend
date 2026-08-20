<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoneyFusionService
{
    private string $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.moneyfusion.api_url', 'https://www.pay.moneyfusion.net/paiement/');
    }

    /**
     * Initier un paiement MoneyFusion
     */
    public function initiatePayment(array $paymentData): array
    {
        try {
            Log::info('MoneyFusion initiatePayment', ['data' => $paymentData]);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ])
                ->post($this->apiUrl, $paymentData);

            Log::info('MoneyFusion response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if (!$response->successful()) {
                Log::error('MoneyFusion payment initiation failed', [
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);

                throw new \Exception('Échec de l\'initiation du paiement (HTTP ' . $response->status() . ')');
            }

            $data = $response->json();

            // MoneyFusion retourne { statut: true/false, token: "...", url: "..." }
            $success = $data['statut'] ?? false;

            return [
                'success'     => (bool) $success,
                'token'       => $data['token']   ?? null,
                'message'     => $data['message'] ?? ($success ? 'OK' : 'Erreur MoneyFusion'),
                'payment_url' => $data['url']     ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('MoneyFusion API error', [
                'message' => $e->getMessage(),
            ]);

            throw new \Exception('Erreur de connexion au service de paiement : ' . $e->getMessage());
        }
    }

    /**
     * Vérifier le statut d'un paiement via le token
     */
    public function checkPaymentStatus(string $token): array
    {
        try {
            $url = "https://www.pay.moneyfusion.net/paiementNotif/{$token}";

            Log::info('MoneyFusion checkPaymentStatus', ['url' => $url]);

            $response = Http::timeout(30)->get($url);

            if (!$response->successful()) {
                throw new \Exception('Impossible de vérifier le statut du paiement');
            }

            $data = $response->json();

            Log::info('MoneyFusion status response', ['data' => $data]);

            return [
                'success' => $data['statut'] ?? false,
                'data'    => $data['data']   ?? null,
                'message' => $data['message'] ?? 'Erreur inconnue',
            ];

        } catch (\Exception $e) {
            Log::error('MoneyFusion status check error', [
                'token'   => $token,
                'message' => $e->getMessage(),
            ]);

            throw new \Exception('Erreur lors de la vérification du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Formater les données pour l'API MoneyFusion
     */
    public function formatPaymentData(
        float $montantFcfa,
        string $telephone,
        string $nomClient,
        int $userId,
        string $transactionReference
    ): array {
        $returnUrl  = config('app.url') . '/api/payment/return';
        $webhookUrl = config('app.url') . '/api/payment/webhook';

        return [
            'totalPrice'    => (int) $montantFcfa,
            'article'       => [
                [
                    'recharge_points' => (int) $montantFcfa,
                ],
            ],
            'personal_Info' => [
                [
                    'userId'         => $userId,
                    'transactionRef' => $transactionReference,
                ],
            ],
            'numeroSend'    => $telephone,
            'nomclient'     => $nomClient,
            'return_url'    => $returnUrl,
            'webhook_url'   => $webhookUrl,
        ];
    }
}