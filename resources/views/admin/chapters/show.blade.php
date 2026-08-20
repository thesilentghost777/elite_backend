@extends('layouts.admin')

@section('title', 'Détails du Chapitre')

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
                                    <div class="d-flex gap-3">
                                        <span>
                                            <i class="fas fa-sort-numeric-up me-2"></i>
                                            Ordre: {{ $chapter->ordre }}
                                        </span>
                                        <span>
                                            <i class="fas fa-graduation-cap me-2"></i>
                                            Note de passage: {{ $chapter->note_passage }}/20
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
                        <h5 class="mb-0 text-primary fw-bold">
                            <i class="fas fa-play-circle me-2"></i>Leçons
                        </h5>
                        <button class="btn btn-success btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#addLessonModal">
                            <i class="fas fa-plus me-1"></i>Ajouter
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($chapter->lessons as $lesson)
                    <div class="lesson-item p-3 mb-3 rounded border">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-primary me-2">{{ $lesson->ordre }}</span>
                                    <h6 class="mb-0 fw-bold">{{ $lesson->titre }}</h6>
                                    @if($lesson->active)
                                    <span class="badge bg-success-subtle text-success ms-2">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    @endif
                                </div>
                                <div class="d-flex gap-3 small text-muted">
                                    <span>
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $lesson->duree_minutes }} min
                                    </span>
                                    @if($lesson->url_video)
                                    <span>
                                        <i class="fas fa-video me-1"></i>Vidéo
                                    </span>
                                    @endif
                                    @if($lesson->url_web)
                                    <span>
                                        <i class="fas fa-globe me-1"></i>Web
                                    </span>
                                    @endif
                                    @if($lesson->contenu_texte)
                                    <span>
                                        <i class="fas fa-file-alt me-1"></i>Texte
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary" 
                                        onclick="editLesson({{ $lesson->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger" 
                                        onclick="deleteLesson({{ $lesson->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune leçon pour ce chapitre</p>
                        <button class="btn btn-primary" 
                                data-bs-toggle="modal" 
                                data-bs-target="#addLessonModal">
                            <i class="fas fa-plus me-2"></i>Créer la première leçon
                        </button>
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
                        <h5 class="mb-0 text-success fw-bold">
                            <i class="fas fa-clipboard-question me-2"></i>Quiz
                        </h5>
                        @if(!$chapter->quiz)
                        <button class="btn btn-success btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#addQuizModal">
                            <i class="fas fa-plus me-1"></i>Ajouter
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($chapter->quiz)
                    <div class="quiz-details">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-2">{{ $chapter->quiz->titre }}</h5>
                                <p class="text-muted mb-0">{{ $chapter->quiz->description }}</p>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="stat-card bg-primary-subtle text-primary p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-question-circle fa-2x me-3"></i>
                                        <div>
                                            <div class="fs-4 fw-bold">{{ $chapter->quiz->questions->count() }}</div>
                                            <small>Questions</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-success-subtle text-success p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock fa-2x me-3"></i>
                                        <div>
                                            <div class="fs-4 fw-bold">{{ $chapter->quiz->duree_minutes }}</div>
                                            <small>Minutes</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Questions List -->
                        <div class="questions-list">
                            <h6 class="text-muted mb-3 fw-bold">Questions du Quiz</h6>
                            @foreach($chapter->quiz->questions as $question)
                            <div class="question-item p-3 mb-2 border rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-secondary me-2">Q{{ $question->ordre }}</span>
                                            <span class="badge bg-info-subtle text-info">{{ $question->type }}</span>
                                            <span class="badge bg-warning-subtle text-warning ms-2">
                                                {{ $question->points }} pts
                                            </span>
                                        </div>
                                        <p class="mb-2 small">{{ Str::limit($question->enonce, 100) }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-list me-1"></i>
                                            {{ $question->answers->count() }} réponse(s)
                                        </small>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <button class="btn btn-primary btn-sm w-100 mt-3">
                            <i class="fas fa-plus me-2"></i>Ajouter une Question
                        </button>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-question fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun quiz pour ce chapitre</p>
                        <button class="btn btn-success" 
                                data-bs-toggle="modal" 
                                data-bs-target="#addQuizModal">
                            <i class="fas fa-plus me-2"></i>Créer le quiz
                        </button>
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
                                    {{ $chapter->quiz ? $chapter->quiz->questions->count() : 0 }}
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
                                <p class="text-muted mb-0 small">Note de passage</p>
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        background-color: rgba(102, 126, 234, 0.05);
        border-color: #667eea !important;
    }
    
    .stat-box {
        transition: transform 0.3s ease;
    }
    
    .stat-box:hover {
        transform: translateY(-5px);
    }
</style>
@endsection