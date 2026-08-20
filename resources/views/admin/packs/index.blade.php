@extends('admin.layouts.app')

@section('title', 'Gestion des Packs')

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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-card.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: var(--white);
    }

    .stat-card.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: var(--white);
    }

    .stat-card.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: var(--white);
    }

    .stat-card.orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: var(--white);
    }

    .stat-content h3 {
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .stat-content p {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-icon {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon svg {
        width: 24px;
        height: 24px;
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

    .data-table th.text-center {
        text-align: center;
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

    .pack-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .pack-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--success-green) 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: 700;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .pack-details h4 {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 0.25rem 0;
    }

    .pack-details p {
        font-size: 0.75rem;
        color: var(--gray-600);
        margin: 0;
    }

    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        text-align: center;
    }

    .badge-blue {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .badge-purple {
        background-color: #ede9fe;
        color: #6b21a8;
    }

    .badge-orange {
        background-color: #ffedd5;
        color: #9a3412;
    }

    .badge-active {
        background-color: var(--light-green);
        color: var(--dark-green);
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .badge-active:hover {
        background-color: #bbf7d0;
    }

    .badge-inactive {
        background-color: #fee2e2;
        color: #991b1b;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .badge-inactive:hover {
        background-color: #fecaca;
    }

    .badge svg {
        width: 14px;
        height: 14px;
    }

    .price-tag {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--success-green);
    }

    .actions-cell {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
    }

    .btn-icon {
        padding: 0.5rem 0.75rem;
        font-size: 0.813rem;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .btn-icon svg {
        width: 16px;
        height: 16px;
    }

    .btn-view {
        background-color: #3b82f6;
        color: var(--white);
    }

    .btn-view:hover {
        background-color: #2563eb;
        transform: translateY(-1px);
    }

    .btn-edit {
        background-color: var(--success-green);
        color: var(--white);
    }

    .btn-edit:hover {
        background-color: #059669;
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
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Réinitialiser et styliser la pagination Laravel */
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

/* Styliser les boutons de pagination */
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
    border-color: var(--primary-color, #3b82f6);
    color: var(--primary-color, #3b82f6);
}

/* Page active */
.pagination-wrapper span[aria-current="page"] {
    background: var(--primary-color, #3b82f6);
    color: white;
    border-color: var(--primary-color, #3b82f6);
    font-weight: 600;
}

/* Boutons désactivés */
.pagination-wrapper span[aria-disabled="true"] {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

/* Texte de résultats */
.pagination-wrapper p {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin: 0;
}

/* Responsive */
@media (max-width: 640px) {
    .pagination-wrapper {
        padding: 1rem;
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
}
    .alert-success {
        background-color: #d1fae5;
        border-left: 4px solid var(--success-green);
        color: #065f46;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        animation: slideIn 0.3s ease-out;
    }

    .alert-success svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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
    <h1 class="page-title">Gestion des Packs</h1>
    <p class="page-description">Gérez tous les packs de formation disponibles</p>
</div>

@if(session('success'))
<div class="alert-success">
    <svg fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-content">
            <h3>Total Packs</h3>
            <p>{{ $packs->total() }}</p>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
    </div>

    <div class="stat-card green">
        <div class="stat-content">
            <h3>Packs Actifs</h3>
            <p>{{ $packs->where('active', true)->count() }}</p>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <div class="stat-card purple">
        <div class="stat-content">
            <h3>Modules Total</h3>
            <p>{{ $packs->sum('modules_count') }}</p>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </div>
    </div>

    <div class="stat-card orange">
        <div class="stat-content">
            <h3>Inscriptions</h3>
            <p>{{ $packs->sum('user_packs_count') }}</p>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-header">
        <h2 class="table-title">Liste des Packs</h2>
        <a href="{{ route('admin.packs.create') }}" class="btn btn-primary">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Créer un Pack
        </a>
    </div>

    @if($packs->count() > 0)
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Pack</th>
                        <th>Catégorie</th>
                        <th>Niveau</th>
                        <th>Prix</th>
                        <th class="text-center">Modules</th>
                        <th class="text-center">Inscrits</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($packs as $pack)
                        <tr>
                            <td>
                                <div class="pack-info">
                                    <div class="pack-avatar">{{ substr($pack->nom, 0, 2) }}</div>
                                    <div class="pack-details">
                                        <h4>{{ $pack->nom }}</h4>
                                        <p>{{ Str::limit($pack->description, 40) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-blue">{{ $pack->category->nom }}</span>
                            </td>
                            <td>{{ $pack->niveau_requis }}</td>
                            <td>
                                <span class="price-tag">{{ $pack->prix_points }} pts</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-purple">{{ $pack->modules_count }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge badge-orange">{{ $pack->user_packs_count }}</span>
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.packs.toggle-active', $pack) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="badge {{ $pack->active ? 'badge-active' : 'badge-inactive' }}" style="border: none; cursor: pointer;">
                                        @if($pack->active)
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Actif
                                        @else
                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            Inactif
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <a href="{{ route('admin.packs.show', $pack) }}" class="btn btn-view btn-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Voir
                                    </a>
                                   
                                    <form action="{{ route('admin.packs.destroy', $pack) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce pack ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete btn-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        @if($packs->hasPages())
            <div class="pagination-wrapper">
                {{ $packs->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3>Aucun pack trouvé</h3>
            <p>Commencez par créer votre premier pack</p>
        </div>
    @endif
</div>
@endsection