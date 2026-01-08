<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\CashCode;
use App\Models\EliteUser;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(30);

        $stats = [
            'total_depots' => Transaction::where('type', 'depot')->where('statut', 'complete')->sum('montant_fcfa'),
            'total_achats' => Transaction::where('type', 'achat_pack')->where('statut', 'complete')->sum('points'),
            'total_transferts' => Transaction::where('type', 'transfert_envoi')->where('statut', 'complete')->sum('points'),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    public function cashCodes()
    {
        $codes = CashCode::with(['creator', 'assignedUser', 'usedByUser'])->latest()->paginate(20);
        return view('admin.transactions.cash-codes', compact('codes'));
    }

    public function createCashCode()
    {
        $users = EliteUser::orderBy('nom')->get();
        return view('admin.transactions.create-cash-code', compact('users'));
    }

    public function storeCashCode(Request $request)
    {
        $validated = $request->validate([
            'montant_fcfa' => 'required|numeric|min:100',
            'user_id' => 'nullable|exists:elite_users,id',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $tauxConversion = SystemSetting::getValue('taux_conversion_fcfa_points', 500);
        $points = $validated['montant_fcfa'] / $tauxConversion;

        CashCode::create([
            'code' => 'CASH-' . strtoupper(Str::random(8)),
            'montant_fcfa' => $validated['montant_fcfa'],
            'points' => $points,
            'user_id' => $validated['user_id'],
            'created_by' => auth()->id(),
            'expires_at' => $validated['expires_at'],
        ]);

        return redirect()->route('admin.cash-codes.index')->with('success', 'Code caisse créé');
    }

    public function deleteCashCode(CashCode $cashCode)
    {
        if ($cashCode->used_at) {
            return back()->with('error', 'Impossible de supprimer un code déjà utilisé');
        }

        $cashCode->delete();
        return back()->with('success', 'Code supprimé');
    }

    public function settings()
    {
        $settings = [
            'taux_conversion_fcfa_points' => SystemSetting::getValue('taux_conversion_fcfa_points', 500),
            'points_parrainage' => SystemSetting::getValue('points_parrainage', 1),
            'code_parrainage_defaut' => SystemSetting::getValue('code_parrainage_defaut', 'ELITE2024'),
        ];

        return view('admin.transactions.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'taux_conversion_fcfa_points' => 'required|numeric|min:1',
            'points_parrainage' => 'required|numeric|min:0.1',
            'code_parrainage_defaut' => 'required|string|max:20',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::setValue($key, $value);
        }

        return back()->with('success', 'Paramètres mis à jour');
    }
}
