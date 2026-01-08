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
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $pack->modules()->create($validated);

        return redirect()->route('admin.packs.show', $pack)->with('success', 'Module créé');
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
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $module->update($validated);

        return redirect()->route('admin.packs.show', $module->pack)->with('success', 'Module mis à jour');
    }

    public function destroy(Module $module)
    {
        $pack = $module->pack;
        $module->delete();
        return redirect()->route('admin.packs.show', $pack)->with('success', 'Module supprimé');
    }
}
