<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}
    
    public function balance(Request $request): JsonResponse { 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->getBalance($request->user())
        ]); 
    }
    
    public function deposit(Request $request): JsonResponse { 
        $request->validate(['montant_fcfa' => 'required|numeric|min:500']); 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->deposit($request->user(), $request->montant_fcfa)
        ]); 
    }
    
    public function useCashCode(Request $request): JsonResponse { 
        $request->validate(['code' => 'required|string']); 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->useCashCode($request->user(), $request->code)
        ]); 
    }
    
    public function findUser(Request $request): JsonResponse { 
        $request->validate(['telephone' => 'required|string']); 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->findUserForTransfer($request->telephone)
        ]); 
    }
    
    public function transfer(Request $request): JsonResponse { 
        $request->validate([
            'telephone' => 'required|string', 
            'points' => 'required|numeric|min:1', 
            'motif' => 'nullable|string'
        ]); 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->transfer(
                $request->user(), 
                $request->telephone, 
                $request->points, 
                $request->motif
            )
        ]); 
    }
    
    public function transactions(Request $request): JsonResponse { 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->getTransactionHistory($request->user())
        ]); 
    }
    
    // ✅ CORRECTION: Retirer la validation de 'duree'
    public function purchasePack(Request $request, int $id): JsonResponse { 
        // Pas de validation - le pack est maintenant illimité par défaut
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->purchasePack($request->user(), $id)
        ]); 
    }
    
    public function myPacks(Request $request): JsonResponse { 
        return response()->json([
            'success' => true, 
            'data' => $this->paymentService->getUserPacks($request->user())
        ]); 
    }
}