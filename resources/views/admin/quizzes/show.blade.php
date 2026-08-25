@extends('admin.layouts.app')

@section('title', 'Assistant Quiz : ' . $quiz->titre)

@php
    $currentModule = $quiz->module ?? ($quiz->chapter?->module ?? null);
    $pack = $currentModule?->pack;
@endphp

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    @if($pack)
        <a href="{{ route('admin.packs.show', $pack) }}">{{ $pack->nom }}</a>
        <span>/</span>
    @endif
    @if($currentModule)
        <a href="{{ route('admin.modules.show', $currentModule) }}">{{ $currentModule->nom }}</a>
        <span>/</span>
    @endif
    <span>Quiz : {{ $quiz->titre }}</span>
@endsection

@section('content')
@php
    $questionsCount = $quiz->questions->count();
    $isComplete = $questionsCount === 10;
    $missingCount = max(0, 10 - $questionsCount);
    $progressPercent = min(100, ($questionsCount / 10) * 100);

    $paliers = [
        1 => ['fcfa' => '1 000 FCFA', 'label' => 'Palier 1', 'badge' => 'gray'],
        2 => ['fcfa' => '5 000 FCFA', 'label' => 'Palier 2', 'badge' => 'gray'],
        3 => ['fcfa' => '10 000 FCFA', 'label' => 'Palier 3', 'badge' => 'gray'],
        4 => ['fcfa' => '25 000 FCFA', 'label' => 'Palier 4', 'badge' => 'gray'],
        5 => ['fcfa' => '50 000 FCFA', 'label' => 'Palier 5', 'badge' => 'gray'],
        6 => ['fcfa' => '100 000 FCFA', 'label' => 'Palier 6', 'badge' => 'gray'],
        7 => ['fcfa' => '250 000 FCFA', 'label' => '⭐️ Palier 7/10 (Déblocage Module suivant)', 'badge' => 'warning', 'is_threshold' => true],
        8 => ['fcfa' => '500 000 FCFA', 'label' => 'Palier 8', 'badge' => 'purple'],
        9 => ['fcfa' => '750 000 FCFA', 'label' => 'Palier 9', 'badge' => 'purple'],
        10 => ['fcfa' => '1 000 000 FCFA', 'label' => '🏆 Palier 10/10 (Cagnotte Maximale)', 'badge' => 'gold', 'is_jackpot' => true],
    ];

    $questionsByOrder = $quiz->questions->keyBy('ordre');
@endphp

<style>
    .quiz-hero-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(30, 58, 138, 0.25);
    }

    .hero-flex {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
        flex-wrap: wrap;
    }

    .assistant-status-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 2px solid {{ $isComplete ? '#10b981' : '#f59e0b' }};
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .progress-bar-bg {
        background: #e2e8f0;
        border-radius: 999px;
        height: 14px;
        overflow: hidden;
        margin: 1rem 0;
    }

    .progress-bar-fill {
        background: {{ $isComplete ? 'linear-gradient(90deg, #10b981, #059669)' : 'linear-gradient(90deg, #f59e0b, #d97706)' }};
        height: 100%;
        width: {{ $progressPercent }}%;
        transition: width 0.4s ease;
    }

    .ladder-grid {
        display: flex;
        flex-direction: column-reverse;
        gap: 0.85rem;
        margin-top: 1.5rem;
    }

    .ladder-row {
        background: #ffffff;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
        gap: 1rem;
    }

    .ladder-row.configured {
        border-color: #cbd5e1;
        background: #ffffff;
    }

    .ladder-row.threshold {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .ladder-row.jackpot {
        border-color: #8b5cf6;
        background: #f5f3ff;
    }

    .ladder-row.missing {
        border-style: dashed;
        border-color: #cbd5e1;
        background: #f8fafc;
    }

    .ladder-step-badge {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: #1e40af;
        color: white;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .ladder-step-badge.threshold {
        background: #f59e0b;
    }

    .ladder-step-badge.jackpot {
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
    }

    .ladder-step-badge.missing-badge {
        background: #94a3b8;
    }

    .cagnotte-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 700;
        font-size: 0.9rem;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        background: #f1f5f9;
        color: #1e293b;
    }

    .cagnotte-pill.gold {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .cagnotte-pill.purple {
        background: #ede9fe;
        color: #5b21b6;
        border: 1px solid #ddd6fe;
    }

    .question-options-preview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .option-chip {
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .option-chip.correct {
        background: #d1fae5;
        border-color: #10b981;
        color: #065f46;
        font-weight: 600;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        overflow-y: auto;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        max-width: 720px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>

<!-- Hero Section -->
<div class="quiz-hero-banner">
    <div class="hero-flex">
        <div>
            <div style="font-size: 0.875rem; opacity: 0.85; margin-bottom: 0.35rem;">
                <i class="fas fa-layer-group" style="margin-right: 0.35rem;"></i>
                {{ $pack?->nom }} &bull; {{ $currentModule?->nom }}
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0 0 0.5rem 0;">{{ $quiz->titre }}</h1>
            @if($quiz->description)
                <p style="opacity: 0.9; margin: 0 0 1rem 0; font-size: 0.95rem;">{{ $quiz->description }}</p>
            @endif

            <div style="display: flex; gap: 1.25rem; flex-wrap: wrap; font-size: 0.875rem; opacity: 0.95;">
                <div><i class="fas fa-clock" style="margin-right: 0.35rem;"></i> {{ $quiz->duree_minutes }} min</div>
                <div><i class="fas fa-trophy" style="margin-right: 0.35rem;"></i> Cagnotte : 1 000 000 FCFA</div>
                <div><i class="fas fa-star" style="margin-right: 0.35rem;"></i> Palier de passage : 7/10 ({{ $currentModule?->note_passage ?? 14 }}/20)</div>
                <div>
                    @if($quiz->active)
                        <span class="badge" style="background: #10b981; color: white;">Actif</span>
                    @else
                        <span class="badge" style="background: #ef4444; color: white;">Inactif</span>
                    @endif
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            @if($currentModule)
                <a href="{{ route('admin.modules.show', $currentModule) }}" class="btn btn-secondary" style="background: rgba(255,255,255,0.15); border: none; color: white;">
                    <i class="fas fa-arrow-left" style="margin-right: 0.4rem;"></i> Module
                </a>
            @endif
            <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-secondary" style="background: rgba(255,255,255,0.25); border: none; color: white;">
                <i class="fas fa-cog" style="margin-right: 0.4rem;"></i> Paramètres
            </a>
        </div>
    </div>
</div>

<!-- Assistant 10 Questions Bloquant -->
<div class="assistant-status-card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.5rem;">
                    @if($isComplete) 🏆 @else 🎯 @endif
                </span>
                <div>
                    <h3 style="margin: 0; font-size: 1.2rem; font-weight: 700; color: #1e293b;">
                        Assistant 10 Questions — Mécanique "Qui Veut Gagner des Millions"
                    </h3>
                    <div style="font-size: 0.875rem; color: #64748b; margin-top: 0.15rem;">
                        Chaque quiz doit comporter rigoureusement <strong>10 questions</strong> (1 question par palier de cagnotte).
                    </div>
                </div>
            </div>
        </div>

        <div>
            @if($isComplete)
                <span class="badge" style="background: #d1fae5; color: #065f46; font-size: 0.9rem; padding: 0.5rem 1rem; border-radius: 999px;">
                    <i class="fas fa-check-circle" style="margin-right: 0.35rem;"></i> 10/10 Questions — Validé & Prêt
                </span>
            @else
                <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.9rem; padding: 0.5rem 1rem; border-radius: 999px;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 0.35rem;"></i> Incomplet ({{ $questionsCount }}/10) — Bloquant
                </span>
            @endif
        </div>
    </div>

    <!-- Progress bar -->
    <div class="progress-bar-bg">
        <div class="progress-bar-fill"></div>
    </div>

    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #64748b; font-weight: 600;">
        <span>Progression : {{ $questionsCount }} sur 10 questions</span>
        @if(!$isComplete)
            <span style="color: #b45309;">Il reste {{ $missingCount }} question(s) pour débloquer l'accès étudiant</span>
        @else
            <span style="color: #047857;">Le quiz respecte toutes les conditions de déblocage</span>
        @endif
    </div>

    @if(!$isComplete)
        <div style="margin-top: 1rem; padding: 0.85rem 1rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; font-size: 0.85rem; color: #92400e; display: flex; gap: 0.5rem; align-items: center;">
            <i class="fas fa-lock" style="font-size: 1.1rem; color: #d97706;"></i>
            <div>
                <strong>Règle bloquante active :</strong> L'API mobile et le web rejettent l'exécution du quiz si le nombre de questions est différent de 10. Utilisez les boutons ci-dessous pour rédiger les paliers manquants.
            </div>
        </div>
    @endif
</div>

<!-- Pyramide des 10 Paliers de Questions -->
<div class="form-card" style="margin-bottom: 2.5rem;">
    <div class="form-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <h3 class="form-section-title" style="margin: 0;">
                <div class="section-icon" style="background-color: #dbeafe; color: #1e40af;">
                    <i class="fas fa-list-ol"></i>
                </div>
                Grille des 10 Paliers & Questions
            </h3>

            @if(!$isComplete)
                <button type="button" class="btn btn-primary" onclick="openAddQuestionModal({{ $questionsCount + 1 }})">
                    <i class="fas fa-plus" style="margin-right: 0.4rem;"></i> Ajouter la Question #{{ $questionsCount + 1 }}
                </button>
            @endif
        </div>

        <div class="ladder-grid">
            @for($i = 10; $i >= 1; $i--)
                @php
                    $palierInfo = $paliers[$i];
                    $question = $questionsByOrder->get($i) ?? $quiz->questions->where('ordre', $i)->first();
                    $isThreshold = !empty($palierInfo['is_threshold']);
                    $isJackpot = !empty($palierInfo['is_jackpot']);
                @endphp

                <div class="ladder-row {{ $question ? 'configured' : 'missing' }} {{ $isJackpot ? 'jackpot' : ($isThreshold ? 'threshold' : '') }}">
                    <!-- Left: Step Number + Icon -->
                    <div style="display: flex; align-items: center; gap: 1rem; flex: 1;">
                        <div class="ladder-step-badge {{ $isJackpot ? 'jackpot' : ($isThreshold ? 'threshold' : ($question ? '' : 'missing-badge')) }}">
                            Q{{ $i }}
                        </div>

                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                                <span class="cagnotte-pill {{ $isJackpot ? 'gold' : ($isThreshold ? 'gold' : '') }}">
                                    <i class="fas fa-coins" style="color: #f59e0b;"></i> {{ $palierInfo['fcfa'] }}
                                </span>
                                <span style="font-size: 0.8rem; font-weight: 700; color: {{ $isJackpot ? '#7c3aed' : ($isThreshold ? '#b45309' : '#64748b') }};">
                                    {{ $palierInfo['label'] }}
                                </span>
                            </div>

                            @if($question)
                                <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem; line-height: 1.4;">
                                    {{ $question->enonce }}
                                </div>
                                <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                                    Type : <strong>{{ $question->type === 'qcm' ? 'QCM (Choix Multiples)' : 'Vrai / Faux' }}</strong> &bull; 
                                    Points : <strong>{{ $question->points }} pt(s)</strong>
                                    @if($question->explication)
                                        &bull; <span style="color: #2563eb;"><i class="fas fa-lightbulb"></i> Explication incluse</span>
                                    @endif
                                </div>

                                <!-- Réponses -->
                                <div class="question-options-preview">
                                    @foreach($question->answers as $ans)
                                        <div class="option-chip {{ $ans->est_correcte ? 'correct' : '' }}">
                                            @if($ans->est_correcte)
                                                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                            @else
                                                <i class="far fa-circle" style="color: #94a3b8;"></i>
                                            @endif
                                            <span>{{ $ans->texte }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="color: #94a3b8; font-style: italic; font-size: 0.9rem;">
                                    <i class="fas fa-exclamation-circle" style="margin-right: 0.3rem;"></i> 
                                    Question #{{ $i }} non configurée (Palier {{ $palierInfo['fcfa'] }})
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Actions -->
                    <div>
                        @if($question)
                            <div style="display: flex; gap: 0.5rem;">
                                <button type="button" 
                                        class="btn btn-secondary" 
                                        style="padding: 0.4rem 0.75rem; font-size: 0.85rem;"
                                        onclick="openEditQuestionModal({{ $question->toJson() }})">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>

                                <form action="{{ route('admin.questions.destroy', $question) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Supprimer la question #{{ $i }} ?');"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.75rem; font-size: 0.85rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <button type="button" 
                                    class="btn btn-primary" 
                                    style="padding: 0.5rem 1rem; font-size: 0.85rem;"
                                    onclick="openAddQuestionModal({{ $i }})">
                                <i class="fas fa-plus"></i> Rédiger Q{{ $i }}
                            </button>
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>

<!-- Modal Ajouter une Question -->
<div id="addQuestionModal" class="modal-overlay">
    <div class="modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.35rem; font-weight: 700; color: #1e293b;">
                <i class="fas fa-plus-circle" style="color: #2563eb; margin-right: 0.4rem;"></i>
                Rédiger la Question <span id="modalAddOrderLabel"></span>
            </h3>
            <button type="button" onclick="closeModal('addQuestionModal')" style="background: none; border: none; font-size: 1.25rem; color: #94a3b8; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.quizzes.add-question', $quiz) }}" method="POST">
            @csrf

            <input type="hidden" name="ordre" id="addQuestionOrdre" value="1">

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Énoncé de la question <span class="required">*</span></label>
                <textarea name="enonce" class="form-control" rows="3" required placeholder="Saisissez ici la question posée à l'apprenant..."></textarea>
            </div>

            <div class="form-grid" style="margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label">Type d'évaluation <span class="required">*</span></label>
                    <select name="type" id="addQuestionType" class="form-control" onchange="renderAddAnswers(this.value)">
                        <option value="qcm" selected>QCM (4 propositions)</option>
                        <option value="vrai_faux">Vrai / Faux (2 choix)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Points accordés <span class="required">*</span></label>
                    <input type="number" name="points" class="form-control" value="2" min="1" required>
                </div>
            </div>

            <!-- Réponses Dynamiques -->
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Propositions de réponses (Cochez la ou les bonnes réponses) <span class="required">*</span></label>
                <div id="addAnswersContainer" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Rendu par JS -->
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Explication pédagogique (Affichée après réponse)</label>
                <textarea name="explication" class="form-control" rows="2" placeholder="Pourquoi est-ce la bonne réponse ? Astuce ou référence du cours..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid #e2e8f0; padding-top: 1.25rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addQuestionModal')">Annuler</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;">
                    <i class="fas fa-save" style="margin-right: 0.4rem;"></i> Enregistrer la Question
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modifier une Question -->
<div id="editQuestionModal" class="modal-overlay">
    <div class="modal-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.35rem; font-weight: 700; color: #1e293b;">
                <i class="fas fa-edit" style="color: #2563eb; margin-right: 0.4rem;"></i>
                Modifier la Question <span id="modalEditOrderLabel"></span>
            </h3>
            <button type="button" onclick="closeModal('editQuestionModal')" style="background: none; border: none; font-size: 1.25rem; color: #94a3b8; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editQuestionForm" method="POST" action="">
            @csrf
            @method('PUT')

            <input type="hidden" name="ordre" id="editQuestionOrdre" value="1">

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Énoncé de la question <span class="required">*</span></label>
                <textarea name="enonce" id="editQuestionEnonce" class="form-control" rows="3" required></textarea>
            </div>

            <div class="form-grid" style="margin-bottom: 1.25rem;">
                <div class="form-group">
                    <label class="form-label">Type d'évaluation <span class="required">*</span></label>
                    <select name="type" id="editQuestionType" class="form-control" onchange="renderEditAnswers(this.value)">
                        <option value="qcm">QCM (4 propositions)</option>
                        <option value="vrai_faux">Vrai / Faux (2 choix)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Points accordés <span class="required">*</span></label>
                    <input type="number" name="points" id="editQuestionPoints" class="form-control" min="1" required>
                </div>
            </div>

            <!-- Réponses Modifiables -->
            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label class="form-label">Propositions de réponses <span class="required">*</span></label>
                <div id="editAnswersContainer" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <!-- Rendu par JS -->
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Explication pédagogique</label>
                <textarea name="explication" id="editQuestionExplication" class="form-control" rows="2"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid #e2e8f0; padding-top: 1.25rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editQuestionModal')">Annuler</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem;">
                    <i class="fas fa-save" style="margin-right: 0.4rem;"></i> Mettre à jour la Question
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    function openAddQuestionModal(order) {
        document.getElementById('addQuestionOrdre').value = order;
        document.getElementById('modalAddOrderLabel').textContent = '#' + order;
        renderAddAnswers(document.getElementById('addQuestionType').value);
        openModal('addQuestionModal');
    }

    function renderAddAnswers(type) {
        const container = document.getElementById('addAnswersContainer');
        if (type === 'vrai_faux') {
            container.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <input type="checkbox" name="answers[0][est_correcte]" value="1" style="width: 18px; height: 18px;" checked>
                    <input type="text" name="answers[0][texte]" value="Vrai" class="form-control" required readonly>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <input type="checkbox" name="answers[1][est_correcte]" value="1" style="width: 18px; height: 18px;">
                    <input type="text" name="answers[1][texte]" value="Faux" class="form-control" required readonly>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <input type="checkbox" name="answers[0][est_correcte]" value="1" style="width: 18px; height: 18px;" checked title="Bonne réponse">
                    <input type="text" name="answers[0][texte]" class="form-control" placeholder="Option A (Ex: Réponse correcte)" required>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <input type="checkbox" name="answers[1][est_correcte]" value="1" style="width: 18px; height: 18px;" title="Bonne réponse">
                    <input type="text" name="answers[1][texte]" class="form-control" placeholder="Option B" required>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <input type="checkbox" name="answers[2][est_correcte]" value="1" style="width: 18px; height: 18px;" title="Bonne réponse">
                    <input type="text" name="answers[2][texte]" class="form-control" placeholder="Option C" required>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <input type="checkbox" name="answers[3][est_correcte]" value="1" style="width: 18px; height: 18px;" title="Bonne réponse">
                    <input type="text" name="answers[3][texte]" class="form-control" placeholder="Option D" required>
                </div>
            `;
        }
    }

    let currentEditingQuestion = null;

    function openEditQuestionModal(question) {
        currentEditingQuestion = question;
        document.getElementById('editQuestionForm').action = `/admin/questions/${question.id}`;
        document.getElementById('modalEditOrderLabel').textContent = '#' + question.ordre;
        document.getElementById('editQuestionOrdre').value = question.ordre;
        document.getElementById('editQuestionEnonce').value = question.enonce;
        document.getElementById('editQuestionType').value = question.type;
        document.getElementById('editQuestionPoints').value = question.points;
        document.getElementById('editQuestionExplication').value = question.explication || '';

        renderEditAnswers(question.type, question.answers);
        openModal('editQuestionModal');
    }

    function renderEditAnswers(type, answers = null) {
        const container = document.getElementById('editAnswersContainer');
        const ansList = answers || (currentEditingQuestion ? currentEditingQuestion.answers : []);

        if (type === 'vrai_faux') {
            const isVraiCorrect = ansList.find(a => a.texte === 'Vrai' && a.est_correcte);
            container.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <input type="checkbox" name="answers[0][est_correcte]" value="1" style="width: 18px; height: 18px;" ${isVraiCorrect || !ansList.length ? 'checked' : ''}>
                    <input type="text" name="answers[0][texte]" value="Vrai" class="form-control" required readonly>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <input type="checkbox" name="answers[1][est_correcte]" value="1" style="width: 18px; height: 18px;" ${!isVraiCorrect && ansList.length ? 'checked' : ''}>
                    <input type="text" name="answers[1][texte]" value="Faux" class="form-control" required readonly>
                </div>
            `;
        } else {
            let html = '';
            for (let i = 0; i < 4; i++) {
                const a = ansList && ansList[i] ? ansList[i] : null;
                const txt = a ? a.texte : '';
                const checked = a && a.est_correcte ? 'checked' : (i === 0 && !ansList.length ? 'checked' : '');
                html += `
                    <div style="display: flex; align-items: center; gap: 0.75rem; background: #f8fafc; padding: 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <input type="checkbox" name="answers[${i}][est_correcte]" value="1" style="width: 18px; height: 18px;" ${checked}>
                        <input type="text" name="answers[${i}][texte]" value="${txt}" class="form-control" placeholder="Option ${String.fromCharCode(65 + i)}" required>
                    </div>
                `;
            }
            container.innerHTML = html;
        }
    }

    // Close modal on background click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
</script>
@endpush
@endsection