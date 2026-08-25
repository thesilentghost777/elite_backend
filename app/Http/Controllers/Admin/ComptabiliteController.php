<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EliteUser;
use App\Models\Partner;
use App\Models\Pack;
use App\Models\UserPaymentInstallment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ComptabiliteController extends Controller
{
    /**
     * Vue principale de comptabilité et de suivi des apprenants
     */
    public function index(Request $request)
    {
        $data = $this->buildComptabiliteData($request);
        $learners = $data['query']->paginate(25)->withQueryString();
        
        return view('admin.comptabilite.index', array_merge($data, [
            'learners' => $learners,
        ]));
    }

    /**
     * Rapport comptable global imprimable / exportable en PDF
     */
    public function report(Request $request)
    {
        $data = $this->buildComptabiliteData($request);
        $learners = $data['query']->get();

        return view('admin.comptabilite.report', array_merge($data, [
            'learners' => $learners,
            'isPdfExport' => true,
            'generatedAt' => Carbon::now(),
        ]));
    }

    /**
     * Fiche individuelle d'un apprenant imprimable / exportable en PDF
     */
    public function learnerReport(EliteUser $user)
    {
        $user->load([
            'partner',
            'userPacks.pack.modules.lessons',
            'userPacks.installments.planInstallment',
            'quizResults.quiz',
            'transactions' => fn($q) => $q->latest(),
        ]);

        $userPack = $user->userPacks->first();
        $installments = $userPack ? $userPack->installments->sortBy('planInstallment.ordre') : collect();
        
        $totalAttendu = $installments->sum('montant_fcfa');
        if ($totalAttendu == 0 && $userPack && $userPack->pack) {
            $totalAttendu = (float) ($userPack->pack->prix_fcfa ?: 0);
        }

        $totalEncaisse = $installments->where('statut', 'paye')->sum('montant_fcfa');
        if ($installments->isEmpty() && $userPack && $userPack->statut === 'actif') {
            $totalEncaisse = (float) ($userPack->prix_paye ?: 0);
        }

        $totalEnRetard = $installments->where('statut', 'en_retard')->sum('montant_fcfa');
        $totalEnAttente = $installments->where('statut', 'en_attente')->sum('montant_fcfa');
        $resteAPayer = max(0, $totalAttendu - $totalEncaisse);

        return view('admin.comptabilite.learner_report', compact(
            'user',
            'userPack',
            'installments',
            'totalAttendu',
            'totalEncaisse',
            'totalEnRetard',
            'totalEnAttente',
            'resteAPayer'
        ));
    }

    /**
     * Méthode utilitaire de construction des filtres et calculs
     */
    private function buildComptabiliteData(Request $request): array
    {
        $query = EliteUser::with([
            'partner',
            'userPacks.pack',
            'userPacks.installments.planInstallment',
        ]);

        // Filtre Recherche
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'LIKE', $search)
                  ->orWhere('prenom', 'LIKE', $search)
                  ->orWhere('telephone', 'LIKE', $search)
                  ->orWhere('email', 'LIKE', $search)
                  ->orWhere('referral_code', 'LIKE', $search);
            });
        }

        // Filtre Partenaire / Centre
        if ($request->filled('partner_id')) {
            if ($request->partner_id === 'none') {
                $query->whereNull('partner_id');
            } else {
                $query->where('partner_id', $request->partner_id);
            }
        }

        // Filtre Statut de Formation
        if ($request->filled('formation_status')) {
            $query->where('formation_status', $request->formation_status);
        }

        // Filtre Pack / Formation
        if ($request->filled('pack_id')) {
            $packId = $request->pack_id;
            $query->whereHas('userPacks', fn($q) => $q->where('pack_id', $packId));
        }

        // Filtre Période d'inscription
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filtre Statut de Paiement
        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'late') {
                $query->whereHas('userPacks.installments', fn($q) => $q->where('statut', 'en_retard'));
            } elseif ($request->payment_status === 'paid') {
                $query->whereDoesntHave('userPacks.installments', fn($q) => $q->where('statut', '!=', 'paye'))
                      ->whereHas('userPacks.installments');
            } elseif ($request->payment_status === 'pending') {
                $query->whereHas('userPacks.installments', fn($q) => $q->where('statut', 'en_attente'));
            }
        }

        $query->latest();

        // Récupération des données pour les statistiques globales basées sur la sélection
        $matchingUserIds = (clone $query)->pluck('id');

        $allInstallments = UserPaymentInstallment::whereHas('userPack', function ($q) use ($matchingUserIds) {
            $q->whereIn('user_id', $matchingUserIds);
        })->get();

        $totalAttendu = $allInstallments->sum('montant_fcfa');
        $totalEncaisse = $allInstallments->where('statut', 'paye')->sum('montant_fcfa');
        $totalEnRetard = $allInstallments->where('statut', 'en_retard')->sum('montant_fcfa');
        $totalEnAttente = $allInstallments->where('statut', 'en_attente')->sum('montant_fcfa');

        $totalLearners = $matchingUserIds->count();
        $partnerLearners = EliteUser::whereIn('id', $matchingUserIds)->whereNotNull('partner_id')->count();
        $activeLearners = EliteUser::whereIn('id', $matchingUserIds)->where('formation_status', 'active')->count();
        $completedLearners = EliteUser::whereIn('id', $matchingUserIds)->where('formation_status', 'complete')->count();
        $failedLearners = EliteUser::whereIn('id', $matchingUserIds)->where('formation_status', 'failed')->count();

        $tauxRecouvrement = $totalAttendu > 0 ? round(($totalEncaisse / $totalAttendu) * 100, 1) : 0;

        $partners = Partner::where('active', true)->orderBy('nom')->get();
        $packs = Pack::active()->orderBy('nom')->get();

        return [
            'query' => $query,
            'totalLearners' => $totalLearners,
            'partnerLearners' => $partnerLearners,
            'activeLearners' => $activeLearners,
            'completedLearners' => $completedLearners,
            'failedLearners' => $failedLearners,
            'totalAttendu' => $totalAttendu,
            'totalEncaisse' => $totalEncaisse,
            'totalEnRetard' => $totalEnRetard,
            'totalEnAttente' => $totalEnAttente,
            'tauxRecouvrement' => $tauxRecouvrement,
            'partners' => $partners,
            'packs' => $packs,
            'filters' => $request->all(),
        ];
    }
}
