<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pack;
use App\Models\Category;
use App\Models\CareerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackController extends Controller
{
    public function index()
    {
        $packs = Pack::with('category')->withCount(['modules', 'userPacks'])->orderBy('ordre')->paginate(20);
        return view('admin.packs.index', compact('packs'));
    }

    public function create()
    {
        $categories = Category::orderBy('ordre')->get();
        $profiles = CareerProfile::active()->orderBy('nom')->get();
        return view('admin.packs.create', compact('categories', 'profiles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'niveau_requis' => 'required|string',
            'durees_disponibles' => 'required|array',
            'diplomes_possibles' => 'required|array',
            'prix_points' => 'required|integer|min:1',
            'debouches' => 'nullable|array',
            'profiles' => 'nullable|array',
            'active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['nom']);
        $validated['active'] = $request->boolean('active');

        $pack = Pack::create($validated);

        if ($request->filled('profiles')) {
            $pack->profiles()->sync($request->profiles);
        }

        return redirect()->route('admin.packs.index')->with('success', 'Pack créé avec succès');
    }

    public function show(Pack $pack)
{
    // ✅ Changer 'quiz' en 'quizzes' (pluriel)
    $pack->load([
        'modules.chapters.lessons', 
        'modules.chapters.quizzes.questions', // ← quizzes au lieu de quiz
        'profiles', 
        'userPacks.user'
    ]);
    return view('admin.packs.show', compact('pack'));
}

    public function edit(Pack $pack)
    {
        $categories = Category::orderBy('ordre')->get();
        $profiles = CareerProfile::active()->orderBy('nom')->get();
        $pack->load('profiles');
        return view('admin.packs.edit', compact('pack', 'categories', 'profiles'));
    }

    public function update(Request $request, Pack $pack)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'niveau_requis' => 'required|string',
            'durees_disponibles' => 'required|array',
            'diplomes_possibles' => 'required|array',
            'prix_points' => 'required|integer|min:1',
            'debouches' => 'nullable|array',
            'profiles' => 'nullable|array',
            'active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['nom']);
        $validated['active'] = $request->boolean('active');

        $pack->update($validated);

        if ($request->filled('profiles')) {
            $pack->profiles()->sync($request->profiles);
        } else {
            $pack->profiles()->detach();
        }

        return redirect()->route('admin.packs.show', $pack)->with('success', 'Pack mis à jour');
    }

    public function destroy(Pack $pack)
    {
        $pack->delete();
        return redirect()->route('admin.packs.index')->with('success', 'Pack supprimé');
    }

    public function toggleActive(Pack $pack)
    {
        $pack->update(['active' => !$pack->active]);
        return back()->with('success', 'Statut du pack modifié');
    }
}
