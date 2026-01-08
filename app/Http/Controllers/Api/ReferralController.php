<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
{
    /**
     * Obtenir le code de parrainage de l'utilisateur connecté
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function myCode(Request $request)
    {
        try {
            $user = $request->user();

            $shareMessage = "🚀 Lance ta carrière avec Elite 2.0 ! Code {$user->referral_code} pour débloquer ton accès et rejoindre des milliers de professionnels en devenir. Ta réussite commence maintenant ! 💼";
            return response()->json([
                'success' => true,
                'data' => [
                    'code' => $user->referral_code,
                    'share_message' => $shareMessage
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du code de parrainage',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques de parrainage de l'utilisateur
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Request $request)
    {
        try {
            $user = $request->user();

            // Nombre total de filleuls
            $totalFilleuls = DB::table('referral_history')
                ->where('parrain_id', $user->id)
                ->count();

            // Points gagnés via parrainage
            $pointsGagnes = DB::table('referral_history')
                ->where('parrain_id', $user->id)
                ->sum('points_gagnes');

            // Filleuls ce mois
            $fillleulsCeMois = DB::table('referral_history')
                ->where('parrain_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_filleuls' => $totalFilleuls,
                    'points_gagnes' => (int) $pointsGagnes,
                    'filleuls_ce_mois' => $fillleulsCeMois
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir l'historique des parrainages
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Request $request)
    {
        try {
            $user = $request->user();

            $history = DB::table('referral_history')
                ->join('elite_users', 'referral_history.filleul_id', '=', 'elite_users.id')
                ->where('referral_history.parrain_id', $user->id)
                ->select(
                    'elite_users.prenom',
                    'elite_users.nom',
                    'elite_users.ville',
                    'referral_history.points_gagnes',
                    'referral_history.created_at as date_inscription'
                )
                ->orderBy('referral_history.created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'filleul' => [
                            'prenom' => $item->prenom,
                            'nom' => $item->nom,
                            'ville' => $item->ville
                        ],
                        'points_gagnes' => $item->points_gagnes,
                        'date_inscription' => $item->date_inscription
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $history
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'historique',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les informations du parrain de l'utilisateur connecté
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function myParrain(Request $request)
    {
        try {
            $user = $request->user();

            // Si l'utilisateur n'a pas de parrain
            if (!$user->referred_by) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'Vous n\'avez pas été parrainé'
                ], 200);
            }

            // Récupérer les infos du parrain
            $parrain = DB::table('elite_users')
                ->where('referral_code', $user->referred_by)
                ->select('id', 'prenom', 'nom', 'ville', 'referral_code')
                ->first();

            if (!$parrain) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parrain introuvable'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'parrain' => [
                        'prenom' => $parrain->prenom,
                        'nom' => $parrain->nom,
                        'ville' => $parrain->ville,
                        'code' => $parrain->referral_code
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du parrain',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}