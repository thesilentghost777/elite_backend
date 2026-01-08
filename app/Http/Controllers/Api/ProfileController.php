<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $service) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['secteur', 'is_cfpam', 'niveau_minimum']);
        return response()->json(['success' => true, 'data' => $this->service->getAllProfiles($filters)]);
    }

    public function secteurs(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->service->getSecteurs()]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->service->getProfile($id)]);
    }

    public function roadmap(Request $request, int $id): JsonResponse
    {
        $niveau = $request->niveau ?? $request->user()->dernier_diplome;
        return response()->json(['success' => true, 'data' => $this->service->getRoadmap($id, $niveau)]);
    }

    public function myRoadmap(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->service->getUserRoadmap($request->user())]);
    }
}
