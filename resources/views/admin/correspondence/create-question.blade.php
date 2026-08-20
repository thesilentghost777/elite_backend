@extends('layouts.admin')

@section('title', 'Créer une Question')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.correspondence.index') }}">Correspondances</a></li>
            <li class="breadcrumb-item active">Nouvelle Question</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm mb-4 bg-gradient-success text-white">
                <div class="card-body p-4">
                    <h2 class="mb-2 fw-bold">
                        <i class="fas fa-question-circle me-2"></i>Nouvelle Question
                    </h2>
                    <p class="mb-0 opacity-90">Catégorie : {{ $category->nom }}</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.correspondence.store-question', $category) }}" method="POST" id="questionForm">
                        @csrf

                        <!-- Question Details -->
                        <div class="mb-4">
                            <h5 class="text-primary fw-bold mb-3">
                                <i class="fas fa-info-circle me-2"></i>Détails de la Question
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Texte de la question *</label>
                                <textarea name="texte" class="form-control form-control-lg" rows="3" required 
                                          placeholder="Entrez votre question...">{{ old('texte') }}</textarea>
                                @error('texte')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Type de question *</label>
                                    <select name="type" class="form-select form-select-lg" required>
                                        <option value="qcm" {{ old('type') == 'qcm' ? 'selected' : '' }}>QCM (Choix Multiple)</option>
                                        <option value="oui_non" {{ old('type') == 'oui_non' ? 'selected' : '' }}>Oui/Non</option>
                                        <option value="echelle" {{ old('type') == 'echelle' ? 'selected' : '' }}>Échelle</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Ordre d'affichage *</label>
                                    <input type="number" name="ordre" class="form-control form-control-lg" 
                                           value="{{ old('ordre', 0) }}" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Obligatoire ?</label>
                                    <div class="form-check form-switch mt-2">
                                        <input type="checkbox" name="obligatoire" class="form-check-input" 
                                               id="obligatoire" value="1" {{ old('obligatoire') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="obligatoire">Question obligatoire</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Answers Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="text-primary fw-bold mb-0">
                                    <i class="fas fa-list-check me-2"></i>Réponses Possibles
                                </h5>
                                <button type="button" class="btn btn-success" id="addAnswer">
                                    <i class="fas fa-plus me-2"></i>Ajouter une Réponse
                                </button>
                            </div>

                            <div id="answersContainer">
                                <!-- Initial Answer Fields (minimum 2) -->
                                @for($i = 0; $i < 2; $i++)
                                <div class="answer-item card mb-3 border-start border-primary border-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="text-primary mb-0">Réponse {{ $i + 1 }}</h6>
                                            @if($i > 1)
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-answer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Texte de la réponse *</label>
                                            <input type="text" name="answers[{{ $i }}][texte]" 
                                                   class="form-control" required 
                                                   placeholder="Ex: J'aime travailler en équipe">
                                        </div>

                                        <div>
                                            <label class="form-label fw-bold mb-2">
                                                <i class="fas fa-link me-1"></i>Correspondances avec les Profils
                                            </label>
                                            <div class="row g-2">
                                                @foreach($profiles as $profile)
                                                <div class="col-md-6">
                                                    <div class="card bg-light border-0">
                                                        <div class="card-body p-2">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-grow-1">
                                                                    <small class="fw-bold">{{ $profile->nom }}</small>
                                                                </div>
                                                                <input type="number" 
                                                                       name="answers[{{ $i }}][matchings][{{ $profile->id }}]" 
                                                                       class="form-control form-control-sm" 
                                                                       style="width: 70px;"
                                                                       min="0" 
                                                                       max="10" 
                                                                       value="0"
                                                                       placeholder="0-10">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Poids de 0 à 10 (0 = aucune correspondance, 10 = correspondance forte)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between pt-4 border-top">
                            <a href="{{ route('admin.correspondence.index') }}" class="btn btn-lg btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary">
                                <i class="fas fa-save me-2"></i>Créer la Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Answer Template (Hidden) -->
<template id="answerTemplate">
    <div class="answer-item card mb-3 border-start border-primary border-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="text-primary mb-0">Réponse <span class="answer-number"></span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-answer">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Texte de la réponse *</label>
                <input type="text" name="answers[INDEX][texte]" class="form-control" required>
            </div>

            <div>
                <label class="form-label fw-bold mb-2">
                    <i class="fas fa-link me-1"></i>Correspondances avec les Profils
                </label>
                <div class="row g-2">
                    @foreach($profiles as $profile)
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <small class="fw-bold">{{ $profile->nom }}</small>
                                    </div>
                                    <input type="number" 
                                           name="answers[INDEX][matchings][{{ $profile->id }}]" 
                                           class="form-control form-control-sm" 
                                           style="width: 70px;"
                                           min="0" 
                                           max="10" 
                                           value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    
    .answer-item {
        transition: all 0.3s ease;
    }
    
    .answer-item:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let answerIndex = 2;
    const answersContainer = document.getElementById('answersContainer');
    const addAnswerBtn = document.getElementById('addAnswer');
    const template = document.getElementById('answerTemplate');

    addAnswerBtn.addEventListener('click', function() {
        const clone = template.content.cloneNode(true);
        const answerDiv = clone.querySelector('.answer-item');
        
        // Update answer number
        clone.querySelector('.answer-number').textContent = answerIndex + 1;
        
        // Replace INDEX with actual index
        answerDiv.innerHTML = answerDiv.innerHTML.replaceAll('INDEX', answerIndex);
        
        answersContainer.appendChild(clone);
        answerIndex++;
        
        // Add remove functionality
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        document.querySelectorAll('.remove-answer').forEach(btn => {
            btn.addEventListener('click', function() {
                if (document.querySelectorAll('.answer-item').length > 2) {
                    this.closest('.answer-item').remove();
                    updateAnswerNumbers();
                } else {
                    alert('Vous devez avoir au moins 2 réponses.');
                }
            });
        });
    }

    function updateAnswerNumbers() {
        document.querySelectorAll('.answer-item').forEach((item, index) => {
            item.querySelector('.answer-number').textContent = index + 1;
        });
    }

    updateRemoveButtons();
});
</script>
@endsection