@extends('admin.layouts.app')

@section('title', 'Modifier utilisateur')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.users.index') }}">Utilisateurs</a>
    <span>/</span>
    <span>Modifier {{ $user->prenom }} {{ $user->nom }}</span>
@endsection

@section('content')
<style>
    .form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .form-card {
        background-color: var(--white);
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background-color: var(--light-blue);
        color: var(--primary-blue);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .form-label .required {
        color: var(--danger);
    }

    .form-control {
        padding: 0.75rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s;
        width: 100%;
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-control:disabled {
        background-color: var(--gray-100);
        cursor: not-allowed;
    }

    .form-help {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin-top: 0.25rem;
    }

    .error-message {
        font-size: 0.75rem;
        color: var(--danger);
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .input-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .input-prefix {
        padding: 0.75rem 1rem;
        background-color: var(--gray-100);
        border: 1px solid var(--gray-300);
        border-radius: 8px 0 0 8px;
        font-size: 0.875rem;
        color: var(--gray-700);
        font-weight: 500;
    }

    .input-group .form-control {
        border-radius: 0 8px 8px 0;
        border-left: none;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 1.5rem;
        border-top: 2px solid var(--gray-200);
    }

    .info-box {
        background-color: var(--light-blue);
        border: 1px solid var(--primary-blue);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 0.75rem;
    }

    .info-box-icon {
        flex-shrink: 0;
        color: var(--primary-blue);
    }

    .info-box-content {
        flex: 1;
    }

    .info-box-title {
        font-weight: 600;
        color: var(--secondary-blue);
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }

    .info-box-text {
        font-size: 0.75rem;
        color: var(--secondary-blue);
        line-height: 1.5;
    }

    .danger-zone {
        border: 2px solid var(--danger);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .danger-zone-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .danger-zone-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--danger);
    }

    .danger-zone-description {
        color: var(--gray-700);
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 1.5rem;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions button,
        .form-actions a {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="form-container">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">Modifier l'utilisateur</h1>
        <p class="page-description">Mettre à jour les informations de {{ $user->prenom }} {{ $user->nom }}</p>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <div class="info-box-icon">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="info-box-content">
            <div class="info-box-title">Informations importantes</div>
            <div class="info-box-text">
                Les modifications apportées à ce profil seront immédiatement visibles pour l'utilisateur. 
                Le code de parrainage et l'email ne peuvent être modifiés que si aucune donnée n'y est liée.
            </div>
        </div>
    </div>

    <!-- Formulaire principal -->
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <!-- Informations personnelles -->
        <div class="form-card">
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    Informations personnelles
                </h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            Nom <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nom" 
                            class="form-control @error('nom') error @enderror" 
                            value="{{ old('nom', $user->nom) }}" 
                            required
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

                    <div class="form-group">
                        <label class="form-label">
                            Prénom <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="prenom" 
                            class="form-control @error('prenom') error @enderror" 
                            value="{{ old('prenom', $user->prenom) }}" 
                            required
                        >
                        @error('prenom')
                            <div class="error-message">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Téléphone <span class="required">*</span>
                        </label>
                        <input 
                            type="tel" 
                            name="telephone" 
                            class="form-control @error('telephone') error @enderror" 
                            value="{{ old('telephone', $user->telephone) }}" 
                            required
                        >
                        @error('telephone')
                            <div class="error-message">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Email
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            class="form-control @error('email') error @enderror" 
                            value="{{ old('email', $user->email) }}"
                        >
                        <div class="form-help">L'email est optionnel</div>
                        @error('email')
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

            <!-- Informations académiques et géographiques -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon" style="background-color: var(--light-green); color: var(--success-green);">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                        </svg>
                    </div>
                    Informations académiques et localisation
                </h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            Ville <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="ville" 
                            class="form-control @error('ville') error @enderror" 
                            value="{{ old('ville', $user->ville) }}" 
                            required
                        >
                        @error('ville')
                            <div class="error-message">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Dernier diplôme <span class="required">*</span>
                        </label>
                        <select 
                            name="dernier_diplome" 
                            class="form-control @error('dernier_diplome') error @enderror" 
                            required
                        >
                            <option value="">Sélectionner un diplôme</option>
                            <option value="BEPC" {{ old('dernier_diplome', $user->dernier_diplome) == 'BEPC' ? 'selected' : '' }}>BEPC</option>
                            <option value="Probatoire" {{ old('dernier_diplome', $user->dernier_diplome) == 'Probatoire' ? 'selected' : '' }}>Probatoire</option>
                            <option value="BAC" {{ old('dernier_diplome', $user->dernier_diplome) == 'BAC' ? 'selected' : '' }}>BAC</option>
                            <option value="Licence" {{ old('dernier_diplome', $user->dernier_diplome) == 'Licence' ? 'selected' : '' }}>Licence</option>
                            <option value="Master" {{ old('dernier_diplome', $user->dernier_diplome) == 'Master' ? 'selected' : '' }}>Master</option>
                        </select>
                        @error('dernier_diplome')
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

            <!-- Gestion des points -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <div class="section-icon" style="background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white;">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    Solde de points
                </h3>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            Solde de points <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <input 
                                type="number" 
                                name="solde_points" 
                                class="form-control @error('solde_points') error @enderror" 
                                value="{{ old('solde_points', $user->solde_points) }}" 
                                step="0.01"
                                min="0"
                                required
                            >
                            <span class="input-prefix" style="border-radius: 0 8px 8px 0; border-left: 1px solid var(--gray-300);">pts</span>
                        </div>
                        <div class="form-help">Attention : Modifier directement le solde peut affecter l'historique</div>
                        @error('solde_points')
                            <div class="error-message">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Code de parrainage
                        </label>
                        <input 
                            type="text" 
                            class="form-control" 
                            value="{{ $user->referral_code }}" 
                            disabled
                            style="font-family: monospace; font-weight: 600;"
                        >
                        <div class="form-help">Le code de parrainage ne peut pas être modifié</div>
                    </div>
                </div>
            </div>

            <!-- Actions du formulaire -->
            <div class="form-actions">
                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>

    <!-- Zone dangereuse -->
    <div class="form-card danger-zone">
        <div class="danger-zone-header">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="danger-zone-title">Zone dangereuse</h3>
        </div>
        
        <p class="danger-zone-description">
            La suppression de cet utilisateur est irréversible. Toutes ses données, transactions, 
            et progressions seront définitivement perdues.
        </p>

        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
              onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer cet utilisateur ? Cette action est IRRÉVERSIBLE.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Supprimer définitivement cet utilisateur
            </button>
        </form>
    </div>
</div>

@push('styles')
<style>
    .form-control.error {
        border-color: var(--danger);
    }

    .form-control.error:focus {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
</style>
@endpush
@endsection