@extends('layouts.admin')

@section('title', 'Détails du Chapitre : ' . $chapter->nom)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.packs.index') }}">Packs</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.packs.show', $chapter->module->pack) }}">{{ $chapter->module->pack->nom }}</a></li>
            <li class="breadcrumb-item">{{ $chapter->module->nom }}</li>
            <li class="breadcrumb-item active">{{ $chapter->nom }}</li>
        </ol>
    </nav>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Chapter Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-chapter text-white">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-start">
                                <div class="chapter-icon me-3">
                                    <i class="fas fa-book-open fa-2x"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center mb-2">
                                        <h2 class="mb-0 fw-bold me-3">{{ $chapter->nom }}</h2>
                                        @if($chapter->active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Actif
                                        </span>
                                        @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Inactif
                                        </span>
                                        @endif
                                    </div>
                                    <p class="mb-2 opacity-90">{{ $chapter->description }}</p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <span>
                                            <i class="fas fa-sort-numeric-up me-2"></i>
                                            Ordre: {{ $chapter->ordre }}
                                        </span>
                                        <span>
                                            <i class="fas fa-graduation-cap me-2"></i>
                                            Note de passage: {{ $chapter->note_passage }}/20 (Palier 7/10)
                                        </span>
                                        <span>
                                            <i class="fas fa-layer-group me-2"></i>
                                            Pédagogie : Architecture 3 Parties & Quiz 10 Questions
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.chapters.edit', $chapter) }}" 
                                   class="btn btn-light">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                                <button type="button" 
                                        class="btn btn-outline-light" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-2"></i>Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Lessons Section -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 text-primary fw-bold">
                                <i class="fas fa-play-circle me-2"></i>Leçons (3 Parties E-Learning)
                            </h5>
                            <small class="text-muted">1. Théorie &bull; 2. Vidéo Explication &bull; 3. Vidéo Pratique</small>
                        </div>
                        <a href="{{ route('admin.lessons.create', $chapter) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Ajouter une leçon
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($chapter->lessons as $lesson)
                    <div class="lesson-item p-3 mb-3 rounded border">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-primary me-2">#{{ $lesson->ordre }}</span>
                                    <h6 class="mb-0 fw-bold">{{ $lesson->titre }}</h6>
                                    @if($lesson->active)
                                    <span class="badge bg-success-subtle text-success ms-2">
                                        <i class="fas fa-check"></i> Actif
                                    </span>
                                    @else
                                    <span class="badge bg-danger-subtle text-danger ms-2">
                                        Inactif
                                    </span>
                                    @endif
                                </div>

                                <div class="d-flex gap-2 flex-wrap mb-2">
                                    @if($lesson->contenu_texte || $lesson->url_web)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <i class="fas fa-book-open me-1"></i>Partie 1: Théorie
                                    </span>
                                    @endif

                                    @if($lesson->url_video_explication || $lesson->url_video)
                                    <span class="badge bg-purple-subtle text-purple border border-purple-subtle" style="background-color: #ede9fe; color: #6d28d9;">
                                        <i class="fas fa-video me-1"></i>Partie 2: Explication
                                    </span>
                                    @endif

                                    @if($lesson->url_video_pratique)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="fas fa-laptop-code me-1"></i>Partie 3: Pratique
                                    </span>
                                    @endif
                                </div>

                                <div class="d-flex gap-3 small text-muted">
                                    <span>
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $lesson->duree_minutes }} min
                                    </span>
                                    <span>
                                        <i class="fas fa-users me-1"></i>
                                        {{ $lesson->progress->count() }} apprenant(s)
                                    </span>
                                </div>
                            </div>

                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.lessons.edit', $lesson) }}" class="btn btn-outline-primary" title="Modifier la leçon">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Supprimer cette leçon ?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Supprimer la leçon">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune leçon enregistrée pour ce chapitre</p>
                        <a href="{{ route('admin.lessons.create', $chapter) }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer la première leçon (3 Parties)
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quiz Section -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 text-success fw-bold">
                                <i class="fas fa-clipboard-question me-2"></i>Quiz & Vrais Prix (1M FCFA)
                            </h5>
                            <small class="text-muted">Mécanique "Qui veut gagner des millions" (10 questions)</small>
                        </div>
                        @if(!$chapter->quiz)
                        <a href="{{ route('admin.quizzes.create', $chapter) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-1"></i>Créer Quiz
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $quiz = $chapter->quiz ?? $chapter->quizzes->first();
                    @endphp

                    @if($quiz)
                    @php
                        $qCount = $quiz->questions->count();
                        $isComplete = $qCount === 10;
                    @endphp
                    <div class="quiz-details">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">{{ $quiz->titre }}</h5>
                                <p class="text-muted mb-2 small">{{ $quiz->description ?? 'Évaluation de fin de chapitre.' }}</p>
                                
                                @if($isComplete)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Complet (10/10 questions) - Prêt
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Incomplet ({{ $qCount }}/10 questions) - Bloquant
                                    </span>
                                @endif
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-outline-primary" title="Modifier les paramètres">
                                    <i class="fas fa-cog"></i>
                                </a>
                                <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" onsubmit="return confirm('Supprimer ce quiz ?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Supprimer le quiz">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="stat-card bg-primary-subtle text-primary p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-list-ol fa-2x me-3"></i>
                                        <div>
                                            <div class="fs-4 fw-bold">{{ $qCount }}/10</div>
                                            <small>Questions configurées</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-warning-subtle text-warning p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-trophy fa-2x me-3 text-warning"></i>
                                        <div>
                                            <div class="fs-5 fw-bold text-dark">1 000 000 F</div>
                                            <small class="text-muted">Cagnotte Maximale</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assistant 10 Questions Alert & Link -->
                        <div class="p-3 mb-3 rounded border {{ $isComplete ? 'bg-light' : 'bg-warning-subtle border-warning' }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <strong class="d-block {{ $isComplete ? 'text-success' : 'text-dark' }}">
                                        <i class="fas {{ $isComplete ? 'fa-check-circle text-success' : 'fa-lock text-warning' }} me-1"></i>
                                        {{ $isComplete ? 'Quiz validé pour les étudiants' : 'Accès étudiant bloqué (10 questions requises)' }}
                                    </strong>
                                    <small class="text-muted">Palier 7/10 requis pour débloquer le chapitre suivant.</small>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-magic me-2"></i>Ouvrir l'Assistant 10 Questions ("Qui veut gagner des millions")
                        </a>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-question fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun quiz configuré pour ce chapitre</p>
                        <a href="{{ route('admin.quizzes.create', $chapter) }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Créer le Quiz (Format 10 Questions)
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques du Chapitre
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="stat-box p-4 bg-primary-subtle rounded">
                                <i class="fas fa-play-circle fa-2x text-primary mb-2"></i>
                                <h3 class="fw-bold text-primary mb-1">{{ $chapter->lessons->count() }}</h3>
                                <p class="text-muted mb-0 small">Leçons</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-box p-4 bg-success-subtle rounded">
                                <i class="fas fa-clipboard-question fa-2x text-success mb-2"></i>
                                <h3 class="fw-bold text-success mb-1">
                                    {{ $chapter->quiz ? $chapter->quiz->questions->count() : ($chapter->quizzes->first() ? $chapter->quizzes->first()->questions->count() : 0) }}/10
                                </h3>
                                <p class="text-muted mb-0 small">Questions Quiz</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-box p-4 bg-info-subtle rounded">
                                <i class="fas fa-clock fa-2x text-info mb-2"></i>
                                <h3 class="fw-bold text-info mb-1">
                                    {{ $chapter->lessons->sum('duree_minutes') }}
                                </h3>
                                <p class="text-muted mb-0 small">Minutes de contenu</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-box p-4 bg-warning-subtle rounded">
                                <i class="fas fa-graduation-cap fa-2x text-warning mb-2"></i>
                                <h3 class="fw-bold text-warning mb-1">{{ $chapter->note_passage }}/20</h3>
                                <p class="text-muted mb-0 small">Note de passage (Palier 7/10)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la Suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Êtes-vous sûr de vouloir supprimer ce chapitre ? Cette action est irréversible et supprimera également toutes les leçons et quiz associés.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-chapter {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    }
    
    .chapter-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .lesson-item,
    .question-item {
        transition: all 0.3s ease;
    }
    
    .lesson-item:hover,
    .question-item:hover {
        background-color: rgba(30, 64, 175, 0.04);
        border-color: #3b82f6 !important;
    }
    
    .stat-box {
        transition: transform 0.3s ease;
    }
    
    .stat-box:hover {
        transform: translateY(-5px);
    }
</style>
@endsection