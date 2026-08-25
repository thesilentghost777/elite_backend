<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPaymentInstallment;
use App\Services\PartnerPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstallmentController extends Controller
{
    public function index(Request $request, PartnerPaymentService $service): JsonResponse
    {
        $installments = $service->getOrCreateUserInstallments($request->user());
        return response()->json(['success' => true, 'data' => $installments]);
    }

    public function pay(Request $request, UserPaymentInstallment $installment, PartnerPaymentService $service): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $service->pay($request->user(), $installment)]);
    }
}