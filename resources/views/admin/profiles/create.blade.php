@extends('admin.layouts.app')

@section('title', 'Créer un profil métier')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.profiles.index') }}">Profils métiers</a>
    <span>/</span>
    <span>Créer</span>
@endsection

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

    .form-section {
        padding: 2rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 1.5rem;
    }

    .section-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--light-blue);
        color: var(--primary-blue);
        border-radius: 8px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
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

    .form-control.error {
        border-color: var(--danger);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .error-message {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        margin-top: 0.5rem;
        font-size: 0.75rem;
        color: var(--danger);
    }

    .debouches-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .debouche-item {
        display: flex;
        gap: 0.5rem;
    }

    .debouche-item input {
        flex: 1;
    }

    .remove-btn {
        padding: 0.625rem;
        background-color: #fee2e2;
        color: #991b1b;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .remove-btn:hover {
        background-color: var(--danger);
        color: white;
    }

    .add-btn {
        margin-top: 0.5rem;
        padding: 0.625rem 1rem;
        background-color: var(--light-blue);
        color: var(--primary-blue);
        border: 2px dashed var(--primary-blue);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .add-btn:hover {
        background-color: var(--primary-blue);
        color: white;
    }

    .switch-container {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background-color: var(--gray-50);
        border-radius: 8px;
        border: 1px solid var(--gray-200);
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--gray-300);
        transition: 0.3s;
        border-radius: 28px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: var(--success-green);
    }

    input:checked + .slider:before {
        transform: translateX(24px);
    }

    .switch-label {
        flex: 1;
    }

    .switch-label-title {
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
    }

    .switch-label-desc {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .secteur-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .secteur-btn {
        padding: 1rem;
        background-color: var(--white);
        border: 2px solid var(--gray-300);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        font-weight: 500;
        color: var(--gray-700);
    }

    .secteur-btn:hover {
        border-color: var(--primary-blue);
        color: var(--primary-blue);
        background-color: var(--light-blue);
    }

    .secteur-btn.active {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
    }

    .secteur-btn input {
        display: none;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 0;
        padding: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        border: none;
    }

    .btn-secondary {
        background-color: var(--white);
        color: var(--gray-700);
        border: 2px solid var(--gray-300);
    }

    .btn-secondary:hover {
        background-color: var(--gray-50);
        border-color: var(--gray-400);
    }

    .btn-primary {
        background-color: var(--primary-blue);
        color: white;
        flex: 1;
    }

    .btn-primary:hover {
        background-color: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }

    .page-description {
        color: var(--gray-600);
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-section {
            padding: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
            padding: 1.5rem;
        }

        .btn {
            width: 100%;
        }

        .secteur-buttons {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="form-container">
    <div class="page-header">
        <h1 class="page-title">Créer un profil métier</h1>
        <p class="page-description">Définir un nouveau parcours professionnel</p>
    </div>

    <form method="POST" action="{{ route('admin.profiles.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-card">
            <!-- Informations générales -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    Informations générales
                </h3>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">
                            Nom du profil <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nom" 
                            class="form-control @error('nom') error @enderror" 
                            value="{{ old('nom') }}" 
                            required
                            placeholder="Ex: Secrétaire de Direction"
                        >
                        @error('nom')
                            <div class="error-message">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">
                            Description <span class="required">*</span>
                        </label>
                        <textarea 
                            name="description" 
                            class="form-control @error('description') error @enderror" 
                            rows="4" 
                            required
                            placeholder="Décrivez le profil, ses missions principales et les compétences requises..."
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <div class="error-message">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Secteur -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon" style="background-color: var(--light-green); color: var(--success-green);">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    Secteur d'activité
                </h3>

                <div class="form-group">
                    <label class="form-label">
                        Choisir un secteur <span class="required">*</span>
                    </label>
                    
                    <div class="secteur-buttons">
                        @foreach(['Secrétariat', 'Audiovisuel', 'Digital', 'Commerce', 'Industrie', 'Santé', 'Éducation', 'Tourisme'] as $secteur)
                        <label class="secteur-btn">
                            <input type="radio" name="secteur" value="{{ $secteur }}" {{ old('secteur') == $secteur ? 'checked' : '' }} required>
                            {{ $secteur }}
                        </label>
                        @endforeach
                    </div>

                    @error('secteur')
                        <div class="error-message">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <!-- Débouchés -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    Débouchés professionnels
                </h3>

                <div class="form-group">
                    <label class="form-label">
                        Postes et opportunités
                    </label>
                    
                    <div class="debouches-list" id="debouchesList">
                        <div class="debouche-item">
                            <input 
                                type="text" 
                                name="debouches[]" 
                                class="form-control" 
                                placeholder="Ex: Secrétaire de direction"
                            >
                            <button type="button" class="remove-btn" onclick="removeDebouche(this)" style="display: none;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="button" class="add-btn" onclick="addDebouche()">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Ajouter un débouché
                    </button>
                </div>
            </div>

            <!-- Configuration -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    Configuration
                </h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            Niveau minimum requis <span class="required">*</span>
                        </label>
                        <select name="niveau_minimum" class="form-control @error('niveau_minimum') error @enderror" required>
                            <option value="">Sélectionner un niveau</option>
                            <option value="BEPC" {{ old('niveau_minimum') == 'BEPC' ? 'selected' : '' }}>BEPC</option>
                            <option value="Probatoire" {{ old('niveau_minimum') == 'Probatoire' ? 'selected' : '' }}>Probatoire</option>
                            <option value="BAC" {{ old('niveau_minimum') == 'BAC' ? 'selected' : '' }}>BAC</option>
                            <option value="Licence" {{ old('niveau_minimum') == 'Licence' ? 'selected' : '' }}>Licence</option>
                            <option value="Master" {{ old('niveau_minimum') == 'Master' ? 'selected' : '' }}>Master</option>
                        </select>
                        @error('niveau_minimum')
                            <div class="error-message">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div style="display: grid; gap: 1rem; margin-top: 1rem;">
                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="is_cfpam" value="1" {{ old('is_cfpam') ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <div class="switch-label">
                            <div class="switch-label-title">Profil CFPAM</div>
                            <div class="switch-label-desc">Ce profil fait partie du Centre de Formation Professionnelle et Artisanale Maréen</div>
                        </div>
                    </div>

                    <div class="switch-container">
                        <label class="switch">
                            <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                        <div class="switch-label">
                            <div class="switch-label-title">Profil actif</div>
                            <div class="switch-label-desc">Les profils actifs sont visibles et sélectionnables par les utilisateurs</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.profiles.index') }}" class="btn btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Créer le profil
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Gestion des débouchés
    function addDebouche() {
        const list = document.getElementById('debouchesList');
        const newItem = document.createElement('div');
        newItem.className = 'debouche-item';
        newItem.innerHTML = `
            <input 
                type="text" 
                name="debouches[]" 
                class="form-control" 
                placeholder="Ex: Assistant de gestion"
            >
            <button type="button" class="remove-btn" onclick="removeDebouche(this)">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        list.appendChild(newItem);
        updateRemoveButtons();
    }

    function removeDebouche(button) {
        button.closest('.debouche-item').remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const items = document.querySelectorAll('.debouche-item');
        items.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-btn');
            removeBtn.style.display = items.length > 1 ? 'block' : 'none';
        });
    }

    // Gestion des boutons secteur
    document.querySelectorAll('.secteur-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.secteur-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Initialiser l'état actif si un secteur est déjà sélectionné
    document.addEventListener('DOMContentLoaded', function() {
        const checkedRadio = document.querySelector('.secteur-btn input:checked');
        if (checkedRadio) {
            checkedRadio.closest('.secteur-btn').classList.add('active');
        }
    });
</script>
@endpush