@extends('admin.layouts.app')

@section('title', 'Modifier les paramètres du Quiz')

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
    <a href="{{ route('admin.quizzes.show', $quiz) }}">{{ $quiz->titre }}</a>
    <span>/</span>
    <span>Paramètres</span>
@endsection

@section('content')
<style>
    .form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .quiz-info-banner {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        border-radius: 14px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .danger-zone {
        border: 2px solid #ef4444;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
        background: #fef2f2;
    }
</style>

<div class="form-container">
    <!-- Info quiz -->
    <div class="quiz-info-banner">
        <div style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-cog"></i>
        </div>
        <div style="flex: 1;">
            <div style="font-size: 0.85rem; opacity: 0.85;">Configuration générale du quiz</div>
            <h2 style="margin: 0; font-size: 1.35rem; font-weight: 700;">{{ $quiz->titre }}</h2>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 0.2rem;">
                {{ $quiz->questions->count() }}/10 questions configurées &bull; Module : {{ $currentModule?->nom }}
            </div>
        </div>
        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-secondary" style="background: rgba(255,255,255,0.2); border: none; color: white;">
            <i class="fas fa-list-ol"></i> Assistant 10 Questions
        </a>
    </div>

    <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}">
        @csrf
        @method('PUT')

        <div class="form-card">
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    Informations du Quiz
                </h3>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Titre du quiz <span class="required">*</span></label>
                        <input type="text" 
                               name="titre" 
                               class="form-control @error('titre') error @enderror" 
                               value="{{ old('titre', $quiz->titre) }}" 
                               required>
                        @error('titre')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Description ou consignes</label>
                        <textarea name="description" 
                                  class="form-control @error('description') error @enderror" 
                                  rows="3">{{ old('description', $quiz->description) }}</textarea>
                        @error('description')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Durée allouée (Minutes) <span class="required">*</span></label>
                        <input type="number" 
                               name="duree_minutes" 
                               class="form-control @error('duree_minutes') error @enderror" 
                               value="{{ old('duree_minutes', $quiz->duree_minutes) }}" 
                               min="5" 
                               required>
                        @error('duree_minutes')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Statut</label>
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem;">
                            <input type="checkbox" name="active" id="active" value="1" {{ old('active', $quiz->active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #2563eb;">
                            <label for="active" style="margin: 0; font-size: 0.9rem; color: #1e293b;">
                                Quiz actif (Nécessite 10 questions pour débloquer l'accès apprenant)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions" style="margin-top: 1.5rem;">
                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-secondary">
                    <i class="fas fa-times" style="margin-right: 0.4rem;"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                    <i class="fas fa-save" style="margin-right: 0.4rem;"></i> Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>

    <!-- Zone Dangereuse -->
    <div class="danger-zone">
        <h4 style="color: #b91c1c; font-weight: 700; margin: 0 0 0.5rem 0;">
            <i class="fas fa-exclamation-triangle" style="margin-right: 0.4rem;"></i> Zone de Suppression
        </h4>
        <p style="color: #7f1d1d; font-size: 0.875rem; margin-bottom: 1rem;">
            La suppression de ce quiz supprimera irréversiblement ses {{ $quiz->questions->count() }} questions ainsi que tous les historiques de résultats d'étudiants.
        </p>
        <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Supprimer définitivement ce quiz ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash" style="margin-right: 0.4rem;"></i> Supprimer définitivement le Quiz
            </button>
        </form>
    </div>
</div>
@endsection