<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\EliteUser;
use App\Models\ReferralHistory;
use App\Models\SystemSetting;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(private FirebaseService $firebase) {}

    // ──────────────────────────────────────────
    // INSCRIPTION
    // ──────────────────────────────────────────

    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $isByEmail = !empty($data['email']) && empty($data['telephone']);

            // Vérification du code de parrainage
            $referralCode = $data['referral_code'] ?? SystemSetting::getDefaultReferralCode();
            $parrain = $this->resolveParrain($referralCode);

            // Création de l'utilisateur
            $user = EliteUser::create([
                'nom'              => $data['nom'],
                'prenom'           => $data['prenom'],
                'telephone'        => $data['telephone'] ?? null,
                'email'            => $data['email'] ?? null,
                'dernier_diplome'  => $data['dernier_diplome'],
                'ville'            => $data['ville'],
                'password'         => Hash::make($data['password']),
                'referral_code'    => EliteUser::generateReferralCode(),
                'referred_by'      => $referralCode,
                'provider'         => $isByEmail ? 'email' : 'phone',
                'email_verified'   => false,
            ]);

            if ($parrain) {
                $this->creditReferralPoints($parrain, $user);
            }

            // Par email → envoyer OTP, pas de token encore
            if ($isByEmail) {
                $this->sendOtp($user);
                return [
                    'needs_otp' => true,
                    'email'     => $user->email,
                    'message'   => 'Un code de vérification a été envoyé à votre adresse email.',
                ];
            }

            // Par téléphone → token direct
            $token = $user->createToken('elite-mobile')->plainTextToken;
            return [
                'needs_otp'             => false,
                'user'                  => $user,
                'token'                 => $token,
                'requires_correspondence' => !$user->correspondence_completed,
                'requires_profile_choice' => $user->correspondence_completed && !$user->profile_chosen,
            ];
        });
    }

    // ──────────────────────────────────────────
    // CONNEXION CLASSIQUE
    // ──────────────────────────────────────────

    public function login(?string $telephone, ?string $email, string $password): array
    {
        if ($telephone) {
            $user = EliteUser::where('telephone', $telephone)->first();
        } elseif ($email) {
            $user = EliteUser::where('email', $email)->first();
        } else {
            throw ValidationException::withMessages([
                'identifier' => ['Téléphone ou email requis.'],
            ]);
        }

        if (!$user || !$user->password || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['Les identifiants sont incorrects.'],
            ]);
        }

        // Email non vérifié
        if ($user->provider === 'email' && !$user->email_verified) {
            $this->sendOtp($user);
            return [
                'needs_otp' => true,
                'email'     => $user->email,
                'message'   => 'Votre email n\'est pas encore vérifié. Un nouveau code vous a été envoyé.',
            ];
        }

        $token = $user->createToken('elite-mobile')->plainTextToken;

        return [
            'needs_otp'               => false,
            'user'                    => $user,
            'token'                   => $token,
            'requires_correspondence' => !$user->correspondence_completed,
            'requires_profile_choice' => $user->correspondence_completed && !$user->profile_chosen,
        ];
    }

    // ──────────────────────────────────────────
    // CONNEXION SOCIALE (Google / Apple)
    // ──────────────────────────────────────────

    public function socialLogin(string $firebaseToken, string $provider): array
    {
        $firebaseUser = $this->firebase->verifyToken($firebaseToken);

        if (!$firebaseUser) {
            throw ValidationException::withMessages([
                'firebase_token' => ['Token Firebase invalide ou expiré.'],
            ]);
        }

        $firebaseUid = $firebaseUser['localId'];
        $email       = $firebaseUser['email'] ?? null;
        $displayName = $firebaseUser['displayName'] ?? '';
        $photoUrl    = $firebaseUser['photoUrl'] ?? null;

        // Chercher par firebase_uid d'abord, puis par email
        $user = EliteUser::where('firebase_uid', $firebaseUid)->first()
             ?? ($email ? EliteUser::where('email', $email)->first() : null);

        if ($user) {
            // Mise à jour du firebase_uid si nécessaire
            $user->update([
                'firebase_uid' => $firebaseUid,
                'email_verified' => true,
                'photo_url'    => $user->photo_url ?? $photoUrl,
            ]);
        } else {
            // Nouvel utilisateur social — profil incomplet
            [$prenom, $nom] = $this->splitDisplayName($displayName);
            $user = EliteUser::create([
                'firebase_uid'   => $firebaseUid,
                'email'          => $email,
                'nom'            => $nom ?: 'Utilisateur',
                'prenom'         => $prenom ?: 'Nouveau',
                'provider'       => $provider,
                'email_verified' => true,
                'referral_code'  => EliteUser::generateReferralCode(),
                'photo_url'      => $photoUrl,
                'dernier_diplome' => 'secondaire', // placeholder
                'ville'          => '',            // placeholder
                'referred_by'    => SystemSetting::getDefaultReferralCode(),
                'solde_points'   => 0,
            ]);
        }

        $token           = $user->createToken('elite-mobile')->plainTextToken;
        $needsCompletion = !$user->isProfileComplete() || empty($user->dernier_diplome) || empty($user->ville);

        return [
            'user'                    => $user,
            'token'                   => $token,
            'needs_completion'        => $needsCompletion,
            'requires_correspondence' => !$user->correspondence_completed,
            'requires_profile_choice' => $user->correspondence_completed && !$user->profile_chosen,
        ];
    }

    // ──────────────────────────────────────────
    // COMPLÉTION PROFIL SOCIAL
    // ──────────────────────────────────────────

    public function completeSocialProfile(EliteUser $user, array $data): array
    {
        $referralCode = $data['referral_code'] ?? SystemSetting::getDefaultReferralCode();
        $parrain = null;

        // Parrainage uniquement si pas encore appliqué
        if (empty($user->referred_by) || $user->referred_by === SystemSetting::getDefaultReferralCode()) {
            $parrain = $this->resolveParrain($referralCode);
        }

        $updateData = [
            'nom'             => $data['nom'],
            'prenom'          => $data['prenom'],
            'dernier_diplome' => $data['dernier_diplome'],
            'ville'           => $data['ville'],
            'referred_by'     => $referralCode,
        ];

        if (!empty($data['telephone']) && empty($user->telephone)) {
            $updateData['telephone'] = $data['telephone'];
        }
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        if ($parrain) {
            $this->creditReferralPoints($parrain, $user);
        }

        return [
            'user'                    => $user->fresh(),
            'requires_correspondence' => !$user->correspondence_completed,
            'requires_profile_choice' => $user->correspondence_completed && !$user->profile_chosen,
        ];
    }

    // ──────────────────────────────────────────
    // OTP EMAIL
    // ──────────────────────────────────────────

    public function sendOtp(EliteUser $user): void
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpMail($otp, $user->prenom));
    }

    public function verifyEmailOtp(string $email, string $code): array
    {
        $user = EliteUser::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages(['email' => ['Email introuvable.']]);
        }

        if ($user->email_verified) {
            // Déjà vérifié — juste connecter
            $token = $user->createToken('elite-mobile')->plainTextToken;
            return $this->buildLoginResponse($user, $token);
        }

        if (!$user->otp_code || $user->otp_code !== $code) {
            throw ValidationException::withMessages(['code' => ['Code incorrect.']]);
        }

        if ($user->otp_expires_at && now()->isAfter($user->otp_expires_at)) {
            throw ValidationException::withMessages(['code' => ['Code expiré. Demandez un nouveau code.']]);
        }

        $user->update([
            'email_verified' => true,
            'otp_code'       => null,
            'otp_expires_at' => null,
        ]);

        $token = $user->createToken('elite-mobile')->plainTextToken;
        return $this->buildLoginResponse($user, $token);
    }

    public function resendEmailOtp(string $email): void
    {
        $user = EliteUser::where('email', $email)->first();
        if (!$user || $user->email_verified) return;
        $this->sendOtp($user);
    }

    // ──────────────────────────────────────────
    // DÉCONNEXION / PROFIL
    // ──────────────────────────────────────────

    public function logout(EliteUser $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function checkReferralCode(string $code): array
    {
        if ($code === SystemSetting::getDefaultReferralCode()) {
            return ['valid' => true, 'is_default' => true, 'parrain' => null];
        }

        $parrain = EliteUser::where('referral_code', $code)
            ->select('id', 'nom', 'prenom', 'ville')
            ->first();

        return [
            'valid'      => $parrain !== null,
            'is_default' => false,
            'parrain'    => $parrain ? ['nom' => $parrain->nom, 'prenom' => $parrain->prenom, 'ville' => $parrain->ville] : null,
        ];
    }

    public function updateProfile(EliteUser $user, array $data): EliteUser
    {
        $allowed = ['nom', 'prenom', 'email', 'ville', 'photo_url', 'password'];
        $update  = array_intersect_key($data, array_flip($allowed));

        if (isset($update['password'])) {
            $update['password'] = Hash::make($update['password']);
        }

        $user->update($update);
        return $user->fresh();
    }

    // ──────────────────────────────────────────
    // HELPERS PRIVÉS
    // ──────────────────────────────────────────

    private function resolveParrain(string $referralCode): ?EliteUser
    {
        if ($referralCode === SystemSetting::getDefaultReferralCode()) {
            return null;
        }
        $parrain = EliteUser::where('referral_code', $referralCode)->first();
        if (!$parrain) {
            throw ValidationException::withMessages(['referral_code' => ['Code de parrainage invalide.']]);
        }
        return $parrain;
    }

    private function creditReferralPoints(EliteUser $parrain, EliteUser $filleul): void
    {
        $points = SystemSetting::getPointsPerReferral();
        $parrain->addPoints($points);

        ReferralHistory::create([
            'parrain_id'   => $parrain->id,
            'filleul_id'   => $filleul->id,
            'points_gagnes'=> $points,
        ]);

        Transaction::create([
            'user_id'     => $parrain->id,
            'type'        => 'parrainage',
            'points'      => $points,
            'reference'   => Transaction::generateReference(),
            'description' => "Bonus parrainage - {$filleul->full_name}",
            'metadata'    => ['filleul_id' => $filleul->id, 'filleul_nom' => $filleul->full_name],
            'statut'      => 'complete',
        ]);
    }

    private function buildLoginResponse(EliteUser $user, string $token): array
    {
        return [
            'user'                    => $user,
            'token'                   => $token,
            'requires_correspondence' => !$user->correspondence_completed,
            'requires_profile_choice' => $user->correspondence_completed && !$user->profile_chosen,
        ];
    }

    private function splitDisplayName(string $displayName): array
    {
        $parts  = explode(' ', trim($displayName), 2);
        $prenom = $parts[0] ?? '';
        $nom    = $parts[1] ?? '';
        return [$prenom, $nom];
    }
}