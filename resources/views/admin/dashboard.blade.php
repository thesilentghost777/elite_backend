@extends('admin.layouts.app')

@section('title', 'Tableau de bord')

@section('breadcrumb')
    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
    </svg>
    <span>Tableau de bord</span>
@endsection

@section('content')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 12px rgba(37, 99, 235, 0.3);
    }

    .stat-card.green {
        background: linear-gradient(135deg, var(--success-green), var(--dark-green));
        box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
    }

    .stat-card.green:hover {
        box-shadow: 0 8px 12px rgba(16, 185, 129, 0.3);
    }

    .stat-card.purple {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        box-shadow: 0 4px 6px rgba(139, 92, 246, 0.2);
    }

    .stat-card.purple:hover {
        box-shadow: 0 8px 12px rgba(139, 92, 246, 0.3);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .stat-change {
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        opacity: 0.9;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background-color: var(--gray-50);
    }

    th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--gray-700);
        border-bottom: 2px solid var(--gray-200);
    }

    td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.875rem;
    }

    tbody tr:hover {
        background-color: var(--gray-50);
    }

    .badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        display: inline-block;
    }

    .badge-success {
        background-color: var(--light-green);
        color: var(--dark-green);
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-info {
        background-color: var(--light-blue);
        color: var(--secondary-blue);
    }

    .activity-item {
        display: flex;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid var(--gray-200);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .activity-icon.blue {
        background-color: var(--light-blue);
        color: var(--primary-blue);
    }

    .activity-icon.green {
        background-color: var(--light-green);
        color: var(--success-green);
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 500;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
    }

    .activity-time {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .activity-amount {
        font-weight: 600;
        color: var(--success-green);
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-value {
            font-size: 1.5rem;
        }
    }
</style>

<div class="page-header">
    <h1 class="page-title">Tableau de bord</h1>
    <p class="page-description">Vue d'ensemble de la plateforme Elite Formation</p>
</div>

<!-- Statistiques principales -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Total Utilisateurs</div>
                <div class="stat-value">{{ number_format($stats['total_users'], 0, ',', ' ') }}</div>
                <div class="stat-change">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    <span>+{{ $stats['new_users_week'] }} cette semaine</span>
                </div>
            </div>
            <div class="stat-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="stat-card green">
        <div class="stat-header">
            <div>
                <div class="stat-label">Revenus Totaux (FCFA)</div>
                <div class="stat-value">{{ number_format($stats['total_revenue'], 0, ',', ' ') }}</div>
                <div class="stat-change">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    <span>{{ number_format($stats['revenue_today'], 0, ',', ' ') }} FCFA aujourd'hui</span>
                </div>
            </div>
            <div class="stat-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="stat-card purple">
        <div class="stat-header">
            <div>
                <div class="stat-label">Packs Actifs</div>
                <div class="stat-value">{{ number_format($stats['active_packs'], 0, ',', ' ') }}</div>
                <div class="stat-change">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    <span>{{ $stats['total_packs_sold'] }} vendus au total</span>
                </div>
            </div>
            <div class="stat-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-header">
            <div>
                <div class="stat-label">Profils Carrières</div>
                <div class="stat-value">{{ number_format($stats['total_profiles'], 0, ',', ' ') }}</div>
                <div class="stat-change">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>{{ $stats['total_packs'] }} packs disponibles</span>
                </div>
            </div>
            <div class="stat-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="content-grid">
    <!-- Dernières inscriptions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Dernières inscriptions</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Voir tout</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Téléphone</th>
                        <th>Ville</th>
                        <th>Diplôme</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $user)
                    <tr>
                        <td>
                            <div style="font-weight: 500;">{{ $user->prenom }} {{ $user->nom }}</div>
                        </td>
                        <td>{{ $user->telephone }}</td>
                        <td>{{ $user->ville ?? 'N/A' }}</td>
                        <td>{{ $user->dernier_diplome ?? 'N/A' }}</td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--gray-600); padding: 2rem;">
                            Aucune inscription récente
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transactions récentes -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Transactions récentes</h2>
        </div>

        <div>
            @forelse($recentTransactions as $transaction)
            <div class="activity-item">
                <div class="activity-icon {{ $transaction->type === 'depot' ? 'green' : 'blue' }}">
                    @if($transaction->type === 'depot')
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    @else
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    @endif
                </div>
                <div class="activity-content">
                    <div class="activity-title">
                        {{ $transaction->user->prenom ?? 'Utilisateur' }} {{ $transaction->user->nom ?? '' }}
                        @if($transaction->type === 'depot')
                            - Dépôt
                        @else
                            - Retrait
                        @endif
                    </div>
                    <div class="activity-time">
                        {{ $transaction->created_at->diffForHumans() }}
                        <span class="activity-amount">{{ number_format($transaction->montant_fcfa, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
                <div>
                    @if($transaction->statut === 'complete')
                        <span class="badge badge-success">Complété</span>
                    @elseif($transaction->statut === 'en_attente')
                        <span class="badge badge-warning">En attente</span>
                    @else
                        <span class="badge badge-info">{{ ucfirst($transaction->statut) }}</span>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 2rem; color: var(--gray-600);">
                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin: 0 auto 1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p>Aucune transaction récente</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Packs populaires -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Packs les plus populaires</h2>
        <a href="{{ route('admin.packs.index') }}" class="btn btn-secondary">Voir tous les packs</a>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nom du pack</th>
                    <th>Prix (FCFA)</th>
                    <th>Durée</th>
                    <th>Points</th>
                    <th>Ventes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($popularPacks as $pack)
                <tr>
                    <td>
                        <div style="font-weight: 500;">{{ $pack->nom }}</div>
                    </td>
                    <td>{{ number_format($pack->prix_fcfa, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $pack->duree_mois }} mois</td>
                    <td>
                        <span class="badge badge-info">{{ number_format($pack->points_inclus, 0, ',', ' ') }} pts</span>
                    </td>
                    <td>
                        <span class="badge badge-success">{{ $pack->user_packs_count }} ventes</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--gray-600); padding: 2rem;">
                        Aucun pack disponible
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection