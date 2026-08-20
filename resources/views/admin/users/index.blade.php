@extends('admin.layouts.app')

@section('title', 'Gestion des utilisateurs')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <span>Utilisateurs</span>
@endsection

@push('styles')
<style>
    .filters-section {
        background-color: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-700);
    }

    .form-control {
        padding: 0.625rem 1rem;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s;
        width: 100%;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .user-avatar-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-blue), var(--success-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: 600;
        font-size: 0.875rem;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 500;
        color: var(--gray-900);
    }

    .user-email {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .points-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        padding: 0.5rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .btn-view {
        background-color: #3b82f6;
        color: white;
    }

    .btn-view:hover {
        background-color: #2563eb;
        transform: translateY(-1px);
    }

    .btn-edit {
        background-color: var(--success-green);
        color: white;
    }

    .btn-edit:hover {
        background-color: #059669;
        transform: translateY(-1px);
    }

    .pagination-wrapper {
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .pagination-wrapper nav {
        width: 100%;
    }

    .pagination-wrapper .flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pagination-wrapper .relative {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .pagination-wrapper a,
    .pagination-wrapper span {
        padding: 0.5rem 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 0.375rem;
        text-decoration: none;
        color: var(--gray-700);
        background: white;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
    }

    .pagination-wrapper a:hover {
        background: var(--gray-50);
        border-color: var(--primary-blue);
        color: var(--primary-blue);
    }

    .pagination-wrapper span[aria-current="page"] {
        background: var(--primary-blue);
        color: white;
        border-color: var(--primary-blue);
        font-weight: 600;
    }

    .pagination-wrapper span[aria-disabled="true"] {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .pagination-wrapper p {
        color: var(--gray-600);
        font-size: 0.875rem;
        margin: 0;
    }

    .stats-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .summary-card {
        background-color: var(--white);
        border-radius: 12px;
        padding: 1.25rem;
        border-left: 4px solid var(--primary-blue);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .summary-card.green {
        border-left-color: var(--success-green);
    }

    .summary-label {
        font-size: 0.75rem;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .summary-value {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--gray-900);
    }

    .card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--success-green) 100%);
        color: var(--white);
    }

    th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.813rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.875rem;
    }

    tbody tr {
        transition: background-color 0.2s ease;
    }

    tbody tr:hover {
        background-color: var(--gray-50);
    }

    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        text-align: center;
    }

    .badge-info {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--gray-600);
    }

    .empty-state svg {
        width: 80px;
        height: 80px;
        margin-bottom: 1.5rem;
        opacity: 0.3;
        color: var(--gray-400);
    }

    .empty-state h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    @media (max-width: 768px) {
        .filters-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .table-container {
            overflow-x: auto;
        }

        .pagination-wrapper .flex {
            flex-direction: column;
            text-align: center;
        }

        .pagination-wrapper a,
        .pagination-wrapper span {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }

        th, td {
            padding: 0.75rem 1rem;
            font-size: 0.813rem;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Gestion des utilisateurs</h1>
        <p class="page-description">Liste complète des utilisateurs inscrits sur la plateforme</p>
    </div>
</div>

<!-- Statistiques résumées -->
<div class="stats-summary">
    <div class="summary-card">
        <div class="summary-label">Total utilisateurs</div>
        <div class="summary-value">{{ $users->total() }}</div>
    </div>
    <div class="summary-card green">
        <div class="summary-label">Nouveaux ce mois</div>
        <div class="summary-value">{{ $newThisMonth ?? 0 }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Profils choisis</div>
        <div class="summary-value">{{ $profilesChosen ?? 0 }}</div>
    </div>
</div>

<!-- Filtres de recherche -->
<div class="filters-section">
    <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="filters-grid">
            <div class="form-group">
                <label class="form-label">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Rechercher
                </label>
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Nom, prénom, téléphone, email..." 
                    value="{{ request('search') }}"
                >
            </div>

            <div class="form-group">
                <label class="form-label">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Ville
                </label>
                <select name="ville" class="form-control">
                    <option value="">Toutes les villes</option>
                    @foreach($villes ?? [] as $ville)
                        <option value="{{ $ville }}" {{ request('ville') == $ville ? 'selected' : '' }}>
                            {{ $ville }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline; vertical-align: middle;">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                    </svg>
                    Diplôme
                </label>
                <select name="diplome" class="form-control">
                    <option value="">Tous les diplômes</option>
                    @foreach($diplomes ?? [] as $diplome)
                        <option value="{{ $diplome }}" {{ request('diplome') == $diplome ? 'selected' : '' }}>
                            {{ $diplome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" style="opacity: 0;">Actions</label>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtrer
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Table des utilisateurs -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            Liste des utilisateurs 
            <span style="font-weight: normal; color: var(--gray-600);">({{ $users->total() }})</span>
        </h2>
    </div>

    @if($users->count() > 0)
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Téléphone</th>
                    <th>Ville</th>
                    <th>Diplôme</th>
                    <th>Points</th>
                    <th>Packs</th>
                    <th>Filleuls</th>
                    <th>Inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div class="user-avatar-cell">
                            <div class="user-avatar-img">
                                {{ strtoupper(substr($user->prenom, 0, 1)) }}{{ strtoupper(substr($user->nom, 0, 1)) }}
                            </div>
                            <div class="user-info">
                                <span class="user-name">{{ $user->prenom }} {{ $user->nom }}</span>
                                @if($user->email)
                                    <span class="user-email">{{ $user->email }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->telephone }}</td>
                    <td>{{ $user->ville }}</td>
                    <td>
                        <span class="badge badge-info">{{ $user->dernier_diplome }}</span>
                    </td>
                    <td>
                        <span class="points-badge">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            {{ number_format($user->solde_points, 0, ',', ' ') }}
                        </span>
                    </td>
                    <td>{{ $user->packs_count ?? 0 }}</td>
                    <td>{{ $user->filleuls_count ?? 0 }}</td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn-icon btn-view" title="Voir">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
    @endif
    @else
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        <h3>Aucun utilisateur trouvé</h3>
        <p>Essayez d'ajuster vos filtres de recherche</p>
    </div>
    @endif
</div>
@endsection