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
        $codes = CashCode::with(['creator', 'assignedUser', 'usedByUser', 'pack', 'partner'])->latest()->paginate(20);
        return view('admin.transactions.cash-codes', compact('codes'));
    }

    public function createCashCode()
    {
        $users = EliteUser::orderBy('nom')->get();
        $packs = \App\Models\Pack::active()->orderBy('nom')->get();
        $partners = \App\Models\Partner::where('active', true)->orderBy('nom')->get();
        $tauxConversion = SystemSetting::getValue('taux_conversion_fcfa_points', 500);
        return view('admin.transactions.create-cash-code', compact('users', 'packs', 'partners', 'tauxConversion'));
    }

    public function storeCashCode(Request $request)
    {
        $validated = $request->validate([
            'montant_fcfa' => 'nullable|numeric|min:0',
            'tranches' => 'nullable|array',
            'tranches.*' => 'integer|min:1|max:5',
            'pack_id' => 'nullable|exists:packs,id',
            'partner_id' => 'nullable|exists:partners,id',
            'user_id' => 'nullable|exists:elite_users,id',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $standardAmounts = [
            1 => 10000,
            2 => 200000,
            3 => 135000,
            4 => 55000,
            5 => 55000,
        ];

        $tranches = isset($validated['tranches']) ? array_map('intval', $validated['tranches']) : [];
        $montant = !empty($validated['montant_fcfa']) ? (float) $validated['montant_fcfa'] : 0;

        if ($montant <= 0 && !empty($tranches)) {
            $montant = (float) array_sum(array_map(fn($t) => $standardAmounts[$t] ?? 0, $tranches));
        }

        if ($montant <= 0 && !empty($validated['pack_id'])) {
            $pack = \App\Models\Pack::find($validated['pack_id']);
            $montant = (float) ($pack?->prix_fcfa_effectif ?? 135000);
        }

        if ($montant <= 0) {
            $montant = 10000;
        }

        $tauxConversion = SystemSetting::getValue('taux_conversion_fcfa_points', 500);
        $points = (int) floor($montant / max(1, $tauxConversion));

        CashCode::create([
            'code' => 'CASH-' . strtoupper(Str::random(8)),
            'montant_fcfa' => $montant,
            'points' => $points,
            'pack_id' => $validated['pack_id'] ?? null,
            'partner_id' => $validated['partner_id'] ?? null,
            'tranches' => !empty($tranches) ? $tranches : null,
            'assigned_to' => $validated['user_id'] ?? null,
            'created_by' => auth()->id(),
            'expires_at' => $validated['expires_at'] ?? null,
            'active' => true,
        ]);

        return redirect()->route('admin.cash-codes.index')->with('success', 'Code caisse créé avec succès');
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
            'points_par_bonne_reponse_quiz' => SystemSetting::getValue('points_par_bonne_reponse_quiz', 500),
            'quiz_cagnotte_fcfa' => SystemSetting::getValue('quiz_cagnotte_fcfa', [0, 1000, 5000, 10000, 25000, 50000, 100000, 250000, 500000, 750000, 1000000]),
            'points_emploi' => SystemSetting::getValue('points_emploi', 0),
            'points_concours' => SystemSetting::getValue('points_concours', 0),
            'points_financement' => SystemSetting::getValue('points_financement', 0),
            'points_bibliotheque' => SystemSetting::getValue('points_bibliotheque', 0),
            'code_parrainage_defaut' => SystemSetting::getValue('code_parrainage_defaut', 'ELITE2026'),
        ];

        return view('admin.transactions.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'taux_conversion_fcfa_points' => 'required|numeric|min:1',
            'points_parrainage' => 'required|numeric|min:0.1',
            'points_par_bonne_reponse_quiz' => 'required|integer|min:0',
            'points_emploi' => 'required|integer|min:0',
            'points_concours' => 'required|integer|min:0',
            'points_financement' => 'required|integer|min:0',
            'points_bibliotheque' => 'required|integer|min:0',
            'quiz_cagnotte_fcfa' => 'required|string',
            'code_parrainage_defaut' => 'required|string|max:20',
        ]);

        $jackpot = array_map('intval', array_map('trim', explode(',', $validated['quiz_cagnotte_fcfa'])));
        if (count($jackpot) !== 11) {
            return back()->withErrors(['quiz_cagnotte_fcfa' => 'La cagnotte doit contenir 11 montants, du palier 0 au palier 10.'])->withInput();
        }
        $validated['quiz_cagnotte_fcfa'] = $jackpot;

        foreach ($validated as $key => $value) {
            $type = is_array($value) ? 'json' : ($key === 'code_parrainage_defaut' ? 'string' : 'integer');
            SystemSetting::setValue($key, $value, $type);
        }

        return back()->with('success', 'Paramètres mis à jour');
    }
}
