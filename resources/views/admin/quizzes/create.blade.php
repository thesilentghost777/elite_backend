@extends('admin.layouts.app')

@section('title', 'Créer un Quiz de Fin de Module (Format 10 Questions)')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.packs.show', $module->pack) }}">{{ $module->pack->nom }}</a>
    <span>/</span>
    <a href="{{ route('admin.modules.show', $module) }}">{{ $module->nom }}</a>
    <span>/</span>
    <span>Nouveau Quiz</span>
@endsection

@section('content')
<style>
    .form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .chapter-info-banner {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        border-radius: 14px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .info-card-rules {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        color: #166534;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }
</style>

<div class="form-container">
    <!-- Info Module -->
    <div class="chapter-info-banner">
        <div style="width: 52px; height: 52px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            <i class="fas fa-trophy"></i>
        </div>
        <div style="flex: 1;">
            <div style="font-size: 0.85rem; opacity: 0.85;">Module d'évaluation</div>
            <h2 style="margin: 0; font-size: 1.35rem; font-weight: 700;">{{ $module->nom }}</h2>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 0.2rem;">
                Pack : {{ $module->pack->nom }} &bull; Note de passage : {{ $module->note_passage }}/20 (Palier 7/10)
            </div>
        </div>
        <a href="{{ route('admin.modules.show', $module) }}" class="btn btn-secondary" style="background: rgba(255,255,255,0.2); border: none; color: white;">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <!-- Règles "Qui veut gagner des millions" -->
    <div class="info-card-rules">
        <i class="fas fa-info-circle" style="font-size: 1.25rem; margin-top: 0.1rem; color: #16a34a;"></i>
        <div style="font-size: 0.875rem; line-height: 1.5;">
            <strong>Mécanique "Qui Veut Gagner des Millions" & Vrais Prix :</strong><br>
            Une fois ce quiz initialisé, vous serez dirigé vers <strong>l'Assistant 10 Questions</strong>. 
            Vous devrez configurer exactement 10 questions correspondant aux 10 paliers de la cagnotte (de 1 000 FCFA à 1 000 000 FCFA). 
            L'accès étudiant est conditionné par la réussite du <strong>palier 7/10</strong> (250 000 FCFA).
        </div>
    </div>

    <form method="POST" action="{{ route('admin.quizzes.store', $module) }}">
        @csrf

        <div class="form-card">
            <!-- Informations générales -->
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
                               value="{{ old('titre', 'Quiz d\'évaluation - ' . $module->nom) }}" 
                               required 
                               placeholder="Ex: Évaluation de synthèse - Module 1">
                        @error('titre')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Description ou consignes (Optionnel)</label>
                        <textarea name="description" 
                                  class="form-control @error('description') error @enderror" 
                                  rows="3" 
                                  placeholder="Consignes pour les candidats (10 questions, paliers de gains, 1 essai pour le jackpot)...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Durée allouée (Minutes) <span class="required">*</span></label>
                        <input type="number" 
                               name="duree_minutes" 
                               class="form-control @error('duree_minutes') error @enderror" 
                               value="{{ old('duree_minutes', 30) }}" 
                               min="5" 
                               required>
                        <div class="form-help">Temps total accordé pour répondre aux 10 questions</div>
                        @error('duree_minutes')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Statut initial</label>
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem;">
                            <input type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #2563eb;">
                            <label for="active" style="margin: 0; font-size: 0.9rem; color: #1e293b;">Quiz actif</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions" style="margin-top: 1.5rem;">
                <a href="{{ route('admin.modules.show', $module) }}" class="btn btn-secondary">
                    <i class="fas fa-times" style="margin-right: 0.4rem;"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                    <i class="fas fa-arrow-right" style="margin-right: 0.4rem;"></i> Créer et ouvrir l'Assistant 10 Questions
                </button>
            </div>
        </div>
    </form>
</div>
@endsection