<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\Module;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function create(Request $request, $target = null)
    {
        $module = null;
        if ($target instanceof Module) {
            $module = $target;
        } elseif ($target instanceof Chapter) {
            $module = $target->module;
        } else {
            $moduleId = $request->route('module') ?? $request->route('chapter') ?? $target;
            $module = Module::find($moduleId);
            if (!$module) {
                $chapter = Chapter::find($moduleId);
                $module = $chapter?->module;
            }
        }

        if (!$module) {
            abort(404, 'Module non trouvé');
        }

        return view('admin.quizzes.create', compact('module'));
    }

    public function store(Request $request, $target = null)
    {
        $module = null;
        if ($target instanceof Module) {
            $module = $target;
        } elseif ($target instanceof Chapter) {
            $module = $target->module;
        } else {
            $moduleId = $request->route('module') ?? $request->route('chapter') ?? $target;
            $module = Module::find($moduleId);
            if (!$module) {
                $chapter = Chapter::find($moduleId);
                $module = $chapter?->module;
            }
        }

        if (!$module) {
            abort(404, 'Module non trouvé');
        }

        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duree_minutes' => 'required|integer|min:5',
            'active' => 'boolean',
        ]);

        $validated['active'] = $request->boolean('active');
        $validated['module_id'] = $module->id;

        $quiz = $module->quizzes()->create($validated);

        return redirect()->route('admin.quizzes.show', $quiz)->with('success', 'Quiz créé. Veuillez maintenant configurer les 10 questions requises via l\'assistant.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load(['module.pack', 'questions.answers']);
        return view('admin.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $quiz->load('module.pack');
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

        $qCount = $quiz->questions()->count();
        if ($validated['active'] && $qCount < 10) {
            return redirect()->route('admin.quizzes.show', $quiz)
                ->with('warning', "Quiz mis à jour. Attention : Il contient actuellement {$qCount}/10 questions. L'accès étudiant reste bloqué jusqu'à ce que les 10 questions soient créées.");
        }

        return redirect()->route('admin.quizzes.show', $quiz)->with('success', 'Quiz mis à jour avec succès');
    }

    public function destroy(Quiz $quiz)
    {
        $pack = $quiz->module?->pack;
        $quiz->delete();
        return $pack
            ? redirect()->route('admin.packs.show', $pack)->with('success', 'Quiz supprimé avec succès.')
            : redirect()->back()->with('success', 'Quiz supprimé avec succès.');
    }

    public function addQuestion(Request $request, Quiz $quiz)
    {
        $currentCount = $quiz->questions()->count();
        if ($currentCount >= 10) {
            return back()->withErrors(['enonce' => 'Ce quiz contient déjà le maximum de 10 questions (mécanique Qui veut gagner des millions).']);
        }

        $validated = $request->validate([
            'enonce' => 'required|string',
            'type' => 'required|in:qcm,vrai_faux',
            'explication' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'ordre' => 'required|integer|min:1|max:10',
            'answers' => 'required|array|min:2',
            'answers.*.texte' => 'required|string',
            'answers.*.est_correcte' => 'nullable',
        ]);

        $hasCorrect = false;
        foreach ($validated['answers'] as $ans) {
            if (!empty($ans['est_correcte'])) {
                $hasCorrect = true;
                break;
            }
        }

        if (!$hasCorrect) {
            return back()->withErrors(['answers' => 'Veuillez désigner au moins une bonne réponse pour cette question.'])->withInput();
        }

        DB::transaction(function () use ($quiz, $validated) {
            $question = $quiz->questions()->create([
                'enonce' => $validated['enonce'],
                'type' => $validated['type'],
                'explication' => $validated['explication'] ?? null,
                'points' => $validated['points'],
                'ordre' => $validated['ordre'],
                'active' => true,
            ]);

            foreach ($validated['answers'] as $index => $answerData) {
                if (empty(trim($answerData['texte'] ?? ''))) {
                    continue;
                }
                $question->answers()->create([
                    'texte' => trim($answerData['texte']),
                    'est_correcte' => !empty($answerData['est_correcte']),
                    'ordre' => $index + 1,
                ]);
            }
        });

        $newCount = $quiz->questions()->count();
        if ($newCount === 10) {
            return redirect()->route('admin.quizzes.show', $quiz)
                ->with('success', '🎉 Félicitations ! Les 10 questions sont complètes. Le quiz est maintenant prêt et débloqué pour les étudiants.');
        }

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', "Question {$validated['ordre']} ajoutée avec succès ({$newCount}/10 questions configurées).");
    }

    public function updateQuestion(Request $request, QuizQuestion $question)
    {
        $validated = $request->validate([
            'enonce' => 'required|string',
            'type' => 'required|in:qcm,vrai_faux',
            'explication' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'ordre' => 'required|integer|min:1|max:10',
            'answers' => 'required|array|min:2',
            'answers.*.texte' => 'required|string',
            'answers.*.est_correcte' => 'nullable',
        ]);

        $hasCorrect = false;
        foreach ($validated['answers'] as $ans) {
            if (!empty($ans['est_correcte'])) {
                $hasCorrect = true;
                break;
            }
        }

        if (!$hasCorrect) {
            return back()->withErrors(['answers' => 'Veuillez désigner au moins une bonne réponse pour cette question.'])->withInput();
        }

        DB::transaction(function () use ($question, $validated) {
            $question->update([
                'enonce' => $validated['enonce'],
                'type' => $validated['type'],
                'explication' => $validated['explication'] ?? null,
                'points' => $validated['points'],
                'ordre' => $validated['ordre'],
            ]);

            $question->answers()->delete();

            foreach ($validated['answers'] as $index => $answerData) {
                if (empty(trim($answerData['texte'] ?? ''))) {
                    continue;
                }
                $question->answers()->create([
                    'texte' => trim($answerData['texte']),
                    'est_correcte' => !empty($answerData['est_correcte']),
                    'ordre' => $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.quizzes.show', $question->quiz)
            ->with('success', "Question {$validated['ordre']} mise à jour avec succès.");
    }

    public function deleteQuestion(QuizQuestion $question)
    {
        $quiz = $question->quiz;
        $question->delete();
        $count = $quiz->questions()->count();
        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', "Question supprimée. Il reste {$count}/10 questions dans ce quiz.");
    }
}
