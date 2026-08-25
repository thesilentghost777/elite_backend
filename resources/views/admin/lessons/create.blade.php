@extends('admin.layouts.app')

@section('title', 'Créer une Leçon (Architecture 3 Parties)')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.packs.show', $module->pack) }}">{{ $module->pack->nom }}</a>
    <span>/</span>
    <a href="{{ route('admin.modules.show', $module) }}">{{ $module->nom }}</a>
    <span>/</span>
    <span>Nouvelle Leçon</span>
@endsection

@section('content')
<style>
    .lesson-form-container {
        max-width: 960px;
        margin: 0 auto;
    }

    .chapter-banner {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        border-radius: 16px;
        padding: 1.75rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 4px 14px rgba(30, 64, 175, 0.2);
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .chapter-icon-badge {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        background-color: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .course-parts-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .part-card {
        border-radius: 14px;
        border: 2px solid #e2e8f0;
        background: #ffffff;
        padding: 1.5rem;
        transition: all 0.2s ease;
        position: relative;
    }

    .part-card.part-1 {
        border-left: 6px solid #3b82f6;
    }

    .part-card.part-2 {
        border-left: 6px solid #8b5cf6;
    }

    .part-card.part-3 {
        border-left: 6px solid #10b981;
    }

    .part-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .part-title-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .part-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .part-badge.blue {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .part-badge.purple {
        background-color: #ede9fe;
        color: #6d28d9;
    }

    .part-badge.green {
        background-color: #d1fae5;
        color: #047857;
    }

    .info-callout {
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        color: #1e40af;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
    }
</style>

<div class="lesson-form-container">
    <!-- Module Context Banner -->
    <div class="chapter-banner">
        <div class="chapter-icon-badge">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div style="flex: 1;">
            <div style="font-size: 0.875rem; opacity: 0.85; margin-bottom: 0.25rem;">
                Pack : <strong>{{ $module->pack->nom }}</strong>
            </div>
            <h2 style="font-size: 1.35rem; font-weight: 700; margin: 0;">Module : {{ $module->nom }}</h2>
            <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 0.25rem;">
                {{ $module->lessons->count() }} leçon(s) déjà enregistrée(s)
            </div>
        </div>
        <a href="{{ route('admin.modules.show', $module) }}" class="btn btn-secondary" style="background: rgba(255,255,255,0.2); border: none; color: white;">
            <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i> Retour
        </a>
    </div>

    <!-- Pédagogie 3 Parties Explication -->
    <div class="info-callout">
        <i class="fas fa-info-circle" style="font-size: 1.25rem; margin-top: 0.1rem; color: #2563eb;"></i>
        <div>
            <strong>Architecture Pédagogique E-Learning en 3 Parties :</strong>
            <div style="margin-top: 0.35rem; line-height: 1.5; color: #1e3a8a;">
                Chaque leçon est structurée en 3 volets complémentaires pour maximiser l'assimilation : 
                <strong>1. Théorie & Support écrit</strong>, 
                <strong>2. Vidéo d'explication conceptuelle</strong>, et 
                <strong>3. Vidéo de pratique & mise en situation</strong>.
            </div>
        </div>
    </div>

    <form action="{{ route('admin.lessons.store', $module) }}" method="POST">
        @csrf

        <div class="form-card" style="margin-bottom: 2rem;">
            <!-- Paramètres Généraux -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    1. Paramètres de la Leçon
                </h3>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Titre de la leçon <span class="required">*</span></label>
                        <input type="text" 
                               name="titre" 
                               class="form-control @error('titre') error @enderror" 
                               value="{{ old('titre') }}" 
                               required 
                               placeholder="Ex: Fondamentaux de la gestion des opérations">
                        @error('titre')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Durée estimée (minutes) <span class="required">*</span></label>
                        <input type="number" 
                               name="duree_minutes" 
                               class="form-control @error('duree_minutes') error @enderror" 
                               value="{{ old('duree_minutes', 15) }}" 
                               min="1" 
                               required>
                        <div class="form-help">Temps moyen d'étude pour les 3 parties</div>
                        @error('duree_minutes')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ordre d'affichage <span class="required">*</span></label>
                        <input type="number" 
                               name="ordre" 
                               class="form-control @error('ordre') error @enderror" 
                               value="{{ old('ordre', $module->lessons->count() + 1) }}" 
                               min="0" 
                               required>
                        <div class="form-help">Position de déroulement dans le module</div>
                        @error('ordre')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Architecture E-Learning en 3 Parties -->
            <div class="form-section" style="border-top: 1px solid #e2e8f0; padding-top: 2rem;">
                <h3 class="form-section-title">
                    <div class="section-icon" style="background-color: #ede9fe; color: #7c3aed;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    2. Contenu E-Learning en 3 Parties
                </h3>

                <div class="course-parts-grid">
                    <!-- PARTIE 1 : Théorie & Support Écrit -->
                    <div class="part-card part-1">
                        <div class="part-header">
                            <div class="part-title-wrapper">
                                <span class="part-badge blue">Partie 1</span>
                                <h4 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">
                                    <i class="fas fa-book-open" style="color: #3b82f6; margin-right: 0.35rem;"></i>
                                    Théorie & Support Écrit
                                </h4>
                            </div>
                            <span style="font-size: 0.8rem; color: #64748b;">Texte Markdown & Ressource Web</span>
                        </div>

                        <div class="form-group" style="margin-bottom: 1.25rem;">
                            <label class="form-label">Contenu Théorique / Cours Écrit</label>
                            <textarea name="contenu_texte" 
                                      rows="8" 
                                      class="form-control @error('contenu_texte') error @enderror" 
                                      placeholder="Rédigez le cours théorique ici (supporte le Markdown : titres #, listes -, mise en gras **texte**)...">{{ old('contenu_texte') }}</textarea>
                            <div class="form-help">Ce texte constitue la base théorique lue par l'étudiant dans l'application mobile et web.</div>
                            @error('contenu_texte')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">Lien Ressource Web / Documentation externe (URL Web)</label>
                            <div style="position: relative;">
                                <input type="url" 
                                       name="url_web" 
                                       class="form-control @error('url_web') error @enderror" 
                                       value="{{ old('url_web') }}" 
                                       placeholder="https://docs.google.com/... ou https://exemple.com/support.html">
                            </div>
                            <div class="form-help">Permet d'ouvrir une WebView interactive intégrée avec documentation ou simulateur.</div>
                            @error('url_web')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- PARTIE 2 : Vidéo d'Explication Conceptuelle -->
                    <div class="part-card part-2">
                        <div class="part-header">
                            <div class="part-title-wrapper">
                                <span class="part-badge purple">Partie 2</span>
                                <h4 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">
                                    <i class="fas fa-play-circle" style="color: #8b5cf6; margin-right: 0.35rem;"></i>
                                    Vidéo d'Explication (Fondements & Concepts)
                                </h4>
                            </div>
                            <span style="font-size: 0.8rem; color: #64748b;">Cours magistral vidéo</span>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">URL de la Vidéo d'Explication</label>
                            <input type="url" 
                                   name="url_video_explication" 
                                   class="form-control @error('url_video_explication') error @enderror" 
                                   value="{{ old('url_video_explication') }}" 
                                   placeholder="https://www.youtube.com/watch?v=... ou lien MP4 / Vimeo">
                            <div class="form-help">Vidéo pédagogique expliquant les concepts fondamentaux de la leçon.</div>
                            @error('url_video_explication')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- PARTIE 3 : Vidéo Pratique & Atelier Démonstration -->
                    <div class="part-card part-3">
                        <div class="part-header">
                            <div class="part-title-wrapper">
                                <span class="part-badge green">Partie 3</span>
                                <h4 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">
                                    <i class="fas fa-laptop-code" style="color: #10b981; margin-right: 0.35rem;"></i>
                                    Vidéo de Pratique & Cas Concret
                                </h4>
                            </div>
                            <span style="font-size: 0.8rem; color: #64748b;">Démonstration métier</span>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label">URL de la Vidéo Pratique</label>
                            <input type="url" 
                                   name="url_video_pratique" 
                                   class="form-control @error('url_video_pratique') error @enderror" 
                                   value="{{ old('url_video_pratique') }}" 
                                   placeholder="https://www.youtube.com/watch?v=... ou lien MP4 / Vimeo">
                            <div class="form-help">Démonstration concrète, atelier pratique, manipulation d'outils et cas réel.</div>
                            @error('url_video_pratique')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statut & Activation -->
            <div class="form-section" style="border-top: 1px solid #e2e8f0; padding-top: 2rem;">
                <h3 class="form-section-title">
                    <div class="section-icon" style="background-color: #d1fae5; color: #059669;">
                        <i class="fas fa-toggle-on"></i>
                    </div>
                    3. Visibilité
                </h3>

                <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background-color: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <input type="checkbox" name="active" id="active" value="1" {{ old('active', true) ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: #2563eb;">
                    <label for="active" style="cursor: pointer; margin: 0;">
                        <div style="font-weight: 600; color: #1e293b;">Leçon active et accessible</div>
                        <div style="font-size: 0.85rem; color: #64748b;">Les apprenants inscrits pourront visionner cette leçon.</div>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions" style="margin-top: 2rem;">
                <a href="{{ route('admin.modules.show', $module) }}" class="btn btn-secondary">
                    <i class="fas fa-times" style="margin-right: 0.5rem;"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                    <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Enregistrer la Leçon (3 Parties)
                </button>
            </div>
        </div>
    </form>
</div>
@endsection