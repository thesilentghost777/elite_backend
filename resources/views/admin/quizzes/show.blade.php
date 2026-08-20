@extends('admin.layouts.app')

@section('title', 'Détails du quiz')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.chapters.show', $quiz->chapter) }}">Chapitre</a>
    <span>/</span>
    <span>{{ $quiz->titre }}</span>
@endsection

@section('content')
<style>
    .quiz-hero {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        border-radius: 16px;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
    }

    .quiz-hero-content {
        display: flex;
        justify-content: space-between;
        align-items: start;
        gap: 2rem;
    }

    .quiz-hero-info h1 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.75rem;
    }

    .quiz-hero-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 1rem;
        font-size: 0.875rem;
        opacity: 0.95;
    }

    .quiz-hero-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quiz-hero-actions {
        display: flex;
        gap: 0.75rem;
        flex-shrink: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-box {
        background-color: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-blue);
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .stat-value.green {
        color: var(--success-green);
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .question-card {
        background-color: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 2px solid var(--gray-200);
        transition: all 0.2s;
    }

    .question-card:hover {
        border-color: var(--primary-blue);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .question-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        color: white;
        border-radius: 8px;
        font-weight: bold;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .question-content {
        flex: 1;
        margin: 0 1rem;
    }

    .question-text {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        line-height: 1.5;
    }

    .question-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .question-type {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        background-color: var(--light-blue);
        color: var(--primary-blue);
        border-radius: 6px;
        font-weight: 600;
    }

    .question-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        padding: 0.5rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        cursor: pointer;
        border: none;
    }

    .btn-edit {
        background-color: var(--light-green);
        color: var(--success-green);
    }

    .btn-edit:hover {
        background-color: var(--success-green);
        color: white;
    }

    .btn-delete {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .btn-delete:hover {
        background-color: var(--danger);
        color: white;
    }

    .answers-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1rem;
        padding-left: 2.5rem;
    }

    .answer-item {
        display: flex;
        align-items: start;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background-color: var(--gray-50);
        border-radius: 8px;
        border: 2px solid var(--gray-200);
    }

    .answer-item.correct {
        background-color: var(--light-green);
        border-color: var(--success-green);
    }

    .answer-icon {
        flex-shrink: 0;
        color: var(--gray-500);
    }

    .answer-item.correct .answer-icon {
        color: var(--success-green);
    }

    .answer-text {
        flex: 1;
        font-size: 0.875rem;
        color: var(--gray-700);
        line-height: 1.5;
    }

    .answer-item.correct .answer-text {
        color: var(--dark-green);
        font-weight: 500;
    }

    .empty-questions {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--gray-600);
    }

    .empty-questions svg {
        margin: 0 auto 1rem;
        opacity: 0.5;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
        padding: 2rem;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal {
        background-color: var(--white);
        border-radius: 12px;
        padding: 2rem;
        max-width: 700px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        margin-bottom: 1.5rem;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--gray-900);
    }

    .modal-footer {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--gray-200);
    }

    .answer-input-group {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .answer-input-item {
        display: flex;
        gap: 0.5rem;
        align-items: start;
    }

    .answer-checkbox {
        margin-top: 0.75rem;
        flex-shrink: 0;
    }

    .answer-input {
        flex: 1;
    }

    @media (max-width: 768px) {
        .quiz-hero-content {
            flex-direction: column;
        }

        .quiz-hero-actions {
            width: 100%;
        }

        .question-header {
            flex-direction: column;
            gap: 1rem;
        }

        .question-actions {
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="page-header" style="margin-bottom: 2rem;">
    <a href="{{ route('admin.chapters.show', $quiz->chapter) }}" class="btn btn-secondary">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Retour au chapitre
    </a>
</div>

<!-- Hero Section -->
<div class="quiz-hero">
    <div class="quiz-hero-content">
        <div class="quiz-hero-info">
            <h1>{{ $quiz->titre }}</h1>
            @if($quiz->description)
                <p style="opacity: 0.9; margin-top: 0.5rem;">{{ $quiz->description }}</p>
            @endif
            <div class="quiz-hero-meta">
                <div class="quiz-hero-meta-item">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ $quiz->duree_minutes }} minutes</span>
                </div>
                <div class="quiz-hero-meta-item">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>{{ $quiz->chapter->nom }}</span>
                </div>
                @if(!$quiz->active)
                <div class="quiz-hero-meta-item" style="color: #fca5a5;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Inactif</span>
                </div>
                @endif
            </div>
        </div>

        <div class="quiz-hero-actions">
            <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-success">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Modifier
            </a>
        </div>
    </div>
</div>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-value">{{ $quiz->questions->count() }}</div>
        <div class="stat-label">Questions</div>
    </div>
    <div class="stat-box">
        <div class="stat-value green">{{ $quiz->max_points ?? 0 }}</div>
        <div class="stat-label">Points total</div>
    </div>
    <div class="stat-box">
        <div class="stat-value">{{ $quiz->results->count() ?? 0 }}</div>
        <div class="stat-label">Tentatives</div>
    </div>
    <div class="stat-box">
        <div class="stat-value">{{ $quiz->duree_minutes }}</div>
        <div class="stat-label">Minutes</div>
    </div>
</div>

<!-- Liste des questions -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            Questions du quiz 
            <span style="font-weight: normal; color: var(--gray-600);">({{ $quiz->questions->count() }})</span>
        </h2>
        <button onclick="openModal('addQuestionModal')" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Ajouter une question
        </button>
    </div>

    @forelse($quiz->questions as $index => $question)
        <div class="question-card">
            <div class="question-header">
                <div class="question-number">{{ $index + 1 }}</div>
                
                <div class="question-content">
                    <div class="question-text">{{ $question->enonce }}</div>
                    <div class="question-meta">
                        <span class="question-type">
                            {{ $question->type == 'qcm' ? 'QCM' : 'Vrai/Faux' }}
                        </span>
                        <span>• {{ $question->points }} point(s)</span>
                        @if($question->explication)
                            <span>• Explication fournie</span>
                        @endif
                    </div>
                </div>

                <div class="question-actions">
                    <form method="POST" action="{{ route('admin.quizzes.delete-question', $question) }}" 
                          onsubmit="return confirm('Supprimer cette question ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon btn-delete">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Réponses -->
            <div class="answers-list">
                @foreach($question->answers as $answer)
                    <div class="answer-item {{ $answer->est_correcte ? 'correct' : '' }}">
                        <svg class="answer-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($answer->est_correcte)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            @endif
                        </svg>
                        <span class="answer-text">{{ $answer->texte }}</span>
                    </div>
                @endforeach
            </div>

            @if($question->explication)
                <div style="margin-top: 1rem; padding: 1rem; background-color: var(--light-blue); border-radius: 8px; margin-left: 2.5rem;">
                    <div style="font-weight: 600; color: var(--primary-blue); margin-bottom: 0.25rem; font-size: 0.875rem;">
                        💡 Explication
                    </div>
                    <div style="font-size: 0.875rem; color: var(--secondary-blue);">
                        {{ $question->explication }}
                    </div>
                </div>
            @endif
        </div>
    @empty
        <div class="empty-questions">
            <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 style="margin-bottom: 0.5rem;">Aucune question</h3>
            <p>Commencez par ajouter des questions à ce quiz</p>
            <button onclick="openModal('addQuestionModal')" class="btn btn-primary" style="margin-top: 1rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Ajouter la première question
            </button>
        </div>
    @endforelse
</div>

<!-- Modal Ajouter question -->
<div id="addQuestionModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Ajouter une question</h3>
        </div>
        
        <form method="POST" action="{{ route('admin.quizzes.add-question', $quiz) }}">
            @csrf
            
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Énoncé de la question <span class="required">*</span></label>
                <textarea name="enonce" class="form-control" rows="3" required placeholder="Posez votre question ici..."></textarea>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Type de question <span class="required">*</span></label>
                <select name="type" class="form-control" required onchange="updateAnswerFields(this.value)">
                    <option value="qcm">QCM (Choix multiples)</option>
                    <option value="vrai_faux">Vrai/Faux</option>
                </select>
            </div>

            <div class="form-grid" style="margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Points <span class="required">*</span></label>
                    <input type="number" name="points" class="form-control" value="1" min="1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Ordre <span class="required">*</span></label>
                    <input type="number" name="ordre" class="form-control" value="{{ $quiz->questions->count() + 1 }}" min="0" required>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Réponses <span class="required">*</span></label>
                <div id="answersContainer" class="answer-input-group">
                    <div class="answer-input-item">
                        <input type="checkbox" name="answers[0][est_correcte]" value="1" class="answer-checkbox">
                        <input type="text" name="answers[0][texte]" class="form-control answer-input" placeholder="Réponse 1" required>
                    </div>
                    <div class="answer-input-item">
                        <input type="checkbox" name="answers[1][est_correcte]" value="1" class="answer-checkbox">
                        <input type="text" name="answers[1][texte]" class="form-control answer-input" placeholder="Réponse 2" required>
                    </div>
                </div>
                <button type="button" class="add-btn" onclick="addAnswerField()" id="addAnswerBtn" style="margin-top: 0.75rem;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Ajouter une réponse
                </button>
            </div>

            <div class="form-group">
                <label class="form-label">Explication (optionnel)</label>
                <textarea name="explication" class="form-control" rows="2" placeholder="Explication de la bonne réponse..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addQuestionModal')">Annuler</button>
                <button type="submit" class="btn btn-primary">Ajouter la question</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let answerCount = 2;

    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }
    
    // Fermer le modal en cliquant en dehors
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });

    function updateAnswerFields(type) {
        const container = document.getElementById('answersContainer');
        const addBtn = document.getElementById('addAnswerBtn');
        
        if (type === 'vrai_faux') {
            container.innerHTML = `
                <div class="answer-input-item">
                    <input type="checkbox" name="answers[0][est_correcte]" value="1" class="answer-checkbox">
                    <input type="text" name="answers[0][texte]" class="form-control answer-input" value="Vrai" required readonly>
                </div>
                <div class="answer-input-item">
                    <input type="checkbox" name="answers[1][est_correcte]" value="1" class="answer-checkbox">
                    <input type="text" name="answers[1][texte]" class="form-control answer-input" value="Faux" required readonly>
                </div>
            `;
            addBtn.style.display = 'none';
            answerCount = 2;
        } else {
            container.innerHTML = `
                <div class="answer-input-item">
                    <input type="checkbox" name="answers[0][est_correcte]" value="1" class="answer-checkbox">
                    <input type="text" name="answers[0][texte]" class="form-control answer-input" placeholder="Réponse 1" required>
                </div>
                <div class="answer-input-item">
                    <input type="checkbox" name="answers[1][est_correcte]" value="1" class="answer-checkbox">
                    <input type="text" name="answers[1][texte]" class="form-control answer-input" placeholder="Réponse 2" required>
                </div>
            `;
            addBtn.style.display = 'inline-flex';
            answerCount = 2;
        }
    }

    function addAnswerField() {
        const container = document.getElementById('answersContainer');
        const newAnswer = document.createElement('div');
        newAnswer.className = 'answer-input-item';
        newAnswer.innerHTML = `
            <input type="checkbox" name="answers[${answerCount}][est_correcte]" value="1" class="answer-checkbox">
            <input type="text" name="answers[${answerCount}][texte]" class="form-control answer-input" placeholder="Réponse ${answerCount + 1}" required>
        `;
        container.appendChild(newAnswer);
        answerCount++;
    }
</script>
@endpush
@endsection