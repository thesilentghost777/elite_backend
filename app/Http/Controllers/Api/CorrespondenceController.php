<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CorrespondenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CorrespondenceController extends Controller
{
    public function __construct(private CorrespondenceService $service) {}

    public function questions(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->service->getQuestions()]);
    }

    public function submit(Request $request): JsonResponse
    {
        $request->validate(['responses' => 'required|array', 'responses.*.question_id' => 'required|integer', 'responses.*.answer_id' => 'required|integer']);
        $results = $this->service->submitResponses($request->user(), $request->responses);
        return response()->json(['success' => true, 'data' => $results]);
    }

    public function results(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->service->getResults($request->user())]);
    }

    public function chooseProfile(Request $request): JsonResponse
    {
        $request->validate(['profile_id' => 'required|integer|exists:career_profiles,id']);
        $result = $this->service->chooseProfile($request->user(), $request->profile_id);
        return response()->json(['success' => true, 'data' => $result]);
    }

    public function choosePath(Request $request): JsonResponse
    {
        $request->validate(['mode' => 'required|in:en_ligne,presentiel,externe']);
        $this->service->choosePathMode($request->user(), $request->mode);
        return response()->json(['success' => true, 'message' => 'Mode de parcours enregistré']);
    }
}
