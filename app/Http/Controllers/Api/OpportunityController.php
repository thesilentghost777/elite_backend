<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bibliotheque;
use App\Models\Concours;
use App\Models\Financement;
use App\Models\JobOffer;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $points = (int) $request->user()->solde_points;
        $decorate = fn ($item) => $this->access($item, $points);
        return response()->json(['success' => true, 'data' => [
            'points' => $points,
            'emplois' => JobOffer::active()->notExpired()->latest()->get()->map(fn ($item) => $this->access($item, $points, (int) SystemSetting::get('points_emploi', $item->points_requis ?? 0))),
            'concours' => Concours::active()->latest()->get()->map(fn ($item) => $this->access($item, $points, (int) SystemSetting::get('points_concours', $item->points_requis ?? 0))),
            'financements' => Financement::active()->latest()->get()->map(fn ($item) => $this->access($item, $points, (int) SystemSetting::get('points_financement', $item->points_requis ?? 0))),
            'bibliotheque' => Bibliotheque::active()->latest()->get()->map(fn ($item) => $this->access($item, $points, (int) SystemSetting::get('points_bibliotheque', $item->points_requis ?? 0))),
        ]]);
    }

    private function access($item, int $points, ?int $requiredOverride = null): array
    {
        $required = $requiredOverride ?? (int) ($item->points_requis ?? 0);
        $data = $item->toArray();
        $data['accessible'] = $points >= $required;
        $data['points_manquants'] = max(0, $required - $points);
        if (!$data['accessible']) {
            unset($data['description'], $data['conditions_eligibilite'], $data['fichier_pdf']);
        }
        return $data;
    }
}