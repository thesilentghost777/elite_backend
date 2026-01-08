@extends('admin.layouts.app')

@section('title', 'Gestion des Catégories')

@push('styles')
<style>
    .table-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 2px solid var(--gray-100);
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--success-green) 100%);
        color: var(--white);
    }

    .data-table th {
        padding: 1rem 1.5rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.813rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        white-space: nowrap;
    }

    .data-table td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.875rem;
    }

    .data-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-active {
        background-color: var(--light-green);
        color: var(--dark-green);
    }

    .badge-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .color-preview {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid var(--gray-200);
        display: inline-block;
        vertical-align: middle;
        margin-right: 0.5rem;
    }

    .color-code {
        font-size: 0.75rem;
        color: var(--gray-600);
        font-family: 'Courier New', monospace;
    }

    .actions-cell {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .btn-icon {
        padding: 0.5rem 0.75rem;
        font-size: 0.813rem;
    }

    .btn-view {
        background-color: #8b5cf6;
        color: var(--white);
    }

    .btn-view:hover {
        background-color: #7c3aed;
        transform: translateY(-1px);
    }

    .btn-edit {
        background-color: var(--primary-blue);
        color: var(--white);
    }

    .btn-edit:hover {
        background-color: var(--secondary-blue);
        transform: translateY(-1px);
    }

    .btn-delete {
        background-color: var(--danger);
        color: var(--white);
    }

    .btn-delete:hover {
        background-color: #dc2626;
        transform: translateY(-1px);
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

    .pagination-wrapper {
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--gray-200);
    }

    @media (max-width: 768px) {
        .table-header {
            flex-direction: column;
            align-items: stretch;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem 1rem;
            font-size: 0.813rem;
        }

        .actions-cell {
            flex-direction: column;
        }

        .btn-icon {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Gestion des Catégories</h1>
    <p class="page-description">Gérez toutes les catégories de formations</p>
</div>

<div class="table-card">
    <div class="table-header">
        <h2 class="table-title">Liste des Catégories</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nouvelle Catégorie
        </a>
    </div>

    @if($categories->count() > 0)
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ordre</th>
                        <th>Nom</th>
                        <th>Slug</th>
                        <th>Couleur</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td><strong>{{ $category->ordre }}</strong></td>
                            <td><strong>{{ $category->nom }}</strong></td>
                            <td><code style="background: var(--gray-100); padding: 0.25rem 0.5rem; border-radius: 4px;">{{ $category->slug }}</code></td>
                            <td>
                                @if($category->couleur)
                                    <span class="color-preview" style="background-color: {{ $category->couleur }}"></span>
                                    <span class="color-code">{{ $category->couleur }}</span>
                                @else
                                    <span style="color: var(--gray-400);">Non définie</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $category->active ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $category->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-view btn-icon">
                                        <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Voir
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-edit btn-icon">
                                        <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Modifier
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete btn-icon">
                                            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="pagination-wrapper">
                {{ $categories->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3>Aucune catégorie trouvée</h3>
            <p>Commencez par créer votre première catégorie</p>
        </div>
    @endif
</div>
@endsection
