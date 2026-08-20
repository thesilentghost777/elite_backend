@extends('admin.layouts.app')

@section('title', 'Gestion des transactions')

@push('styles')
<style>
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

    .stat-card.orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: var(--white);
    }

    .stat-card.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
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

    .stat-subtitle {
        font-size: 0.813rem;
        opacity: 0.9;
        margin-top: 0.25rem;
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

    .filters-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        align-items: end;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        transition: all 0.2s ease;
        background: var(--white);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .btn-filter {
        padding: 0.75rem 1.5rem;
        background: var(--primary-blue);
        color: var(--white);
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-filter:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }

    .btn-reset {
        padding: 0.75rem 1.5rem;
        background: var(--gray-200);
        color: var(--gray-700);
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-reset:hover {
        background: var(--gray-300);
        transform: translateY(-1px);
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

    .reference-code {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.813rem;
        color: var(--gray-700);
        background: var(--gray-100);
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        letter-spacing: 0.05em;
    }

    .user-link {
        color: var(--primary-blue);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .user-link:hover {
        color: #2563eb;
        text-decoration: underline;
    }

    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
        text-align: center;
    }

    .badge-depot {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-achat {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .badge-parrainage {
        background-color: #f3e8ff;
        color: #6b21a8;
    }

    .badge-transfert {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-code {
        background-color: #fce7f3;
        color: #9f1239;
    }

    .badge-bourse {
        background-color: #dcfce7;
        color: #166534;
    }

    .amount-cell {
        font-weight: 700;
        color: var(--success-green);
    }

    .amount-negative {
        color: #ef4444;
    }

    .amount-fcfa {
        font-size: 0.813rem;
        color: var(--gray-600);
        margin-top: 0.25rem;
    }

    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .status-complete {
        background-color: var(--light-green);
        color: var(--dark-green);
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-failed {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .status-cancelled {
        background-color: var(--gray-200);
        color: var(--gray-700);
    }

    .status-badge svg {
        width: 14px;
        height: 14px;
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

    .export-buttons {
        display: flex;
        gap: 0.75rem;
    }

    .btn-export {
        padding: 0.75rem 1.5rem;
        background: var(--gray-200);
        color: var(--gray-700);
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-export:hover {
        background: var(--gray-300);
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .table-header {
            flex-direction: column;
            align-items: stretch;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .data-table th,
        .data-table td {
            padding: 0.75rem 1rem;
            font-size: 0.813rem;
        }

        .export-buttons {
            width: 100%;
            flex-direction: column;
        }

        .btn-export {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <span>Transactions</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Gestion des transactions</h1>
    <p class="page-description">Historique complet des transactions financières</p>
</div>

<!-- Statistiques -->
<div class="stats-grid">
    <div class="stat-card green">
        <div class="stat-content">
            <h3>Total Dépôts</h3>
            <p>{{ number_format($stats['total_depots'] ?? 0, 0, ',', ' ') }}</p>
            <div class="stat-subtitle">FCFA</div>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
            </svg>
        </div>
    </div>

    <div class="stat-card blue">
        <div class="stat-content">
            <h3>Total Achats</h3>
            <p>{{ number_format($stats['total_achats'] ?? 0, 0, ',', ' ') }}</p>
            <div class="stat-subtitle">Points</div>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
    </div>

    <div class="stat-card orange">
        <div class="stat-content">
            <h3>Total Transferts</h3>
            <p>{{ number_format($stats['total_transferts'] ?? 0, 0, ',', ' ') }}</p>
            <div class="stat-subtitle">Points</div>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </div>
    </div>

    <div class="stat-card purple">
        <div class="stat-content">
            <h3>Parrainages</h3>
            <p>{{ number_format($stats['total_parrainages'] ?? 0, 0, ',', ' ') }}</p>
            <div class="stat-subtitle">Récompenses</div>
        </div>
        <div class="stat-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filters-card">
    <form method="GET" action="{{ route('admin.transactions.index') }}">
        <div class="filters-grid">
            <div class="form-group">
                <label class="form-label">Type de transaction</label>
                <select name="type" class="form-control">
                    <option value="">Tous les types</option>
                    <option value="depot" {{ request('type') == 'depot' ? 'selected' : '' }}>Dépôt</option>
                    <option value="achat_pack" {{ request('type') == 'achat_pack' ? 'selected' : '' }}>Achat pack</option>
                    <option value="parrainage" {{ request('type') == 'parrainage' ? 'selected' : '' }}>Parrainage</option>
                    <option value="transfert_envoi" {{ request('type') == 'transfert_envoi' ? 'selected' : '' }}>Transfert envoi</option>
                    <option value="transfert_recu" {{ request('type') == 'transfert_recu' ? 'selected' : '' }}>Transfert reçu</option>
                    <option value="code_caisse" {{ request('type') == 'code_caisse' ? 'selected' : '' }}>Code caisse</option>
                    <option value="bourse" {{ request('type') == 'bourse' ? 'selected' : '' }}>Bourse</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="complete" {{ request('statut') == 'complete' ? 'selected' : '' }}>Complété</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="echoue" {{ request('statut') == 'echoue' ? 'selected' : '' }}>Échoué</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Date début</label>
                <input 
                    type="date" 
                    name="date_from" 
                    class="form-control" 
                    value="{{ request('date_from') }}"
                >
            </div>

            <div class="form-group">
                <label class="form-label">Date fin</label>
                <input 
                    type="date" 
                    name="date_to" 
                    class="form-control" 
                    value="{{ request('date_to') }}"
                >
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filtrer
                </button>
                <a href="{{ route('admin.transactions.index') }}" class="btn-reset">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Réinitialiser
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Table des transactions -->
<div class="table-card">
    <div class="table-header">
        <h2 class="table-title">Liste des transactions ({{ $transactions->total() }})</h2>
        <div class="export-buttons">
            <button class="btn-export">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exporter CSV
            </button>
        </div>
    </div>

    @if($transactions->count() > 0)
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Utilisateur</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>
                                <span class="reference-code">{{ $transaction->reference }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.users.show', $transaction->user) }}" class="user-link">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ $transaction->user->prenom }} {{ $transaction->user->nom }}
                                </a>
                            </td>
                            <td>
                                @php
                                    $typeClass = match($transaction->type) {
                                        'depot' => 'badge-depot',
                                        'achat_pack' => 'badge-achat',
                                        'parrainage' => 'badge-parrainage',
                                        'transfert_envoi', 'transfert_recu' => 'badge-transfert',
                                        'code_caisse' => 'badge-code',
                                        'bourse' => 'badge-bourse',
                                        default => 'badge-depot'
                                    };
                                @endphp
                                <span class="badge {{ $typeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                </span>
                            </td>
                            <td>
                                <div class="amount-cell {{ $transaction->points < 0 ? 'amount-negative' : '' }}">
                                    {{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points, 0, ',', ' ') }} pts
                                </div>
                                @if($transaction->montant_fcfa)
                                    <div class="amount-fcfa">
                                        {{ number_format($transaction->montant_fcfa, 0, ',', ' ') }} FCFA
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="max-width: 250px;">
                                    {{ $transaction->description ?? '-' }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($transaction->statut) {
                                        'complete' => 'status-complete',
                                        'en_attente' => 'status-pending',
                                        'echoue' => 'status-failed',
                                        'annule' => 'status-cancelled',
                                        default => 'status-pending'
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    @if($transaction->statut == 'complete')
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($transaction->statut == 'en_attente')
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @elseif($transaction->statut == 'echoue')
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    {{ ucfirst(str_replace('_', ' ', $transaction->statut)) }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 500; color: var(--gray-900);">
                                    {{ $transaction->created_at->format('d/m/Y') }}
                                </div>
                                <div style="font-size: 0.813rem; color: var(--gray-600);">
                                    {{ $transaction->created_at->format('H:i') }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="pagination-wrapper">
                {{ $transactions->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <h3>Aucune transaction trouvée</h3>
            <p>Essayez d'ajuster vos filtres de recherche</p>
        </div>
    @endif
</div>
@endsection