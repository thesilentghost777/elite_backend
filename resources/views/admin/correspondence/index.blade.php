@extends('layouts.admin')

@section('title', 'Gestion des Correspondances')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-2 fw-bold">Formulaire de Correspondance</h2>
                            <p class="mb-0 opacity-90">Gérez les questions et catégories du formulaire d'orientation</p>
                        </div>
                        <button class="btn btn-light btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                            <i class="fas fa-plus-circle me-2"></i>Nouvelle Catégorie
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Categories List -->
    <div class="row">
        @forelse($categories as $category)
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm hover-lift">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-primary fw-bold">{{ $category->nom }}</h4>
                            <p class="mb-0 text-muted small">{{ $category->description }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary-subtle text-primary fs-6">
                                {{ $category->questions->count() }} question(s)
                            </span>
                            <a href="{{ route('admin.correspondence.create-question', $category) }}" 
                               class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-1"></i>Ajouter Question
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($category->questions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Ordre</th>
                                    <th>Question</th>
                                    <th>Type</th>
                                    <th>Réponses</th>
                                    <th>Statut</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->questions as $question)
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-secondary">{{ $question->ordre }}</span>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 400px;">
                                            {{ $question->question }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">
                                            {{ strtoupper($question->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $question->answers_count }} réponse(s)
                                        </span>
                                    </td>
                                    <td>
                                        @if($question->active)
                                        <span class="badge bg-success-subtle text-success">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                        @else
                                        <span class="badge bg-danger-subtle text-danger">
                                            <i class="fas fa-times-circle me-1"></i>Inactive
                                        </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.correspondence.edit-question', $question) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.correspondence.delete-question', $question) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?');"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune question dans cette catégorie</p>
                        <a href="{{ route('admin.correspondence.create-question', $category) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer la première question
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted mb-3">Aucune catégorie créée</h4>
                    <p class="text-muted mb-4">Commencez par créer une catégorie pour organiser vos questions</p>
                    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                        <i class="fas fa-plus-circle me-2"></i>Créer une Catégorie
                    </button>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Create Category -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-folder-plus me-2"></i>Nouvelle Catégorie
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.correspondence.store-category') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom de la catégorie *</label>
                        <input type="text" name="nom" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ordre d'affichage *</label>
                        <input type="number" name="ordre" class="form-control" value="0" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Créer la Catégorie
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
    
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }
</style>
@endsection