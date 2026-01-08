<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|unique:elite_users,telephone',
            'email' => 'nullable|email|unique:elite_users,email',
            'dernier_diplome' => 'required|in:BEPC,Probatoire,BAC,Licence,Master',
            'ville' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'referral_code' => 'nullable|string',
        ]);

        $user = $this->authService->register($data);
        $loginData = $this->authService->login(['telephone' => $data['telephone'], 'password' => $data['password']]);

        return response()->json(['success' => true, 'data' => $loginData], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'telephone' => 'required|string',
            'password' => 'required|string',
        ]);

        $data = $this->authService->login($credentials);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return response()->json(['success' => true, 'message' => 'Déconnexion réussie']);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Charger le profil de carrière choisi si disponible
        $careerProfile = null;
        if ($user->profile_chosen) {
            $profileChoice = $user->profileChoice()->with('profile')->first();
            if ($profileChoice) {
                $careerProfile = $profileChoice->profile;
            }
        }

        return response()->json([
            'success' => true, 
            'data' => [
                'user' => $user,
                'career_profile' => $careerProfile,
                'requires_correspondence' => !$user->correspondence_completed,
                'requires_profile_choice' => $user->correspondence_completed && !$user->profile_chosen,
            ]
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|nullable|email|unique:elite_users,email,' . $request->user()->id,
            'ville' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:6|confirmed',
            'photo_url' => 'sometimes|nullable|string',
        ]);

        // Si un mot de passe est fourni, le hasher
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            // Retirer password_confirmation car non nécessaire dans la DB
            unset($data['password_confirmation']);
        }

        $user = $this->authService->updateProfile($request->user(), $data);
        
        // Charger le profil de carrière si disponible
        $careerProfile = null;
        if ($user->profile_chosen) {
            $profileChoice = $user->profileChoice()->with('profile')->first();
            if ($profileChoice) {
                $careerProfile = $profileChoice->profile;
            }
        }

        return response()->json([
            'success' => true, 
            'data' => [
                'user' => $user->fresh(),
                'career_profile' => $careerProfile,
            ]
        ]);
    }

    public function checkReferralCode(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string']);
        $result = $this->authService->checkReferralCode($request->code);
        return response()->json(['success' => true, 'data' => $result]);
    }
}