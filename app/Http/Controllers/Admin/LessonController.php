<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Chapter;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function create(Chapter $chapter)
    {
        return view('admin.lessons.create', compact('chapter'));
    }

    public function store(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'contenu_texte' => 'nullable|string',
            'url_video' => 'nullable|url',
            'url_externe' => 'nullable|url',
            'duree_minutes' => 'required|integer|min:1',
            'ordre' => 'required|integer|min:0',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $chapter->lessons()->create($validated);

        return redirect()->route('admin.chapters.show', $chapter)->with('success', 'Leçon créée');
    }

    public function edit(Lesson $lesson)
    {
        return view('admin.lessons.edit', compact('lesson'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'contenu_texte' => 'nullable|string',
            'url_video' => 'nullable|url',
            'url_externe' => 'nullable|url',
            'duree_minutes' => 'required|integer|min:1',
            'ordre' => 'required|integer|min:0',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $lesson->update($validated);

        return redirect()->route('admin.chapters.show', $lesson->chapter)->with('success', 'Leçon mise à jour');
    }

    public function destroy(Lesson $lesson)
    {
        $chapter = $lesson->chapter;
        $lesson->delete();
        return redirect()->route('admin.chapters.show', $chapter)->with('success', 'Leçon supprimée');
    }
}
