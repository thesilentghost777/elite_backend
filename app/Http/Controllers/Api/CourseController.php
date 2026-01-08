<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function __construct(private CourseService $service) {}

    public function categories(): JsonResponse { 
        return response()->json(['success' => true, 'data' => $this->service->getCategories()]); 
    }
    
    public function packs(Request $request): JsonResponse { 
        return response()->json(['success' => true, 'data' => $this->service->getAllPacks($request->only(['category_id', 'niveau_requis']))]); 
    }
    
    public function recommendedPacks(Request $request): JsonResponse { 
        return response()->json(['success' => true, 'data' => $this->service->getRecommendedPacks($request->user())]); 
    }
    
    public function packDetails(string $id): JsonResponse 
    { 
        return response()->json([
            'success' => true, 
            'data' => $this->service->getPackDetails((int)$id)
        ]); 
    }
    
    // CORRECTION: Ne plus valider ni passer la durée
    public function purchasePack(Request $request, int $id): JsonResponse 
    { 
        Log::info("Pursahace controller called");
        return response()->json([
            'success' => true, 
            'data' => $this->service->purchasePack($request->user(), $id)
        ]); 
    }
    
    public function packModules(Request $request, int $id): JsonResponse { 
        return response()->json(['success' => true, 'data' => $this->service->getPackModules($request->user(), $id)]); 
    }
    
    public function moduleChapters(Request $request, int $id): JsonResponse { 
        return response()->json(['success' => true, 'data' => $this->service->getModuleChapters($request->user(), $id)]); 
    }
    
    public function chapterLessons(Request $request, int $id): JsonResponse { 
        return response()->json(['success' => true, 'data' => $this->service->getChapterLessons($request->user(), $id)]); 
    }
    
    public function lesson(Request $request, int $id): JsonResponse { 
        return response()->json(['success' => true, 'data' => $this->service->getLesson($request->user(), $id)]); 
    }
    
    public function completeLesson(Request $request, int $id): JsonResponse { 
        $this->service->markLessonComplete($request->user(), $id, $request->temps_passe ?? 0); 
        return response()->json(['success' => true]); 
    }
    
    public function chapterQuiz(Request $request, int $id): JsonResponse { 
        return response()->json(['success' => true, 'data' => $this->service->getChapterQuiz($request->user(), $id)]); 
    }
    
    public function submitQuiz(Request $request, int $id): JsonResponse { 
        $request->validate(['responses' => 'required|array']); 
        return response()->json(['success' => true, 'data' => $this->service->submitQuiz($request->user(), $id, $request->responses)]); 
    }
    
    public function unlockByReferral(Request $request, int $id): JsonResponse { 
        return response()->json(['success' => true, 'data' => $this->service->unlockChapterByReferral($request->user(), $id)]); 
    }

    public function getChapterQuizInfo(Request $request, int $chapterId): JsonResponse 
{ 
    $user = $request->user();
    
    // Vérifier que le chapitre existe et a un quiz
    $chapter = Chapter::with('quizzes')->findOrFail($chapterId);
    
    if (!$chapter->quizzes || $chapter->quizzes->isEmpty()) {
        return response()->json([
            'success' => false, 
            'message' => 'Aucun quiz pour ce chapitre'
        ], 404);
    }
    
    $quiz = $chapter->quizzes->first();
    
    // Récupérer les résultats de l'utilisateur pour ce quiz
    $results = QuizResult::where('user_id', $user->id)
        ->where('quiz_id', $quiz->id)
        ->get();
    
    if ($results->isEmpty()) {
        return response()->json([
            'success' => false, 
            'message' => 'Aucune tentative'
        ], 404);
    }
    
    // Trouver le meilleur score
    $bestResult = $results->sortByDesc('note')->first();
    $attempts = $results->count();
    
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $quiz->id,
            'titre' => $quiz->titre,
            'best_score' => round(($bestResult->note / $quiz->note_totale) * 20, 2),
            'passed' => $bestResult->reussi,
            'attempts' => $attempts,
        ]
    ]);
}
}