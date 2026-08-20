@extends('admin.layouts.app')

@section('title', 'Modifier le quiz')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.chapters.show', $quiz->chapter) }}">Chapitre</a>
    <span>/</span>
    <span>Modifier {{ $quiz->titre }}</span>
@endsection

@section('content')
<style>
    .form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .duration-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .duration-btn {
        padding: 0.75rem;
        background-color: var(--white);
        border: 2px solid var(--gray-300);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        font-weight: 500;
        color: var(--gray-700);
    }

    .duration-btn:hover {
        border-color: var(--primary-blue);
        color: var(--primary-blue);
        background-color: var(--light-blue);
    }

    .duration-btn.active {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
    }

    .duration-btn input {
        display: none;
    }

    .switch-container {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background-color: var(--gray-50);
        border-radius: 8px;
        border: 1px solid var(--gray-200);
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--gray-300);
        transition: 0.3s;
        border-radius: 28px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: var(--success-green);
    }

    input:checked + .slider:before {
        transform: translateX(24px);
    }

    .switch-label {
        flex: 1;
    }

    .switch-label-title {
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
    }

    .switch-label-desc {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .danger-zone {
        border: 2px solid var(--danger);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .danger-zone-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .danger-zone-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--danger);
    }

    .danger-zone-description {
        color: var(--gray-700);
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .stats-row {
        display: flex;
        gap: 1rem;
        padding: 1rem;
        background-color: var(--light-blue);
        border: 1px solid var(--primary-blue);
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .stat-item {
        flex: 1;
        text-align: center;
    }

    .stat-item-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-blue);
        margin-bottom: 0.25rem;
    }

    .stat-item-label {
        font-size: 0.75rem;
        color: var(--secondary-blue);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>

<div class="form-container">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">Modifier le quiz</h1>
        <p class="page-description">{{ $quiz->titre }}</p>
    </div>

    <!-- Statistiques rapides -->
    <div class="stats-row">
        <div class="stat-item">
            <div class="stat-item-value">{{ $quiz->questions->count() }}</div>
            <div class="stat-item-label">Questions</div>
        </div>
        <div class="stat-item">
            <div class="stat-item-value">{{ $quiz->max_points ?? 0 }}</div>
            <div class="stat-item-label">Points total</div>
        </div>
        <div class="stat-item">
            <div class="stat-item-value">{{ $quiz->results->count() ?? 0 }}</div>
            <div class="stat-item-label">Tentatives</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}">
        @csrf
        @method('PUT')

        <div class="form-card">
            <!-- Informations générales -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    Informations du quiz
                </h3>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">
                            Titre du quiz <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="titre" 
                            class="form-control @error('titre') error @enderror" 
                            value="{{ old('titre', $quiz->titre) }}" 
                            required
                        >
                        @error('titre')
                            <div class="error-message">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">
                            Description (optionnel)
                        </label>
                        <textarea 
                            name="description" 
                            class="form-control @error('description') error @enderror" 
                            rows="3"
                        >{{ old('description', $quiz->description) }}</textarea>
                        @error('description')
                            <div class="error-message">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Configuration -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon" style="background-color: var(--light-green); color: var(--success-green);">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    Durée du quiz
                </h3>

                <div class="form-group">
                    <label class="form-label">
                        Temps alloué <span class="required">*</span>
                    </label>
                    
                    <div class="duration-selector">
                        <label class="duration-btn {{ old('duree_minutes', $quiz->duree_minutes) == '15' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="15" {{ old('duree_minutes', $quiz->duree_minutes) == '15' ? 'checked' : '' }} required>
                            15 min
                        </label>
                        <label class="duration-btn {{ old('duree_minutes', $quiz->duree_minutes) == '30' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="30" {{ old('duree_minutes', $quiz->duree_minutes) == '30' ? 'checked' : '' }} required>
                            30 min
                        </label>
                        <label class="duration-btn {{ old('duree_minutes', $quiz->duree_minutes) == '45' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="45" {{ old('duree_minutes', $quiz->duree_minutes) == '45' ? 'checked' : '' }} required>
                            45 min
                        </label>
                        <label class="duration-btn {{ old('duree_minutes', $quiz->duree_minutes) == '60' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="60" {{ old('duree_minutes', $quiz->duree_minutes) == '60' ? 'checked' : '' }} required>
                            60 min
                        </label>
                        <label class="duration-btn {{ old('duree_minutes', $quiz->duree_minutes) == '90' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="90" {{ old('duree_minutes', $quiz->duree_minutes) == '90' ? 'checked' : '' }} required>
                            90 min
                        </label>
                        <label class="duration-btn {{ old('duree_minutes', $quiz->duree_minutes) == '120' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="120" {{ old('duree_minutes', $quiz->duree_minutes) == '120' ? 'checked' : '' }} required>
                            120 min
                        </label>
                    </div>

                    @error('duree_minutes')
                        <div class="error-message">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Activation -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    Statut
                </h3>

                <div class="switch-container">
                    <label class="switch">
                        <input type="checkbox" name="active" value="1" {{ old('active', $quiz->active) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <div class="switch-label">
                        <div class="switch-label-title">Quiz actif</div>
                        <div class="switch-label-desc">Les quiz actifs sont accessibles aux étudiants</div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>

    <!-- Zone dangereuse -->
    <div class="form-card danger-zone">
        <div class="danger-zone-header">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="danger-zone-title">Zone dangereuse</h3>
        </div>
        
        <p class="danger-zone-description">
            La suppression de ce quiz est irréversible. Toutes les questions et les résultats des étudiants 
            ({{ $quiz->results->count() ?? 0 }} tentative(s)) seront définitivement perdus.
        </p>

        <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" 
              onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer ce quiz ? Cette action est IRRÉVERSIBLE et supprimera {{ $quiz->questions->count() }} question(s) et {{ $quiz->results->count() ?? 0 }} résultat(s).');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Supprimer définitivement ce quiz
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Gestion des boutons de durée
    document.querySelectorAll('.duration-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.duration-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>
@endpush
@endsection