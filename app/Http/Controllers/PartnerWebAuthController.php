<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PartnerWebAuthController extends Controller
{
    public function showLogin()
    {
        return view('partner.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $partner = Partner::where('email', $credentials['email'])->where('active', true)->first();
        if (!$partner || !Hash::check($credentials['password'], $partner->password)) {
            return back()->withErrors(['email' => 'Identifiants invalides.'])->withInput();
        }
        Auth::guard('partner_web')->login($partner, true);
        $request->session()->regenerate();
        return redirect()->route('partner.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('partner_web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('partner.login');
    }
}