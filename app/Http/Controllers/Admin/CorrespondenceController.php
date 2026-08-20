<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorrespondenceCategory;
use App\Models\CorrespondenceQuestion;
use App\Models\CorrespondenceAnswer;
use App\Models\ProfileMatching;
use App\Models\CareerProfile;
use Illuminate\Http\Request;

class CorrespondenceController extends Controller
{
    public function index()
    {
        $categories = CorrespondenceCategory::with(['questions' => function ($q) {
            $q->withCount('answers')->orderBy('ordre');
        }])->orderBy('ordre')->get();

        return view('admin.correspondence.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.correspondence.create-category');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ordre' => 'required|integer|min:0',
        ]);

        CorrespondenceCategory::create($validated);

        return redirect()->route('admin.correspondence.index')->with('success', 'Catégorie créée');
    }

    public function createQuestion(CorrespondenceCategory $category)
    {
        $profiles = CareerProfile::active()->orderBy('nom')->get();
        return view('admin.correspondence.create-question', compact('category', 'profiles'));
    }

    public function storeQuestion(Request $request, CorrespondenceCategory $category)
    {
        $validated = $request->validate([
            'texte' => 'required|string',
            'type' => 'required|in:qcm,oui_non,echelle',
            'ordre' => 'required|integer|min:0',
            'obligatoire' => 'boolean',
            'answers' => 'required|array|min:2',
            'answers.*.texte' => 'required|string',
            'answers.*.matchings' => 'nullable|array',
        ]);

        $question = $category->questions()->create([
            'texte' => $validated['texte'],
            'type' => $validated['type'],
            'ordre' => $validated['ordre'],
            'obligatoire' => $request->boolean('obligatoire'),
        ]);

        foreach ($validated['answers'] as $index => $answerData) {
            $answer = $question->answers()->create([
                'texte' => $answerData['texte'],
                'ordre' => $index,
            ]);

            if (!empty($answerData['matchings'])) {
                foreach ($answerData['matchings'] as $profileId => $poids) {
                    if ($poids > 0) {
                        ProfileMatching::create([
                            'answer_id' => $answer->id,
                            'profile_id' => $profileId,
                            'poids' => $poids,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.correspondence.index')->with('success', 'Question créée');
    }

    public function editQuestion(CorrespondenceQuestion $question)
    {
        $question->load('answers.matchings');
        $profiles = CareerProfile::active()->orderBy('nom')->get();
        return view('admin.correspondence.edit-question', compact('question', 'profiles'));
    }

    public function updateQuestion(Request $request, CorrespondenceQuestion $question)
    {
        $validated = $request->validate([
            'texte' => 'required|string',
            'type' => 'required|in:qcm,oui_non,echelle',
            'ordre' => 'required|integer|min:0',
            'obligatoire' => 'boolean',
        ]);

        $validated['obligatoire'] = $request->boolean('obligatoire');
        $question->update($validated);

        return redirect()->route('admin.correspondence.index')->with('success', 'Question mise à jour');
    }

    public function deleteQuestion(CorrespondenceQuestion $question)
    {
        $question->delete();
        return redirect()->route('admin.correspondence.index')->with('success', 'Question supprimée');
    }
}
