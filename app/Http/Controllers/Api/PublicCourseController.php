<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Pack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PublicCourseController extends Controller
{
    public function digital(): JsonResponse
    {
        $pack = Pack::where('slug', 'developpement-web-et-app')
            ->with('modules.chapters.lessons')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $pack->modules->map(fn ($module) => [
                'id' => $module->id,
                'nom' => $module->nom,
                'chapters' => $module->chapters->map(fn ($chapter) => [
                    'id' => $chapter->id,
                    'nom' => $chapter->nom,
                    'lessons' => $chapter->lessons->map(fn ($lesson) => $this->lessonLinks($lesson)),
                ]),
            ]),
        ]);
    }

    public function theory(Lesson $lesson): RedirectResponse
    {
        abort_unless($lesson->active && $lesson->url_web, 404);

        return redirect()->away($lesson->url_web);
    }

    public function video(Lesson $lesson, string $part = 'pratique'): RedirectResponse
    {
        $url = match ($part) {
            'explication' => $lesson->url_video_explication,
            'pratique' => $lesson->url_video_pratique ?: $lesson->url_video,
            default => null,
        };

        abort_unless($lesson->active && $url, 404);

        return redirect()->away($url);
    }

    private function lessonLinks(Lesson $lesson): array
    {
        return [
            'id' => $lesson->id,
            'titre' => $lesson->titre,
            'theorie_url' => url('/api/public/lessons/' . $lesson->id . '/theory'),
            'video_pratique_url' => url('/api/public/lessons/' . $lesson->id . '/video/pratique'),
            'video_explication_url' => url('/api/public/lessons/' . $lesson->id . '/video/explication'),
        ];
    }
}