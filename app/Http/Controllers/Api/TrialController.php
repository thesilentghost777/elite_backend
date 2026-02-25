<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrialController extends Controller
{
    public function status(Request $request)
    {
        $user = $request->user();

        if ($user->account_activated) {
            return response()->json([
                'account_activated'       => true,
                'trial_started'           => true,
                'trial_expired'           => false,
                'trial_remaining_seconds' => null,
                'activated_at'            => $user->activated_at,
            ]);
        }

        if (is_null($user->trial_started_at)) {
            return response()->json([
                'account_activated'       => false,
                'trial_started'           => false,
                'trial_expired'           => false,
                'trial_remaining_seconds' => 600,
            ]);
        }

        $now       = Carbon::now();
        $expiresAt = Carbon::parse($user->trial_expires_at);
        $remaining = max(0, (int) $now->diffInSeconds($expiresAt, false));
        $expired   = $now->greaterThanOrEqualTo($expiresAt);

        return response()->json([
            'account_activated'       => false,
            'trial_started'           => true,
            'trial_expired'           => $expired,
            'trial_remaining_seconds' => $remaining,
            'trial_expires_at'        => $user->trial_expires_at,
        ]);
    }

    public function start(Request $request)
    {
        $user = $request->user();

        if ($user->account_activated) {
            return response()->json([
                'account_activated'       => true,
                'trial_remaining_seconds' => null,
            ]);
        }

        if (!is_null($user->trial_started_at)) {
            $remaining = max(0, (int) Carbon::now()->diffInSeconds(
                Carbon::parse($user->trial_expires_at), false
            ));
            return response()->json([
                'account_activated'       => false,
                'trial_started'           => true,
                'trial_remaining_seconds' => $remaining,
                'trial_expires_at'        => $user->trial_expires_at,
            ]);
        }

        $now = Carbon::now();
        $user->trial_started_at = $now;
        $user->trial_expires_at = $now->copy()->addMinutes(10);
        $user->save();

        return response()->json([
            'account_activated'       => false,
            'trial_started'           => true,
            'trial_remaining_seconds' => 600,
            'trial_expires_at'        => $user->trial_expires_at,
        ]);
    }

    public function activate(Request $request)
    {
        $user           = $request->user();
        $activationCost = 3; // 3 points = 1 000 FCFA

        if ($user->account_activated) {
            return response()->json([
                'message'           => 'Compte déjà activé.',
                'account_activated' => true,
            ], 400);
        }

        if ($user->solde_points < $activationCost) {
            return response()->json([
                'message'      => "Solde insuffisant. Il vous faut {$activationCost} points (1 000 FCFA) pour activer votre compte.",
                'error_code'   => 'INSUFFICIENT_BALANCE',
                'solde_points' => $user->solde_points,
                'required'     => $activationCost,
            ], 422);
        }

        DB::transaction(function () use ($user, $activationCost) {
    $user->solde_points      -= $activationCost;
    $user->account_activated  = true;
    $user->activated_at       = Carbon::now();
    $user->save();

    DB::table('transactions')->insert([
        'user_id'     => $user->id,
        // 'user_type' => supprimé (colonne inexistante)
        'type'        => 'achat_pack',  // valeur valide de l'enum
        'montant_fcfa'=> 1000,
        'points'      => $activationCost,
        'reference'   => 'ACT-' . $user->id . '-' . time(),
        'description' => 'Activation du compte Elite (1 000 FCFA = 3 points)',
        'statut'      => 'complete',    // pas 'completed'
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);
});

        return response()->json([
            'message'           => 'Compte activé avec succès !',
            'account_activated' => true,
            'activated_at'      => $user->activated_at,
            'solde_points'      => $user->solde_points,
        ]);
    }
}