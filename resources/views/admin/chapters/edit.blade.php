@extends('layouts.admin')

@section('title', 'Modifier le Chapitre')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.packs.index') }}">Packs</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.packs.show', $chapter->module->pack) }}">{{ $chapter->module->pack->nom }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.chapters.show', $chapter) }}">{{ $chapter->nom }}</a></li>
            <li class="breadcrumb-item active">Modifier</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4 bg-gradient-edit text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-icon">
                                <i class="fas fa-edit fa-2x"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="mb-2 fw-bold">Modifier le Chapitre</h2>
                            <p class="mb-1 opacity-90">
                                <i class="fas fa-book-open me-2"></i>{{ $chapter->nom }}
                            </p>
                            <p class="mb-0 opacity-90">
                                <i class="fas fa-layer-group me-2"></i>Module : {{ $chapter->module->nom }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs de validation</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.chapters.update', $chapter) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Informations Générales -->
                        <div class="section-header mb-4">
                            <h5 class="text-primary fw-bold mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informations Générales
                            </h5>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                Nom du chapitre *
                                <i class="fas fa-question-circle text-muted ms-1" 
                                   data-bs-toggle="tooltip" 
                                   title="Donnez un nom clair et descriptif au chapitre"></i>
                            </label>
                            <input type="text" 
                                   name="nom" 
                                   class="form-control form-control-lg @error('nom') is-invalid @enderror" 
                                   value="{{ old('nom', $chapter->nom) }}" 
                                   required
                                   placeholder="Ex: Introduction aux bases de données">
                            @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="4"
                                      placeholder="Décrivez brièvement le contenu et les objectifs de ce chapitre...">{{ old('description', $chapter->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-lightbulb me-1"></i>
                                Expliquez ce que les apprenants vont découvrir dans ce chapitre
                            </small>
                        </div>

                        <hr class="my-4">

                        <!-- Paramètres de Notation -->
                        <div class="section-header mb-4">
                            <h5 class="text-primary fw-bold mb-0">
                                <i class="fas fa-chart-line me-2"></i>Paramètres de Notation
                            </h5>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    Ordre d'affichage *
                                    <i class="fas fa-question-circle text-muted ms-1" 
                                       data-bs-toggle="tooltip" 
                                       title="Position du chapitre dans le module"></i>
                                </label>
                                <input type="number" 
                                       name="ordre" 
                                       class="form-control form-control-lg @error('ordre') is-invalid @enderror" 
                                       value="{{ old('ordre', $chapter->ordre) }}" 
                                       min="0"
                                       required>
                                @error('ordre')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    Note de passage *
                                    <i class="fas fa-question-circle text-muted ms-1" 
                                       data-bs-toggle="tooltip" 
                                       title="Note minimale pour réussir le chapitre (sur 20)"></i>
                                </label>
                                <input type="number" 
                                       name="note_passage" 
                                       class="form-control form-control-lg @error('note_passage') is-invalid @enderror" 
                                       value="{{ old('note_passage', $chapter->note_passage) }}" 
                                       min="10" 
                                       max="20"
                                       required
                                       id="notePassage">
                                @error('note_passage')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Entre 10 et 20</small>
                            </div>
                        </div>

                        <!-- Dynamic Info Box -->
                        <div class="alert alert-info border-0 shadow-sm" role="alert" id="infoBox">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-2">Système de Déblocage</h6>
                                    <ul class="mb-0 small">
                                        <li><strong>Note ≥ <span id="noteValue">{{ $chapter->note_passage }}</span>/20 :</strong> Chapitre suivant débloqué automatiquement</li>
                                        <li><strong>Note entre 10 et <span id="noteValue2">{{ $chapter->note_passage }}</span>/20 :</strong> Parrainage requis pour débloquer</li>
                                        <li><strong>Note < 10/20 :</strong> Repasser l'évaluation</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Statut -->
                        <div class="section-header mb-4">
                            <h5 class="text-primary fw-bold mb-0">
                                <i class="fas fa-toggle-on me-2"></i>Statut
                            </h5>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="active" 
                                       id="active" 
                                       value="1" 
                                       {{ old('active', $chapter->active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="active">
                                    Chapitre actif
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-eye me-1"></i>
                                Les chapitres actifs sont visibles par les apprenants
                            </small>
                        </div>

                        <!-- Statistics Section -->
                        <div class="alert alert-light border shadow-sm" role="alert">
                            <h6 class="fw-bold text-muted mb-3">
                                <i class="fas fa-chart-bar me-2"></i>Statistiques Actuelles
                            </h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="stat-mini p-2">
                                        <div class="fs-3 fw-bold text-primary">{{ $chapter->lessons->count() }}</div>
                                        <small class="text-muted">Leçons</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-mini p-2">
                                        <div class="fs-3 fw-bold text-success">
                                            {{ $chapter->quizzes->count() }}
                                        </div>
                                        <small class="text-muted">Quiz</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-mini p-2">
                                        <div class="fs-3 fw-bold text-info">
                                            {{ $chapter->lessons->sum('duree_minutes') }}
                                        </div>
                                        <small class="text-muted">Minutes</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between pt-4 border-top">
                            <a href="{{ route('admin.chapters.show', $chapter) }}" class="btn btn-lg btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-lg btn-primary">
                                    <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card border-danger shadow-sm mt-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Zone Dangereuse
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="fw-bold mb-2">Supprimer ce Chapitre</h6>
                            <p class="text-muted mb-3 mb-md-0">
                                La suppression est irréversible. Toutes les leçons, quiz et progression des utilisateurs seront perdus.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <button type="button" 
                                    class="btn btn-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal">
                                <i class="fas fa-trash-alt me-2"></i>Supprimer le Chapitre
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmation de Suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                    <h5 class="fw-bold">Êtes-vous absolument sûr ?</h5>
                </div>
                <div class="alert alert-danger" role="alert">
                    <h6 class="alert-heading fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Cette action va supprimer :
                    </h6>
                    <ul class="mb-0">
                        <li>Le chapitre "{{ $chapter->nom }}"</li>
                        <li>{{ $chapter->lessons->count() }} leçon(s) associée(s)</li>
                        <li>{{ $chapter->quizzes->count() }} quiz associé(s)</li>
                        <li>Toute la progression des utilisateurs</li>
                    </ul>
                </div>
                <p class="text-center text-muted mb-0">
                    <strong>Cette action est irréversible et ne peut pas être annulée.</strong>
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Oui, Supprimer Définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-edit {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .avatar-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .section-header {
        border-left: 4px solid #667eea;
        padding-left: 15px;
    }
    
    .form-control-lg {
        border-radius: 8px;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .stat-mini {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(102, 126, 234, 0.05) 100%);
        border-radius: 8px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Update dynamic info box
    const noteInput = document.getElementById('notePassage');
    const noteValue1 = document.getElementById('noteValue');
    const noteValue2 = document.getElementById('noteValue2');

    if (noteInput) {
        noteInput.addEventListener('input', function() {
            const value = this.value;
            if (noteValue1) noteValue1.textContent = value;
            if (noteValue2) noteValue2.textContent = value;
        });
    }
});
</script>
@endsection