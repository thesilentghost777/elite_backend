<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\CourseSchedule;
use App\Models\Pack;
use App\Models\PartnerPaymentPlan;
use App\Models\Lesson;
use App\Models\UserPaymentInstallment;
use App\Models\EliteUser;
use App\Services\PartnerPaymentService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PartnerWebController extends Controller
{
    public function dashboard()
    {
        $partner = request()->user('partner_web');
        $partner->load(['learners.userPacks.installments.planInstallment', 'packs', 'paymentPlans.installments', 'schedules.pack', 'schedules.lesson']);

        $totalLearners = $partner->learners()->count();
        $activeLearners = $partner->learners()->where('formation_status', 'active')->count();
        $completedLearners = $partner->learners()->where('formation_status', 'complete')->count();
        $failedLearners = $partner->learners()->where('formation_status', 'failed')->count();

        // Calculs comptables globaux
        $allInstallments = UserPaymentInstallment::whereHas('userPack.user', function ($q) use ($partner) {
            $q->where('partner_id', $partner->id);
        })->get();

        $totalAttendu = $allInstallments->sum('montant_fcfa');
        $totalEncaisse = $allInstallments->where('statut', 'paye')->sum('montant_fcfa');
        $totalEnRetard = $allInstallments->where('statut', 'en_retard')->sum('montant_fcfa');
        $totalEnAttente = $allInstallments->where('statut', 'en_attente')->sum('montant_fcfa');

        $packs = Pack::active()->orderBy('nom')->get();
        $schedules = $partner->schedules()->with('pack', 'lesson')->latest()->take(5)->get();

        return view('partner.dashboard', compact(
            'partner',
            'totalLearners',
            'activeLearners',
            'completedLearners',
            'failedLearners',
            'totalAttendu',
            'totalEncaisse',
            'totalEnRetard',
            'totalEnAttente',
            'packs',
            'schedules'
        ));
    }

    public function comptabilite(Request $request)
    {
        $partner = request()->user('partner_web');
        $data = $this->buildPartnerComptabiliteData($request, $partner);

        $learners = $data['query']->latest()->paginate(25)->withQueryString();
        $packs = Pack::active()->orderBy('nom')->get();

        return view('partner.comptabilite', array_merge($data, [
            'partner' => $partner,
            'learners' => $learners,
            'packs' => $packs,
            'filters' => $request->all(),
        ]));
    }

    /**
     * Rapport comptable global du partenaire (Imprimable / PDF)
     */
    public function globalReport(Request $request)
    {
        $partner = request()->user('partner_web');
        $data = $this->buildPartnerComptabiliteData($request, $partner);

        $learners = $data['query']->latest()->get();
        $generatedAt = Carbon::now();

        return view('partner.reports.global', array_merge($data, [
            'partner' => $partner,
            'learners' => $learners,
            'generatedAt' => $generatedAt,
            'filters' => $request->all(),
        ]));
    }

    /**
     * Fiche individuelle d'un apprenant du partenaire (Imprimable / PDF)
     */
    public function learnerReport(Request $request, EliteUser $learner)
    {
        $partner = request()->user('partner_web');

        if ((int) $learner->partner_id !== (int) $partner->id) {
            abort(403, 'Accès non autorisé à cet apprenant.');
        }

        $learner->load([
            'partner',
            'userPacks.pack.modules.lessons',
            'userPacks.installments.planInstallment',
            'quizResults.quiz',
        ]);

        $userPack = $learner->userPacks->first();
        $installments = $userPack ? $userPack->installments->sortBy('planInstallment.ordre') : collect();

        $totalAttendu = $installments->sum('montant_fcfa');
        $totalEncaisse = $installments->where('statut', 'paye')->sum('montant_fcfa');
        $totalEnRetard = $installments->where('statut', 'en_retard')->sum('montant_fcfa');
        $totalEnAttente = $installments->where('statut', 'en_attente')->sum('montant_fcfa');
        $resteAPayer = max(0, $totalAttendu - $totalEncaisse);
        $generatedAt = Carbon::now();

        return view('partner.reports.learner', compact(
            'partner',
            'learner',
            'userPack',
            'installments',
            'totalAttendu',
            'totalEncaisse',
            'totalEnRetard',
            'totalEnAttente',
            'resteAPayer',
            'generatedAt'
        ));
    }

    private function buildPartnerComptabiliteData(Request $request, Partner $partner): array
    {
        $query = $partner->learners()->with(['userPacks.pack', 'userPacks.installments.planInstallment']);

        if ($request->filled('statut')) {
            $query->where('formation_status', $request->statut);
        }

        if ($request->filled('pack_id')) {
            $packId = $request->pack_id;
            $query->whereHas('userPacks', fn($q) => $q->where('pack_id', $packId));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

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

        // Calculs statistiques
        $matchingUserIds = (clone $query)->pluck('id');

        $allInstallments = UserPaymentInstallment::whereHas('userPack.user', function ($q) use ($matchingUserIds) {
            $q->whereIn('id', $matchingUserIds);
        })->get();

        $totalAttendu = $allInstallments->sum('montant_fcfa');
        $totalEncaisse = $allInstallments->where('statut', 'paye')->sum('montant_fcfa');
        $totalEnRetard = $allInstallments->where('statut', 'en_retard')->sum('montant_fcfa');
        $totalEnAttente = $allInstallments->where('statut', 'en_attente')->sum('montant_fcfa');

        $totalLearners = $matchingUserIds->count();
        $activeLearners = EliteUser::whereIn('id', $matchingUserIds)->where('formation_status', 'active')->count();
        $completedLearners = EliteUser::whereIn('id', $matchingUserIds)->where('formation_status', 'complete')->count();
        $failedLearners = EliteUser::whereIn('id', $matchingUserIds)->where('formation_status', 'failed')->count();
        $tauxRecouvrement = $totalAttendu > 0 ? round(($totalEncaisse / $totalAttendu) * 100, 1) : 0;

        return [
            'query' => $query,
            'totalLearners' => $totalLearners,
            'activeLearners' => $activeLearners,
            'completedLearners' => $completedLearners,
            'failedLearners' => $failedLearners,
            'totalAttendu' => $totalAttendu,
            'totalEncaisse' => $totalEncaisse,
            'totalEnRetard' => $totalEnRetard,
            'totalEnAttente' => $totalEnAttente,
            'tauxRecouvrement' => $tauxRecouvrement,
        ];
    }

    public function markInstallmentPaid(Request $request, UserPaymentInstallment $installment, PartnerPaymentService $payments)
    {
        $partner = $request->user('partner_web');
        $installment->load('userPack.user');

        if ((int) $installment->userPack->user->partner_id !== (int) $partner->id) {
            abort(403, 'Accès non autorisé à cet apprenant.');
        }

        $payments->markAsPaidByPartner($installment, $request->input('notes', 'Payé au guichet du centre ' . $partner->nom));

        return back()->with('success', 'Paiement de la tranche enregistré avec succès.');
    }

    public function plans()
    {
        $partner = request()->user('partner_web');
        $partner->load('paymentPlans.pack', 'paymentPlans.installments');
        $packs = Pack::active()->orderBy('nom')->get();

        return view('partner.plans', compact('partner', 'packs'));
    }

    public function savePlan(Request $request, PartnerPaymentService $payments)
    {
        $data = $request->validate([
            'pack_id' => 'required|exists:packs,id',
            'nom' => 'required|string|max:255',
            'date_fin_formation' => 'required|date',
            'installments' => 'required|array|size:5',
            'installments.*.libelle' => 'required|string|max:255',
            'installments.*.montant_fcfa' => 'required|numeric|min:0',
            'installments.*.delai_jours' => 'required|integer|min:0',
        ]);

        $partner = $request->user('partner_web');
        $partner->packs()->syncWithoutDetaching([$data['pack_id'] => ['active' => true]]);

        $plan = PartnerPaymentPlan::updateOrCreate(
            ['partner_id' => $partner->id, 'pack_id' => $data['pack_id']],
            ['nom' => $data['nom'], 'date_fin_formation' => $data['date_fin_formation'], 'active' => true]
        );

        $payments->createPlan($plan, $data['installments']);
        return back()->with('success', 'Échéancier en 5 tranches enregistré avec succès.');
    }

    public function deletePlan(PartnerPaymentPlan $plan)
    {
        $partner = request()->user('partner_web');
        if ((int) $plan->partner_id !== (int) $partner->id) {
            abort(403);
        }

        $plan->delete();
        return back()->with('success', 'Échéancier supprimé.');
    }

    public function schedules()
    {
        $partner = request()->user('partner_web');
        $schedules = $partner->schedules()->with('pack', 'lesson')->latest()->paginate(20);
        $packs = Pack::active()->orderBy('nom')->get();
        $lessons = Lesson::active()->with('module.pack')->orderBy('titre')->get();

        return view('partner.schedules', compact('partner', 'schedules', 'packs', 'lessons'));
    }

    public function saveSchedule(Request $request)
    {
        $data = $request->validate([
            'pack_id' => 'required|exists:packs,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $partner = $request->user('partner_web');
        $partner->packs()->syncWithoutDetaching([$data['pack_id'] => ['active' => true]]);

        if (!empty($data['lesson_id'])) {
            $lesson = Lesson::with('module')->findOrFail($data['lesson_id']);
            $lessonPackId = $lesson->module?->pack_id;
            abort_unless((int) $lessonPackId === (int) $data['pack_id'], 422, 'La leçon ne correspond pas à la formation sélectionnée.');
        }

        CourseSchedule::create($data + ['partner_id' => $partner->id, 'active' => true]);
        return back()->with('success', 'Créneau horaire de cours enregistré avec succès.');
    }

    public function deleteSchedule(CourseSchedule $schedule)
    {
        $partner = request()->user('partner_web');
        if ((int) $schedule->partner_id !== (int) $partner->id) {
            abort(403);
        }

        $schedule->delete();
        return back()->with('success', 'Créneau horaire supprimé.');
    }

    public function centres()
    {
        $partner = request()->user('partner_web');
        $centres = Partner::where('active', true)->orderBy('nom')->get(['id', 'nom', 'code_partenaire', 'email', 'telephone', 'created_at']);

        return view('partner.centres', compact('partner', 'centres'));
    }
}