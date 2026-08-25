<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PartnerAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $partner = Partner::where('email', $data['email'])->where('active', true)->first();
        if (!$partner || !Hash::check($data['password'], $partner->password)) {
            return response()->json(['success' => false, 'message' => 'Identifiants invalides.'], 422);
        }
        return response()->json(['success' => true, 'data' => [
            'partner' => $partner,
            'token' => $partner->createToken('partner-mobile')->plainTextToken,
        ]]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['success' => true]);
    }
}