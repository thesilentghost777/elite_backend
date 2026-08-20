@extends('layouts.admin')

@section('title', 'Modifier la Question')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.correspondence.index') }}">Correspondances</a></li>
            <li class="breadcrumb-item active">Modifier Question</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4 bg-gradient-info text-white">
                <div class="card-body p-4">
                    <h2 class="mb-2 fw-bold">
                        <i class="fas fa-edit me-2"></i>Modifier la Question
                    </h2>
                    <p class="mb-0 opacity-90">Catégorie : {{ $question->category->nom }}</p>
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
                    <form action="{{ route('admin.correspondence.update-question', $question) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Question Details -->
                        <div class="mb-4">
                            <h5 class="text-primary fw-bold mb-3">
                                <i class="fas fa-info-circle me-2"></i>Détails de la Question
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Texte de la question *</label>
                                <textarea name="texte" class="form-control form-control-lg" rows="3" required>{{ old('texte', $question->question) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Type de question *</label>
                                    <select name="type" class="form-select form-select-lg" required>
                                        <option value="qcm" {{ old('type', $question->type) == 'qcm' ? 'selected' : '' }}>QCM</option>
                                        <option value="oui_non" {{ old('type', $question->type) == 'oui_non' ? 'selected' : '' }}>Oui/Non</option>
                                        <option value="echelle" {{ old('type', $question->type) == 'echelle' ? 'selected' : '' }}>Échelle</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Ordre d'affichage *</label>
                                    <input type="number" name="ordre" class="form-control form-control-lg" 
                                           value="{{ old('ordre', $question->ordre) }}" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Statut</label>
                                    <div class="form-check form-switch mt-2">
                                        <input type="checkbox" name="obligatoire" class="form-check-input" 
                                               id="obligatoire" value="1" {{ old('obligatoire', $question->active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="obligatoire">Question obligatoire</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Existing Answers Display -->
                        <div class="mb-4">
                            <h5 class="text-primary fw-bold mb-3">
                                <i class="fas fa-list-check me-2"></i>Réponses Actuelles
                            </h5>

                            @foreach($question->answers as $answer)
                            <div class="card mb-3 border-start border-success border-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h6 class="text-success mb-0">
                                            <i class="fas fa-check-circle me-2"></i>Réponse {{ $loop->iteration }}
                                        </h6>
                                        <span class="badge bg-success-subtle text-success">{{ $answer->ordre }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold small text-muted">Texte</label>
                                        <div class="form-control-plaintext bg-light rounded p-2">
                                            {{ $answer->texte }}
                                        </div>
                                    </div>

                                    @if($answer->matchings->count() > 0)
                                    <div>
                                        <label class="form-label fw-bold small text-muted mb-2">
                                            <i class="fas fa-link me-1"></i>Correspondances
                                        </label>
                                        <div class="row g-2">
                                            @foreach($answer->matchings as $matching)
                                            <div class="col-md-6">
                                                <div class="card bg-success-subtle border-0">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="fw-bold text-success">
                                                                {{ $matching->profile->nom }}
                                                            </small>
                                                            <span class="badge bg-success">
                                                                Poids: {{ $matching->poids }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @else
                                    <div class="alert alert-warning mb-0" role="alert">
                                        <small><i class="fas fa-exclamation-triangle me-2"></i>Aucune correspondance définie</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                            <div class="alert alert-info mt-3" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note :</strong> Pour modifier les réponses et leurs correspondances, veuillez supprimer cette question et la recréer.
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between pt-4 border-top">
                            <a href="{{ route('admin.correspondence.index') }}" class="btn btn-lg btn-outline-secondary">
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
                    <p class="text-muted mb-3">
                        La suppression de cette question est irréversible. Toutes les réponses et correspondances associées seront également supprimées.
                    </p>
                    <form action="{{ route('admin.correspondence.delete-question', $question) }}" 
                          method="POST" 
                          onsubmit="return confirm('⚠️ ATTENTION ⚠️\n\nÊtes-vous absolument sûr de vouloir supprimer cette question ?\n\nCette action est IRRÉVERSIBLE et supprimera :\n- La question\n- Toutes ses réponses\n- Toutes les correspondances\n- Les réponses des utilisateurs\n\nTapez OUI pour confirmer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-2"></i>Supprimer Définitivement cette Question
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .card {
        transition: all 0.3s ease;
    }
</style>
@endsection