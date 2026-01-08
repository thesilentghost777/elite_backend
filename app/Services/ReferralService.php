<?php

namespace App\Services;

use App\Models\EliteUser;
use App\Models\ReferralHistory;
use App\Models\SystemSetting;

class ReferralService
{
    /**
     * Récupérer le code de parrainage de l'utilisateur
     */
    public function getMyCode(EliteUser $user): array
    {
        return [
            'code' => $user->referral_code,
            'lien_partage' => config('app.url') . '/register?ref=' . $user->referral_code,
            'points_par_parrainage' => SystemSetting::getPointsPerReferral(),
        ];
    }

    /**
     * Récupérer les statistiques de parrainage
     */
    public function getStats(EliteUser $user): array
    {
        $history = ReferralHistory::where('parrain_id', $user->id)->get();
        
        $totalFilleuls = $history->count();
        $totalPoints = $history->sum('points_gagnes');
        
        // Filleuls ce mois
        $filleulsCeMois = ReferralHistory::where('parrain_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            'total_filleuls' => $totalFilleuls,
            'total_points_gagnes' => $totalPoints,
            'filleuls_ce_mois' => $filleulsCeMois,
            'points_par_parrainage' => SystemSetting::getPointsPerReferral(),
        ];
    }

    /**
     * Récupérer l'historique des parrainages
     */
    public function getHistory(EliteUser $user, int $limit = 20): array
    {
        $history = ReferralHistory::where('parrain_id', $user->id)
            ->with('filleul:id,nom,prenom,telephone,created_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $history->map(function ($item) {
            return [
                'id' => $item->id,
                'filleul' => [
                    'nom' => $item->filleul->nom,
                    'prenom' => $item->filleul->prenom,
                    'telephone' => substr($item->filleul->telephone, 0, 4) . '****' . substr($item->filleul->telephone, -2),
                ],
                'points_gagnes' => $item->points_gagnes,
                'date' => $item->created_at->format('d/m/Y H:i'),
            ];
        })->toArray();
    }

    /**
     * Récupérer les infos du parrain de l'utilisateur
     */
    public function getMyParrain(EliteUser $user): ?array
    {
        if (!$user->referred_by || $user->referred_by === SystemSetting::getDefaultReferralCode()) {
            return null;
        }

        $parrain = EliteUser::where('referral_code', $user->referred_by)
            ->select('id', 'nom', 'prenom', 'ville')
            ->first();

        if (!$parrain) {
            return null;
        }

        return [
            'nom' => $parrain->nom,
            'prenom' => $parrain->prenom,
            'ville' => $parrain->ville,
        ];
    }
}
