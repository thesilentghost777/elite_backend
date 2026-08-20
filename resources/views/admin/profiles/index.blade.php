@extends('admin.layouts.app')

@section('title', 'Profils métiers')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <span>Profils métiers</span>
@endsection

@section('content')
<style>
    .profiles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
    }

    .profile-card {
        background-color: var(--white);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
        border: 2px solid transparent;
        position: relative;
    }

    .profile-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        border-color: var(--primary-blue);
    }

    .profile-card.cfpam {
        border-color: var(--success-green);
    }

    .profile-card.cfpam::before {
        content: 'CFPAM';
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, var(--success-green), var(--dark-green));
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 1;
    }

    .profile-card.inactive {
        opacity: 0.6;
    }

    .profile-card.inactive::after {
        content: 'INACTIF';
        position: absolute;
        top: 1rem;
        left: 1rem;
        background-color: var(--gray-600);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        z-index: 1;
    }

    .profile-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }

    .profile-content {
        padding: 1.5rem;
    }

    .profile-header {
        margin-bottom: 1rem;
    }

    .profile-name {
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .profile-sector {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: var(--light-blue);
        color: var(--primary-blue);
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .profile-description {
        color: var(--gray-600);
        font-size: 0.875rem;
        line-height: 1.6;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .profile-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--gray-200);
    }

    .profile-stat {
        text-align: center;
    }

    .profile-stat-value {
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--primary-blue);
        margin-bottom: 0.25rem;
    }

    .profile-stat-label {
        font-size: 0.7rem;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .profile-actions {
        display: flex;
        gap: 0.5rem;
    }

    .profile-actions .btn {
        flex: 1;
        justify-content: center;
        padding: 0.625rem;
        font-size: 0.875rem;
    }

    .stats-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background-color: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .summary-icon.green {
        background: linear-gradient(135deg, var(--success-green), var(--dark-green));
    }

    .summary-content {
        flex: 1;
    }

    .summary-value {
        font-size: 1.75rem;
        font-weight: bold;
        color: var(--gray-900);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .summary-label {
        font-size: 0.75rem;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .filter-tab {
        padding: 0.625rem 1.25rem;
        background-color: var(--white);
        border: 2px solid var(--gray-300);
        border-radius: 8px;
        color: var(--gray-700);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.875rem;
    }

    .filter-tab:hover {
        border-color: var(--primary-blue);
        color: var(--primary-blue);
    }

    .filter-tab.active {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
    }

    @media (max-width: 768px) {
        .profiles-grid {
            grid-template-columns: 1fr;
        }

        .stats-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">Profils métiers</h1>
            <p class="page-description">Gestion des profils de carrière et parcours professionnels</p>
        </div>
        <a href="{{ route('admin.profiles.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Créer un profil
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="stats-summary">
    <div class="summary-card">
        <div class="summary-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $profiles->count() }}</div>
            <div class="summary-label">Total profils</div>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon green">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $profiles->where('active', true)->count() }}</div>
            <div class="summary-label">Actifs</div>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $profiles->where('is_cfpam', true)->count() }}</div>
            <div class="summary-label">CFPAM</div>
        </div>
    </div>

    <div class="summary-card">
        <div class="summary-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <div class="summary-content">
            <div class="summary-value">{{ $totalChoices ?? 0 }}</div>
            <div class="summary-label">Choix users</div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="filter-tabs">
    <button class="filter-tab active" onclick="filterProfiles('all')">Tous</button>
    <button class="filter-tab" onclick="filterProfiles('active')">Actifs</button>
    <button class="filter-tab" onclick="filterProfiles('cfpam')">CFPAM</button>
    <button class="filter-tab" onclick="filterProfiles('popular')">Populaires</button>
    <button class="filter-tab" onclick="filterProfiles('inactive')">Inactifs</button>
</div>

<!-- Grille des profils -->
@if($profiles->count() > 0)
<div class="profiles-grid">
    @foreach($profiles as $profile)
    <div class="profile-card {{ $profile->is_cfpam ? 'cfpam' : '' }} {{ !$profile->active ? 'inactive' : '' }}" 
         data-status="{{ $profile->active ? 'active' : 'inactive' }}"
         data-type="{{ $profile->is_cfpam ? 'cfpam' : 'popular' }}">
        
        <div class="profile-image">
            @if($profile->image_url)
                <img src="{{ $profile->image_url }}" alt="{{ $profile->nom }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            @endif
        </div>

        <div class="profile-content">
            <div class="profile-header">
                <h3 class="profile-name">{{ $profile->nom }}</h3>
                <span class="profile-sector">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    {{ $profile->secteur }}
                </span>
            </div>

            <p class="profile-description">{{ $profile->description }}</p>

            <div class="profile-stats">
                <div class="profile-stat">
                    <div class="profile-stat-value">{{ $profile->roadmaps_count ?? 0 }}</div>
                    <div class="profile-stat-label">Roadmaps</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-value">{{ $profile->packs_count ?? 0 }}</div>
                    <div class="profile-stat-label">Packs</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-value">{{ $profile->user_choices_count ?? 0 }}</div>
                    <div class="profile-stat-label">Choix</div>
                </div>
            </div>

            <div class="profile-actions">
                <a href="{{ route('admin.profiles.show', $profile) }}" class="btn btn-primary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    Voir
                </a>
                <a href="{{ route('admin.profiles.edit', $profile) }}" class="btn btn-success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card">
    <div class="empty-state">
        <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        <h3 style="margin-bottom: 0.5rem;">Aucun profil métier</h3>
        <p>Créez votre premier profil de carrière</p>
        <a href="{{ route('admin.profiles.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Créer un profil
        </a>
    </div>
</div>
@endif

@push('scripts')
<script>
    function filterProfiles(filter) {
        const cards = document.querySelectorAll('.profile-card');
        const tabs = document.querySelectorAll('.filter-tab');
        
        // Mettre à jour les onglets
        tabs.forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');
        
        // Filtrer les cartes
        cards.forEach(card => {
            const status = card.dataset.status;
            const type = card.dataset.type;
            
            if (filter === 'all') {
                card.style.display = 'block';
            } else if (filter === 'active' && status === 'active') {
                card.style.display = 'block';
            } else if (filter === 'inactive' && status === 'inactive') {
                card.style.display = 'block';
            } else if (filter === 'cfpam' && type === 'cfpam') {
                card.style.display = 'block';
            } else if (filter === 'popular' && type === 'popular') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endpush
@endsection