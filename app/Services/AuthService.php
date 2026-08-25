<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\EliteUser;
use App\Models\Partner;
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

            // Vérification et résolution obligatoire du code
            $referralCode = $data['referral_code'] ?? '';
            $resolved = $this->resolveReferral($referralCode);

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
                'partner_id'       => $resolved['partner']?->id,
                'referred_by'      => $resolved['code'],
                'provider'         => $isByEmail ? 'email' : 'phone',
                'email_verified'   => false,
            ]);

            if ($resolved['type'] === 'user' && $resolved['parrain']) {
                $this->creditReferralPoints($resolved['parrain'], $user);
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
            $user->load('partner');
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
        $referralCode = $data['referral_code'] ?? '';
        $resolved = $this->resolveReferral($referralCode);

        $updateData = [
            'nom'             => $data['nom'],
            'prenom'          => $data['prenom'],
            'dernier_diplome' => $data['dernier_diplome'],
            'ville'           => $data['ville'],
            'referred_by'     => $resolved['code'],
        ];

        if ($resolved['partner']) {
            $updateData['partner_id'] = $resolved['partner']->id;
        }

        if (!empty($data['telephone']) && empty($user->telephone)) {
            $updateData['telephone'] = $data['telephone'];
        }
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        if ($resolved['type'] === 'user' && $resolved['parrain']) {
            $this->creditReferralPoints($resolved['parrain'], $user);
        }

        $freshUser = $user->fresh();
        $freshUser->load('partner');

        return [
            'user'                    => $freshUser,
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

    public function findReferralEntity(string $code): ?array
    {
        $clean = trim(strtoupper($code));
        if (empty($clean)) {
            return null;
        }

        $stripped = preg_replace('/[^A-Z0-9]/', '', $clean);

        // 1. Partenaire (par code_partenaire ou nom)
        $partner = Partner::where('active', true)
            ->where(function ($query) use ($clean, $stripped) {
                $query->whereRaw('UPPER(TRIM(code_partenaire)) = ?', [$clean])
                      ->orWhereRaw('UPPER(TRIM(nom)) = ?', [$clean])
                      ->orWhere('nom', 'LIKE', "%{$clean}%");
                if (!empty($stripped)) {
                    $query->orWhereRaw("REPLACE(REPLACE(UPPER(code_partenaire), '-', ''), ' ', '') = ?", [$stripped]);
                }
            })->first();

        if ($partner) {
            return [
                'type'       => 'partner',
                'partner'    => $partner,
                'partner_id' => $partner->id,
                'nom'        => $partner->nom,
                'parrain'    => null,
                'code'       => $partner->code_partenaire ?: $clean,
                'label'      => "Centre Partenaire : {$partner->nom}",
            ];
        }

        // 2. Parrain utilisateur
        $parrain = EliteUser::where(function ($query) use ($clean, $stripped) {
            $query->whereRaw('UPPER(TRIM(referral_code)) = ?', [$clean]);
            if (!empty($stripped)) {
                $query->orWhereRaw("REPLACE(REPLACE(UPPER(referral_code), '-', ''), ' ', '') = ?", [$stripped]);
            }
        })->first();

        if ($parrain) {
            return [
                'type'       => 'user',
                'partner'    => null,
                'parrain'    => $parrain,
                'code'       => $parrain->referral_code,
                'label'      => "Parrain : {$parrain->prenom} {$parrain->nom}" . ($parrain->ville ? " ({$parrain->ville})" : ""),
            ];
        }

        // 3. Code système officiel (ELITE2026, ELITE2024, etc.)
        $defaultCode = strtoupper(trim(SystemSetting::getDefaultReferralCode()));
        $validSystemCodes = array_filter(array_unique([$defaultCode, 'ELITE2026', 'ELITE2024', 'ELITE']));
        if (in_array($clean, $validSystemCodes, true) || (!empty($stripped) && in_array($stripped, $validSystemCodes, true))) {
            return [
                'type'    => 'default',
                'partner' => null,
                'parrain' => null,
                'code'    => $defaultCode ?: 'ELITE2026',
                'label'   => 'Code Officiel Elite',
            ];
        }

        return null;
    }

    public function checkReferralCode(string $code): array
    {
        $entity = $this->findReferralEntity($code);
        if ($entity) {
            return array_merge(['valid' => true], $entity);
        }

        return [
            'valid'   => false,
            'message' => 'Code de parrainage ou de centre partenaire invalide.',
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

    public function resolveReferral(string $referralCode): array
    {
        $code = trim($referralCode);
        if (empty($code)) {
            throw ValidationException::withMessages([
                'referral_code' => ['Le code de parrainage ou de centre partenaire est obligatoire.'],
            ]);
        }

        $entity = $this->findReferralEntity($code);
        if ($entity) {
            return $entity;
        }

        throw ValidationException::withMessages([
            'referral_code' => ['Le code de parrainage ou de centre partenaire est invalide.'],
        ]);
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
        $user->load('partner');
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