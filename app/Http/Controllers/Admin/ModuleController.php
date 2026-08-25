<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Pack;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function create(Pack $pack)
    {
        return view('admin.modules.create', compact('pack'));
    }

    public function store(Request $request, Pack $pack)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:theorique,pratique',
            'ordre' => 'required|integer|min:0',
            'note_passage' => 'nullable|integer|min:1|max:20',
            'note_parrainage' => 'nullable|integer|min:1|max:20',
            'parrainages_requis' => 'nullable|integer|min:1',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $validated['note_passage'] = $validated['note_passage'] ?? 14;
        $validated['note_parrainage'] = $validated['note_parrainage'] ?? 10;
        $validated['parrainages_requis'] = $validated['parrainages_requis'] ?? 4;

        $pack->modules()->create($validated);

        return redirect()->route('admin.packs.show', $pack)->with('success', 'Module créé avec succès.');
    }

    public function show(Module $module)
    {
        $module->load(['pack', 'lessons', 'quizzes.questions.answers']);
        return view('admin.modules.show', compact('module'));
    }

    public function edit(Module $module)
    {
        return view('admin.modules.edit', compact('module'));
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:theorique,pratique',
            'ordre' => 'required|integer|min:0',
            'note_passage' => 'nullable|integer|min:1|max:20',
            'note_parrainage' => 'nullable|integer|min:1|max:20',
            'parrainages_requis' => 'nullable|integer|min:1',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $module->update($validated);

        return redirect()->route('admin.packs.show', $module->pack)->with('success', 'Module mis à jour avec succès.');
    }

    public function destroy(Module $module)
    {
        $pack = $module->pack;
        $module->delete();
        return redirect()->route('admin.packs.show', $pack)->with('success', 'Module supprimé avec succès.');
    }
}
