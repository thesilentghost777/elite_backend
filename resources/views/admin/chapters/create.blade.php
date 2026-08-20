@extends('layouts.admin')

@section('title', 'Créer un Chapitre')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.packs.index') }}">Packs</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.packs.show', $module->pack) }}">{{ $module->pack->nom }}</a></li>
            <li class="breadcrumb-item">{{ $module->nom }}</li>
            <li class="breadcrumb-item active">Nouveau Chapitre</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-icon">
                                <i class="fas fa-book-open fa-2x"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="mb-2 fw-bold">Créer un Nouveau Chapitre</h2>
                            <p class="mb-1 opacity-90">
                                <i class="fas fa-layer-group me-2"></i>Module : {{ $module->nom }}
                            </p>
                            <p class="mb-0 opacity-90">
                                <i class="fas fa-box me-2"></i>Pack : {{ $module->pack->nom }}
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
                    <form action="{{ route('admin.chapters.store', $module) }}" method="POST">
                        @csrf

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
                                   value="{{ old('nom') }}" 
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
                                      placeholder="Décrivez brièvement le contenu et les objectifs de ce chapitre...">{{ old('description') }}</textarea>
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
                                       value="{{ old('ordre', 0) }}" 
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
                                       value="{{ old('note_passage', 14) }}" 
                                       min="10" 
                                       max="20"
                                       required>
                                @error('note_passage')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Entre 10 et 20</small>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="alert alert-info border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle fa-lg me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-2">Système de Déblocage</h6>
                                    <ul class="mb-0 small">
                                        <li><strong>Note ≥ {{ old('note_passage', 14) }}/20 :</strong> Chapitre suivant débloqué automatiquement</li>
                                        <li><strong>Note entre 10 et {{ old('note_passage', 14) }}/20 :</strong> Parrainage requis pour débloquer</li>
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
                                       {{ old('active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="active">
                                    Chapitre actif
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-eye me-1"></i>
                                Les chapitres actifs sont visibles par les apprenants
                            </small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between pt-4 border-top">
                            <a href="{{ route('admin.packs.show', $module->pack) }}" class="btn btn-lg btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-lg btn-success">
                                <i class="fas fa-check-circle me-2"></i>Créer le Chapitre
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    
    .alert {
        border-radius: 12px;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection