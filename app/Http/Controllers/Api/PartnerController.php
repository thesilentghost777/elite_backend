<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseSchedule;
use App\Models\Pack;
use App\Models\PartnerPaymentPlan;
use App\Services\PartnerPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function __construct(private PartnerPaymentService $payments) {}

    public function dashboard(Request $request): JsonResponse
    {
        $partner = $request->user();
        return response()->json(['success' => true, 'data' => [
            'partner' => $partner,
            'apprenants' => $partner->learners()->with('userPacks.pack')->latest()->get(),
            'formations' => $partner->packs()->wherePivot('active', true)->get(),
        ]]);
    }

    public function centres(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => \App\Models\Partner::where('active', true)->select('id', 'nom', 'telephone')->orderBy('nom')->get()]);
    }

    public function plans(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $request->user()->paymentPlans()->with('installments', 'pack')->get()]);
    }

    public function savePlan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pack_id' => 'required|exists:packs,id', 'nom' => 'required|string|max:255',
            'date_fin_formation' => 'nullable|date', 'installments' => 'required|array|size:5',
            'installments.*.libelle' => 'required|string|max:255',
            'installments.*.montant_fcfa' => 'required|numeric|min:0',
            'installments.*.delai_jours' => 'required|integer|min:0',
        ]);
        $request->user()->packs()->syncWithoutDetaching([$data['pack_id'] => ['active' => true]]);
        $plan = PartnerPaymentPlan::updateOrCreate(
            ['partner_id' => $request->user()->id, 'pack_id' => $data['pack_id']],
            ['nom' => $data['nom'], 'date_fin_formation' => $data['date_fin_formation'] ?? null, 'active' => true]
        );
        $this->payments->createPlan($plan, $data['installments']);
        return response()->json(['success' => true, 'data' => $plan->load('installments')]);
    }

    public function schedules(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $request->user()->schedules()->with('pack', 'lesson')->orderBy('starts_at')->get()]);
    }

    public function saveSchedule(Request $request): JsonResponse
    {
        $data = $request->validate([
            'pack_id' => 'required|exists:packs,id', 'lesson_id' => 'nullable|exists:lessons,id',
            'starts_at' => 'required|date', 'ends_at' => 'nullable|date|after:starts_at',
        ]);
        $request->user()->packs()->syncWithoutDetaching([$data['pack_id'] => ['active' => true]]);
        $schedule = CourseSchedule::create($data + ['partner_id' => $request->user()->id, 'active' => true]);
        return response()->json(['success' => true, 'data' => $schedule], 201);
    }
}