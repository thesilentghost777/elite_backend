@extends('admin.layouts.app')

@section('title', 'Modifier la Catégorie')

@push('styles')
<style>
    .form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .form-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .form-card-body {
        padding: 2.5rem;
    }

    .form-group {
        margin-bottom: 1.75rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.875rem;
    }

    .form-label .required {
        color: var(--danger);
        margin-left: 0.25rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        font-size: 0.875rem;
        font-family: inherit;
        transition: all 0.2s ease;
        background-color: var(--white);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .form-hint {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-top: 0.375rem;
    }

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }

    .checkbox-input {
        width: 1.25rem;
        height: 1.25rem;
        cursor: pointer;
        accent-color: var(--primary-blue);
    }

    .checkbox-label {
        margin: 0;
        font-weight: 500;
        cursor: pointer;
    }

    .color-input-group {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .color-picker {
        width: 60px;
        height: 45px;
        padding: 0.25rem;
        cursor: pointer;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
    }

    .color-text-input {
        flex: 1;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 2px solid var(--gray-100);
    }

    .error-list {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--danger);
    }

    .error-list strong {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .error-list ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .error-list li {
        padding: 0.25rem 0;
        font-size: 0.875rem;
    }

    .info-box {
        background-color: var(--light-blue);
        border-left: 4px solid var(--primary-blue);
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .info-box strong {
        color: var(--secondary-blue);
        display: block;
        margin-bottom: 0.375rem;
        font-weight: 600;
    }

    .info-box p {
        color: var(--secondary-blue);
        font-size: 0.813rem;
        margin: 0;
    }

    @media (max-width: 768px) {
        .form-card-body {
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .color-input-group {
            flex-direction: column;
        }

        .color-picker {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1 class="page-title">Modifier la Catégorie</h1>
        <p class="page-description">Modifiez les informations de la catégorie "{{ $category->nom }}"</p>
    </div>

    <div class="form-card">
        <div class="form-card-body">
            @if ($errors->any())
                <div class="error-list">
                    <strong>Erreurs de validation :</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="info-box">
                <strong>Information</strong>
                <p>Créée le {{ $category->created_at->format('d/m/Y à H:i') }} - Dernière modification le {{ $category->updated_at->format('d/m/Y à H:i') }}</p>
            </div>

            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nom" class="form-label">
                        Nom de la catégorie <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        class="form-control"
                        value="{{ old('nom', $category->nom) }}"
                        placeholder="Ex: Développement Web"
                        required
                    >
                    <div class="form-hint">Le nom principal de votre catégorie</div>
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">Slug (URL)</label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        class="form-control"
                        value="{{ old('slug', $category->slug) }}"
                        placeholder="Ex: developpement-web"
                    >
                    <div class="form-hint">Sera regénéré automatiquement si laissé vide</div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        placeholder="Décrivez cette catégorie..."
                    >{{ old('description', $category->description) }}</textarea>
                    <div class="form-hint">Une brève description de la catégorie</div>
                </div>

                <div class="form-group">
                    <label for="image_url" class="form-label">URL de l'image</label>
                    <input
                        type="url"
                        id="image_url"
                        name="image_url"
                        class="form-control"
                        value="{{ old('image_url', $category->image_url) }}"
                        placeholder="https://exemple.com/image.jpg"
                    >
                    <div class="form-hint">URL complète vers l'image de la catégorie</div>
                </div>

                <div class="form-group">
                    <label for="couleur" class="form-label">Couleur</label>
                    <div class="color-input-group">
                        <input
                            type="color"
                            id="couleur_picker"
                            class="color-picker"
                            value="{{ old('couleur', $category->couleur ?? '#2563eb') }}"
                            onchange="document.getElementById('couleur').value = this.value"
                        >
                        <input
                            type="text"
                            id="couleur"
                            name="couleur"
                            class="form-control color-text-input"
                            value="{{ old('couleur', $category->couleur ?? '#2563eb') }}"
                            placeholder="#2563eb"
                            maxlength="7"
                            onchange="document.getElementById('couleur_picker').value = this.value"
                        >
                    </div>
                    <div class="form-hint">Code couleur hexadécimal pour identifier la catégorie</div>
                </div>

                <div class="form-group">
                    <label for="ordre" class="form-label">Ordre d'affichage</label>
                    <input
                        type="number"
                        id="ordre"
                        name="ordre"
                        class="form-control"
                        value="{{ old('ordre', $category->ordre) }}"
                        min="0"
                    >
                    <div class="form-hint">Ordre d'affichage (0 = premier)</div>
                </div>

                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input
                            type="checkbox"
                            id="active"
                            name="active"
                            class="checkbox-input"
                            value="1"
                            {{ old('active', $category->active) ? 'checked' : '' }}
                        >
                        <label for="active" class="checkbox-label">Catégorie active</label>
                    </div>
                    <div class="form-hint">Décochez pour désactiver la catégorie</div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-success" style="flex: 1;">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
