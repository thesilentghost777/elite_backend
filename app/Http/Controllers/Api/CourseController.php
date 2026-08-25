<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function __construct(private CourseService $service) {}

    public function categories(): JsonResponse
    { 
        return response()->json(['success' => true, 'data' => $this->service->getCategories()]); 
    }
    
    public function packs(Request $request): JsonResponse
    { 
        return response()->json(['success' => true, 'data' => $this->service->getAllPacks($request->only(['category_id', 'niveau_requis']))]); 
    }
    
    public function recommendedPacks(Request $request): JsonResponse
    { 
        return response()->json(['success' => true, 'data' => $this->service->getRecommendedPacks($request->user())]); 
    }
    
    public function packDetails(string $id): JsonResponse 
    { 
        return response()->json([
            'success' => true, 
            'data' => $this->service->getPackDetails((int)$id)
        ]); 
    }
    
    public function packModules(Request $request, int $id): JsonResponse
    { 
        return response()->json(['success' => true, 'data' => $this->service->getPackModules($request->user(), $id)]); 
    }

    public function moduleLessons(Request $request, int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->service->getModuleLessons($request->user(), $id)]);
    }

    public function moduleQuiz(Request $request, int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->service->getModuleQuiz($request->user(), $id)]);
    }

    public function getModuleQuizInfo(Request $request, int $moduleId): JsonResponse
    {
        $user = $request->user();
        $module = Module::with('quizzes')->findOrFail($moduleId);
        $quiz = $module->quizzes->first();

        if (!$quiz) {
            return response()->json([
                'success' => false, 
                'message' => 'Aucun quiz pour ce module'
            ], 404);
        }

        $results = QuizResult::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->get();

        if ($results->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'Aucune tentative'
            ], 404);
        }

        $bestResult = $results->sortByDesc('note')->first();
        $attempts = $results->count();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $quiz->id,
                'titre' => $quiz->titre,
                'best_score' => $bestResult->note,
                'passed' => (bool) $bestResult->reussi,
                'attempts' => $attempts,
            ]
        ]);
    }

    public function unlockModuleByReferral(Request $request, int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->service->unlockModuleByReferral($request->user(), $id)]);
    }
    
    public function lesson(Request $request, int $id): JsonResponse
    { 
        return response()->json(['success' => true, 'data' => $this->service->getLesson($request->user(), $id)]); 
    }
    
    public function completeLesson(Request $request, int $id): JsonResponse
    { 
        $result = $this->service->markLessonComplete($request->user(), $id, $request->temps_passe ?? 0); 
        return response()->json(['success' => true, 'data' => $result]); 
    }
    
    public function submitQuiz(Request $request, int $id): JsonResponse
    { 
        $request->validate(['responses' => 'nullable|array']); 
        $responses = $request->input('responses', []);
        return response()->json(['success' => true, 'data' => $this->service->submitQuiz($request->user(), $id, $responses)]); 
    }

    // =========================================================================
    // ENDPOINTS DE COMPATIBILITÉ
    // =========================================================================
    
    public function moduleChapters(Request $request, int $id): JsonResponse
    { 
        return response()->json(['success' => true, 'data' => $this->service->getModuleChapters($request->user(), $id)]); 
    }
    
    public function chapterLessons(Request $request, int $id): JsonResponse
    { 
        return response()->json(['success' => true, 'data' => $this->service->getChapterLessons($request->user(), $id)]); 
    }
    
    public function chapterQuiz(Request $request, int $id): JsonResponse
    { 
        return response()->json(['success' => true, 'data' => $this->service->getChapterQuiz($request->user(), $id)]); 
    }
    
    public function unlockByReferral(Request $request, int $id): JsonResponse
    { 
        return response()->json(['success' => true, 'data' => $this->service->unlockChapterByReferral($request->user(), $id)]); 
    }

    public function getChapterQuizInfo(Request $request, int $chapterId): JsonResponse 
    { 
        return $this->getModuleQuizInfo($request, $chapterId);
    }
}