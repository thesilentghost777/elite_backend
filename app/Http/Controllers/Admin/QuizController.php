<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Chapter;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function create(Chapter $chapter)
    {
        return view('admin.quizzes.create', compact('chapter'));
    }

    public function store(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree_minutes' => 'required|integer|min:5',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $chapter->quiz()->create($validated);

        return redirect()->route('admin.chapters.show', $chapter)->with('success', 'Quiz créé');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions.answers');
        return view('admin.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree_minutes' => 'required|integer|min:5',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $quiz->update($validated);

        return redirect()->route('admin.quizzes.show', $quiz)->with('success', 'Quiz mis à jour');
    }

    public function destroy(Quiz $quiz)
    {
        $chapter = $quiz->chapter;
        $quiz->delete();
        return redirect()->route('admin.chapters.show', $chapter)->with('success', 'Quiz supprimé');
    }

    public function addQuestion(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'enonce' => 'required|string',
            'type' => 'required|in:qcm,vrai_faux',
            'explication' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'ordre' => 'required|integer|min:0',
            'answers' => 'required|array|min:2',
            'answers.*.texte' => 'required|string',
            'answers.*.est_correcte' => 'boolean',
        ]);

        $question = $quiz->questions()->create([
            'enonce' => $validated['enonce'],
            'type' => $validated['type'],
            'explication' => $validated['explication'],
            'points' => $validated['points'],
            'ordre' => $validated['ordre'],
        ]);

        foreach ($validated['answers'] as $index => $answerData) {
            $question->answers()->create([
                'texte' => $answerData['texte'],
                'est_correcte' => $answerData['est_correcte'] ?? false,
                'ordre' => $index,
            ]);
        }

        return redirect()->route('admin.quizzes.show', $quiz)->with('success', 'Question ajoutée');
    }

    public function deleteQuestion(QuizQuestion $question)
    {
        $quiz = $question->quiz;
        $question->delete();
        return redirect()->route('admin.quizzes.show', $quiz)->with('success', 'Question supprimée');
    }
}
