<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EliteUser;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class RegisterController extends Controller
{
    /**
     * Afficher la page d'inscription web
     * Le code de parrainage peut être passé en paramètre ?ref=CODE
     */
    public function showRegisterForm(Request $request)
    {
        $referralCode = $request->query('ref', '');
        $parrainName  = null;

        if ($referralCode) {
            $parrain = EliteUser::where('referral_code', $referralCode)->first();
            if ($parrain) {
                $parrainName = $parrain->prenom . ' ' . $parrain->nom;
            }
        }

        return view('web.register', compact('referralCode', 'parrainName'));
    }

    /**
     * Traiter l'inscription web
     */
    public function register(Request $request)
    {
        $request->validate([
            'nom'                  => 'required|string|max:255',
            'prenom'               => 'required|string|max:255',
            'telephone'            => 'required|string|unique:elite_users,telephone',
            'email'                => 'nullable|email|unique:elite_users,email',
            'dernier_diplome'      => 'required|string|max:255',
            'ville'                => 'required|string|max:255',
            'password'             => 'required|string|min:6|confirmed',
            'referral_code'        => 'nullable|string',
        ]);

        // Vérifier le code de parrainage
        $referralCode = null;
        if ($request->referral_code) {
            $parrain = EliteUser::where('referral_code', $request->referral_code)->first();
            if ($parrain) {
                $referralCode = $request->referral_code;
            }
        }

        $user = EliteUser::create([
            'nom'             => $request->nom,
            'prenom'          => $request->prenom,
            'telephone'       => $request->telephone,
            'email'           => $request->email,
            'dernier_diplome' => $request->dernier_diplome,
            'ville'           => $request->ville,
            'password'        => Hash::make($request->password),
            'referred_by'     => $referralCode,
            'referral_code'   => strtoupper(substr(md5(uniqid()), 0, 8)),
        ]);

        // Rediriger vers le store approprié selon l'appareil
        $userAgent = $request->header('User-Agent', '');
        $isIos     = stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false;

        $storeUrl = $isIos
            ? 'https://apps.apple.com/app/elite-20/id0000000000' // Remplacer par l'App Store ID réel
            : 'https://play.google.com/store/apps/details?id=com.ghost777xsorganization.elite20';

        return view('web.register_success', compact('user', 'storeUrl'));
    }

    /**
     * Redirection vers Google OAuth
     */
    public function redirectToGoogle(Request $request)
    {
        $referralCode = $request->query('ref', '');
        session(['referral_code' => $referralCode]);
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback Google OAuth
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser   = Socialite::driver('google')->user();
            $referralCode = session('referral_code', '');

            $user = EliteUser::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = EliteUser::create([
                    'nom'             => $googleUser->getName(),
                    'prenom'          => '',
                    'email'           => $googleUser->getEmail(),
                    'telephone'       => '',
                    'dernier_diplome' => '',
                    'ville'           => '',
                    'password'        => Hash::make(uniqid()),
                    'referred_by'     => $referralCode ?: null,
                    'referral_code'   => strtoupper(substr(md5(uniqid()), 0, 8)),
                    'google_id'       => $googleUser->getId(),
                ]);
            }

            $userAgent = $request->header('User-Agent', '');
            $isIos     = stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false;

            $storeUrl = $isIos
                ? 'https://apps.apple.com/app/elite-20/id0000000000'
                : 'https://play.google.com/store/apps/details?id=com.ghost777xsorganization.elite20';

            return view('web.register_success', compact('user', 'storeUrl'));

        } catch (\Exception $e) {
            return redirect()->route('web.register')->withErrors(['google' => 'Erreur lors de la connexion Google.']);
        }
    }

    /**
     * Envoyer un OTP (pour inscription par email/téléphone)
     */
    public function sendOtp(Request $request)
    {
        $request->validate(['telephone' => 'required|string']);

        $otp = rand(100000, 999999);
        session(['otp' => $otp, 'otp_telephone' => $request->telephone, 'otp_expires_at' => now()->addMinutes(10)]);

        // TODO: Intégrer un service SMS réel ici (ex: Twilio, Orange SMS)
        // Pour le dev : on retourne l'OTP dans la réponse (à supprimer en prod)
        return response()->json(['success' => true, 'message' => 'OTP envoyé.', 'dev_otp' => $otp]);
    }

    /**
     * Vérifier l'OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string', 'telephone' => 'required|string']);

        if (
            session('otp') != $request->otp ||
            session('otp_telephone') != $request->telephone ||
            now()->isAfter(session('otp_expires_at'))
        ) {
            return response()->json(['success' => false, 'message' => 'OTP invalide ou expiré.'], 422);
        }

        session()->forget(['otp', 'otp_telephone', 'otp_expires_at']);
        return response()->json(['success' => true, 'message' => 'OTP vérifié.']);
    }
}