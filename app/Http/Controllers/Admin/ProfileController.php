<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerProfile;
use App\Models\Roadmap;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = CareerProfile::withCount(['roadmaps', 'userChoices', 'packs'])->orderBy('nom')->paginate(20);
        return view('admin.profiles.index', compact('profiles'));
    }

    public function create()
    {
        return view('admin.profiles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'secteur' => 'required|string',
            'debouches' => 'nullable|array',
            'niveau_minimum' => 'required|string',
            'is_cfpam' => 'boolean',
            'active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['nom']);
        $validated['is_cfpam'] = $request->boolean('is_cfpam');
        $validated['active'] = $request->boolean('active');

        CareerProfile::create($validated);

        return redirect()->route('admin.profiles.index')->with('success', 'Profil créé');
    }

    public function show(CareerProfile $profile)
    {
        $profile->load(['roadmaps.steps', 'packs', 'userChoices.user']);
        return view('admin.profiles.show', compact('profile'));
    }

    public function edit(CareerProfile $profile)
    {
        return view('admin.profiles.edit', compact('profile'));
    }

    public function update(Request $request, CareerProfile $profile)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'secteur' => 'required|string',
            'debouches' => 'nullable|array',
            'niveau_minimum' => 'required|string',
            'is_cfpam' => 'boolean',
            'active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['nom']);
        $validated['is_cfpam'] = $request->boolean('is_cfpam');
        $validated['active'] = $request->boolean('active');

        $profile->update($validated);

        return redirect()->route('admin.profiles.show', $profile)->with('success', 'Profil mis à jour');
    }

    public function destroy(CareerProfile $profile)
    {
        $profile->delete();
        return redirect()->route('admin.profiles.index')->with('success', 'Profil supprimé');
    }
}
