<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Afficher la liste des catégories
     */
    public function index()
    {
        $categories = Category::orderBy('ordre')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Enregistrer une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url|max:255',
            'couleur' => 'nullable|string|max:7',
            'ordre' => 'nullable|integer|min:0',
            'active' => 'boolean',
        ]);

        // Générer le slug automatiquement si non fourni
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nom']);
        }

        // S'assurer que active est défini
        $validated['active'] = $request->has('active');

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Afficher les détails d'une catégorie
     */
    public function show(Category $category)
    {
        $category->load('packs');
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image_url' => 'nullable|url|max:255',
            'couleur' => 'nullable|string|max:7',
            'ordre' => 'nullable|integer|min:0',
            'active' => 'boolean',
        ]);

        // Générer le slug si modifié
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nom']);
        }

        $validated['active'] = $request->has('active');

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy(Category $category)
    {
        // Vérifier s'il y a des packs associés
        if ($category->packs()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Impossible de supprimer cette catégorie car elle contient des packs.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}