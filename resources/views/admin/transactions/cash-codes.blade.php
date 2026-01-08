@extends('admin.layouts.app')

@section('title', 'Gestion des Codes Caisse')

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

    .code-display {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-900);
        background: var(--gray-100);
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        display: inline-block;
        letter-spacing: 0.05em;
    }

    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge-used {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-expired {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .badge svg {
        width: 14px;
        height: 14px;
    }

    .amount-display {
        font-weight: 700;
        color: #059669;
    }

    .points-display {
        font-weight: 700;
        color: #3b82f6;
    }

    .user-link {
        color: var(--primary-blue);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .user-link:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    .user-unassigned {
        color: var(--gray-500);
        font-style: italic;
    }

    .creator-info {
        color: var(--gray-600);
        font-size: 0.813rem;
    }

    .expiry-info {
        color: var(--gray-600);
        font-size: 0.813rem;
    }

    .expiry-info.expired {
        color: #dc2626;
    }

    .actions-cell {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .btn-icon {
        padding: 0.5rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-delete {
        background-color: #fee2e2;
        color: #ef4444;
    }

    .btn-delete:hover {
        background-color: #fecaca;
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

    .alert-success {
        background-color: #d1fae5;
        border-left: 4px solid #10b981;
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
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Gestion des Codes Caisse</h1>
    <p class="page-description">Gérez tous les codes de rechargement disponibles</p>
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
            <h3>Total Codes</h3>
            <p>{{ $codes->total() }}</p>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <div class="stat-card green">
        <div class="stat-content">
            <h3>Codes Actifs</h3>
            <p>{{ $codes->whereNull('used_at')->where(function($q) {
                return $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->count() }}</p>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <div class="stat-card purple">
        <div class="stat-content">
            <h3>Valeur Totale</h3>
            <p>{{ number_format($codes->sum('montant_fcfa')) }} F</p>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <div class="stat-card orange">
        <div class="stat-content">
            <h3>Points à Générer</h3>
            <p>{{ number_format($codes->whereNull('used_at')->sum('points')) }}</p>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
        </div>
    </div>
</div>

<div class="table-card">
    <div class="table-header">
        <h2 class="table-title">Liste des Codes Caisse</h2>
        <a href="{{ route('admin.cash-codes.create') }}" class="btn btn-primary">
            <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Créer un Code
        </a>
    </div>

    @if($codes->count() > 0)
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Montant</th>
                        <th>Points</th>
                        <th>Destinataire</th>
                        <th>Créé par</th>
                        <th>Statut</th>
                        <th>Expiration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($codes as $code)
                        <tr>
                            <td>
                                <span class="code-display">{{ $code->code }}</span>
                            </td>
                            <td>
                                <span class="amount-display">{{ number_format($code->montant_fcfa) }} FCFA</span>
                            </td>
                            <td>
                                <span class="points-display">{{ $code->points }} pts</span>
                            </td>
                            <td>
                                @if($code->user)
                                    <a href="{{ route('admin.users.show', $code->user) }}" class="user-link">
                                        {{ $code->user->prenom }} {{ $code->user->nom }}
                                    </a>
                                @else
                                    <span class="user-unassigned">Tout le monde</span>
                                @endif
                            </td>
                            <td>
                                <span class="creator-info">
                                    {{ $code->createdBy->name ?? 'Admin' }}
                                </span>
                            </td>
                            <td>
                                @if($code->used_at)
                                    <span class="badge badge-used">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        Utilisé
                                    </span>
                                    @if($code->used_at)
                                    <div style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">
                                        {{ $code->used_at->format('d/m/Y H:i') }}
                                    </div>
                                    @endif
                                @elseif($code->expires_at && $code->expires_at->isPast())
                                    <span class="badge badge-expired">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Expiré
                                    </span>
                                @else
                                    <span class="badge badge-pending">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        En attente
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($code->expires_at)
                                    <span class="expiry-info {{ $code->expires_at->isPast() ? 'expired' : '' }}">
                                        {{ $code->expires_at->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="expiry-info">Jamais</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions-cell">
                                    @if(!$code->used_at)
                                        <form action="{{ route('admin.cash-codes.destroy', $code) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce code ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-delete">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($codes->hasPages())
            <div class="pagination-wrapper">
                {{ $codes->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3>Aucun code caisse trouvé</h3>
            <p>Commencez par créer votre premier code de rechargement</p>
        </div>
    @endif
</div>
@endsection