<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::withCount([
            'learners',
            'learners as active_learners_count' => fn($q) => $q->where('formation_status', 'active'),
            'learners as failed_learners_count' => fn($q) => $q->where('formation_status', 'failed'),
            'learners as completed_learners_count' => fn($q) => $q->where('formation_status', 'complete'),
        ])->latest()->paginate(20);

        $totalPartners = Partner::count();
        $activePartners = Partner::where('active', true)->count();
        $totalLearners = \App\Models\EliteUser::whereNotNull('partner_id')->count();

        $installments = \App\Models\UserPaymentInstallment::whereHas('userPack.user', fn($q) => $q->whereNotNull('partner_id'))->get();
        $totalCollected = $installments->where('statut', 'paye')->sum('montant_fcfa');
        $totalLate = $installments->where('statut', 'en_retard')->sum('montant_fcfa');

        return view('admin.partners.index', compact('partners', 'totalPartners', 'activePartners', 'totalLearners', 'totalCollected', 'totalLate'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'code_partenaire' => 'nullable|string|max:50|unique:partners,code_partenaire',
            'email' => 'required|email|unique:partners,email',
            'telephone' => 'nullable|string|max:30',
            'password' => 'required|string|min:8',
        ]);

        if (empty($data['code_partenaire'])) {
            $data['code_partenaire'] = Partner::generatePartnerCode($data['nom']);
        } else {
            $data['code_partenaire'] = strtoupper(trim($data['code_partenaire']));
        }

        $data['password'] = Hash::make($data['password']);
        Partner::create($data + ['active' => true]);
        return back()->with('success', 'Partenaire créé avec succès.');
    }

    public function toggle(Partner $partner)
    {
        $partner->update(['active' => !$partner->active]);
        return back()->with('success', 'Statut du partenaire mis à jour.');
    }
}