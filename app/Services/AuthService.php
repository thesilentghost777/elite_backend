<?php

namespace App\Services;

use App\Models\EliteUser;
use App\Models\ReferralHistory;
use App\Models\SystemSetting;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Inscription d'un nouvel utilisateur
     */
    public function register(array $data): EliteUser
    {
        return DB::transaction(function () use ($data) {
            // Vérifier le code de parrainage
            $referralCode = $data['referral_code'] ?? SystemSetting::getDefaultReferralCode();
            
            $parrain = null;
            if ($referralCode !== SystemSetting::getDefaultReferralCode()) {
                $parrain = EliteUser::where('referral_code', $referralCode)->first();
                if (!$parrain) {
                    throw ValidationException::withMessages([
                        'referral_code' => ['Code de parrainage invalide.']
                    ]);
                }
            }

            // Créer l'utilisateur
            $user = EliteUser::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'telephone' => $data['telephone'],
                'email' => $data['email'] ?? null,
                'dernier_diplome' => $data['dernier_diplome'],
                'ville' => $data['ville'],
                'password' => Hash::make($data['password']),
                'referral_code' => EliteUser::generateReferralCode(),
                'referred_by' => $referralCode,
            ]);

            // Attribuer les points au parrain
            if ($parrain) {
                $this->creditReferralPoints($parrain, $user);
            }

            return $user;
        });
    }

    /**
     * Connexion de l'utilisateur
     */
    public function login(array $credentials): array
    {
        $user = EliteUser::where('telephone', $credentials['telephone'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'telephone' => ['Les identifiants sont incorrects.']
            ]);
        }

        $token = $user->createToken('elite-mobile')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'requires_correspondence' => !$user->correspondence_completed,
            'requires_profile_choice' => $user->correspondence_completed && !$user->profile_chosen,
        ];
    }

    /**
     * Déconnexion
     */
    public function logout(EliteUser $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Vérifier si un code de parrainage est valide
     */
    public function checkReferralCode(string $code): array
    {
        if ($code === SystemSetting::getDefaultReferralCode()) {
            return [
                'valid' => true,
                'is_default' => true,
                'parrain' => null,
            ];
        }

        $parrain = EliteUser::where('referral_code', $code)
            ->select('id', 'nom', 'prenom', 'ville')
            ->first();

        return [
            'valid' => $parrain !== null,
            'is_default' => false,
            'parrain' => $parrain ? [
                'nom' => $parrain->nom,
                'prenom' => $parrain->prenom,
                'ville' => $parrain->ville,
            ] : null,
        ];
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfile(EliteUser $user, array $data): EliteUser
    {
        $updateData = [];

        if (isset($data['nom'])) $updateData['nom'] = $data['nom'];
        if (isset($data['prenom'])) $updateData['prenom'] = $data['prenom'];
        if (isset($data['email'])) $updateData['email'] = $data['email'];
        if (isset($data['ville'])) $updateData['ville'] = $data['ville'];
        if (isset($data['photo_url'])) $updateData['photo_url'] = $data['photo_url'];

        if (isset($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        return $user->fresh();
    }

    /**
     * Créditer les points de parrainage
     */
    private function creditReferralPoints(EliteUser $parrain, EliteUser $filleul): void
    {
        $points = SystemSetting::getPointsPerReferral();

        // Créditer le parrain
        $parrain->addPoints($points);

        // Enregistrer l'historique
        ReferralHistory::create([
            'parrain_id' => $parrain->id,
            'filleul_id' => $filleul->id,
            'points_gagnes' => $points,
        ]);

        // Créer la transaction
        Transaction::create([
            'user_id' => $parrain->id,
            'type' => 'parrainage',
            'points' => $points,
            'reference' => Transaction::generateReference(),
            'description' => "Bonus parrainage - {$filleul->full_name}",
            'metadata' => [
                'filleul_id' => $filleul->id,
                'filleul_nom' => $filleul->full_name,
            ],
            'statut' => 'complete',
        ]);
    }
}
