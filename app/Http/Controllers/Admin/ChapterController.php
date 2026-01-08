<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Module;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function create(Module $module)
    {
        return view('admin.chapters.create', compact('module'));
    }

    public function store(Request $request, Module $module)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordre' => 'required|integer|min:0',
            'note_passage' => 'required|integer|min:10|max:20',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $module->chapters()->create($validated);

        return redirect()->route('admin.packs.show', $module->pack)->with('success', 'Chapitre créé');
    }

    public function show(Chapter $chapter)
    {
        $chapter->load(['lessons', 'quiz.questions.answers']);
        return view('admin.chapters.show', compact('chapter'));
    }

    public function edit(Chapter $chapter)
    {
        return view('admin.chapters.edit', compact('chapter'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordre' => 'required|integer|min:0',
            'note_passage' => 'required|integer|min:10|max:20',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $chapter->update($validated);

        return redirect()->route('admin.chapters.show', $chapter)->with('success', 'Chapitre mis à jour');
    }

    public function destroy(Chapter $chapter)
    {
        $pack = $chapter->module->pack;
        $chapter->delete();
        return redirect()->route('admin.packs.show', $pack)->with('success', 'Chapitre supprimé');
    }
}
