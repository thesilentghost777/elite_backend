@extends('admin.layouts.app')

@section('title', 'Créer un quiz')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.chapters.show', $chapter) }}">Chapitre</a>
    <span>/</span>
    <span>Créer un quiz</span>
@endsection

@section('content')
<style>
    .form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .chapter-info {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .chapter-icon {
        width: 56px;
        height: 56px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .chapter-details h2 {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .chapter-meta {
        font-size: 0.875rem;
        opacity: 0.9;
        display: flex;
        gap: 1rem;
    }

    .info-card {
        background-color: var(--light-blue);
        border: 1px solid var(--primary-blue);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-card-title {
        font-weight: 600;
        color: var(--secondary-blue);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-card-text {
        font-size: 0.875rem;
        color: var(--secondary-blue);
        line-height: 1.6;
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

    .preview-box {
        background-color: var(--gray-50);
        border: 2px dashed var(--gray-300);
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
    }

    .preview-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .preview-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }

    .preview-details {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 1rem;
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .preview-detail {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<div class="form-container">
    <!-- Info chapitre -->
    <div class="chapter-info">
        <div class="chapter-icon">
            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
        <div class="chapter-details">
            <h2>{{ $chapter->nom }}</h2>
            <div class="chapter-meta">
                <span>Module: {{ $chapter->module->nom }}</span>
                <span>•</span>
                <span>Pack: {{ $chapter->module->pack->nom }}</span>
            </div>
        </div>
    </div>

    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">Créer un quiz d'évaluation</h1>
        <p class="page-description">Définir les paramètres du quiz pour ce chapitre</p>
    </div>

    <!-- Information -->
    <div class="info-card">
        <div class="info-card-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            À propos des quiz
        </div>
        <div class="info-card-text">
            Le quiz sera créé avec les paramètres de base. Vous pourrez ensuite ajouter des questions 
            depuis la page de détails du quiz. Les étudiants devront réussir ce quiz pour débloquer 
            le chapitre suivant.
        </div>
    </div>

    <form method="POST" action="{{ route('admin.quizzes.store', $chapter) }}">
        @csrf

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
                            value="{{ old('titre', 'Quiz - ' . $chapter->nom) }}" 
                            required
                            placeholder="Ex: Quiz - Introduction au secrétariat"
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
                            placeholder="Décrivez brièvement les objectifs et le contenu de ce quiz..."
                        >{{ old('description') }}</textarea>
                        <div class="form-help">Consignes ou informations utiles pour les étudiants</div>
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
                        <label class="duration-btn {{ old('duree_minutes') == '15' || !old('duree_minutes') ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="15" {{ old('duree_minutes') == '15' || !old('duree_minutes') ? 'checked' : '' }} required>
                            15 min
                        </label>
                        <label class="duration-btn {{ old('duree_minutes') == '30' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="30" {{ old('duree_minutes') == '30' ? 'checked' : '' }} required>
                            30 min
                        </label>
                        <label class="duration-btn {{ old('duree_minutes') == '45' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="45" {{ old('duree_minutes') == '45' ? 'checked' : '' }} required>
                            45 min
                        </label>
                        <label class="duration-btn {{ old('duree_minutes') == '60' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="60" {{ old('duree_minutes') == '60' ? 'checked' : '' }} required>
                            60 min
                        </label>
                        <label class="duration-btn {{ old('duree_minutes') == '90' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="90" {{ old('duree_minutes') == '90' ? 'checked' : '' }} required>
                            90 min
                        </label>
                        <label class="duration-btn {{ old('duree_minutes') == '120' ? 'active' : '' }}">
                            <input type="radio" name="duree_minutes" value="120" {{ old('duree_minutes') == '120' ? 'checked' : '' }} required>
                            120 min
                        </label>
                    </div>

                    <div class="form-help" style="margin-top: 0.75rem;">
                        Le chronomètre démarrera dès que l'étudiant commencera le quiz
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
                        <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                    <div class="switch-label">
                        <div class="switch-label-title">Quiz actif</div>
                        <div class="switch-label-desc">Les quiz actifs sont accessibles aux étudiants. Vous pouvez désactiver le quiz pour le modifier sans le rendre visible.</div>
                    </div>
                </div>
            </div>

            <!-- Prévisualisation -->
            <div class="form-section">
                <div class="preview-box">
                    <div class="preview-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="preview-title">Quiz prêt à être créé</div>
                    <p style="color: var(--gray-600); font-size: 0.875rem; margin: 0.5rem 0;">
                        Vous pourrez ajouter des questions après la création
                    </p>
                    <div class="preview-details">
                        <div class="preview-detail">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="previewDuration">15 minutes</span>
                        </div>
                        <div class="preview-detail">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>0 questions pour le moment</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.chapters.show', $chapter) }}" class="btn btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Créer le quiz
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Gestion des boutons de durée
    document.querySelectorAll('.duration-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.duration-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Mettre à jour la prévisualisation
            const duration = this.querySelector('input').value;
            document.getElementById('previewDuration').textContent = duration + ' minutes';
        });
    });
</script>
@endpush
@endsection