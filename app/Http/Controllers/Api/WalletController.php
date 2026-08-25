<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function balance(Request $request): JsonResponse
    {
        $user    = $request->user();
        $myPacks = $user->userPacks()->where('statut', 'actif')->with('pack')->get();

        $formationsData = $myPacks->map(fn($up) => [
            'pack_id'     => $up->pack_id,
            'pack_nom'    => $up->pack->nom ?? '',
            'progression' => $up->progression ?? 0,
        ])->toArray();

        $points = (float) $user->solde_points;
        $soldeFcfa = $points * SystemSetting::getTauxConversionFcfaPoints();

        return response()->json([
            'success' => true,
            'data' => [
                'points'             => $points,
                'equivalent_fcfa'    => $soldeFcfa,
                'solde_fcfa'         => $soldeFcfa,
                'taux_conversion'    => SystemSetting::getTauxConversionFcfaPoints(),
                'formations'         => $formationsData,
                'total_formations'   => $myPacks->count(),
                'account_activated'  => $user->account_activated ?? false,
            ],
        ]);
    }

    public function deposit(Request $request): JsonResponse
    {
        $request->validate(['montant_fcfa' => 'required|numeric|min:500']);
        return response()->json([
            'success' => true,
            'data'    => $this->paymentService->deposit($request->user(), $request->montant_fcfa),
        ]);
    }

    public function useCashCode(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);
        return response()->json([
            'success' => true,
            'data'    => $this->paymentService->useCashCode($request->user(), $request->code),
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->paymentService->getTransactionHistory($request->user()),
        ]);
    }

    public function purchasePack(Request $request, int $id): JsonResponse
    {
        $request->merge(['pack_id' => $id]);
        return app(\App\Http\Controllers\Api\PaymentController::class)
            ->initiatePackPayment($request);
    }

    public function myPacks(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->paymentService->getUserPacks($request->user()),
        ]);
    }
}