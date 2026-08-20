@extends('admin.layouts.app')

@section('title', 'Détails du profil')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.profiles.index') }}">Profils métiers</a>
    <span>/</span>
    <span>{{ $profile->nom }}</span>
@endsection

@section('content')
<style>
    .profile-hero {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        border-radius: 16px;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
        position: relative;
        overflow: hidden;
    }

    .profile-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    }

    .profile-hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: start;
        gap: 2rem;
    }

    .profile-hero-info h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.75rem;
        line-height: 1.2;
    }

    .profile-hero-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 1rem;
        font-size: 0.875rem;
        opacity: 0.95;
    }

    .profile-hero-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .profile-hero-actions {
        display: flex;
        gap: 0.75rem;
        flex-shrink: 0;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-box {
        background-color: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .info-box-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gray-200);
    }

    .info-box-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background-color: var(--light-blue);
        color: var(--primary-blue);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .info-box-icon.green {
        background-color: var(--light-green);
        color: var(--success-green);
    }

    .info-box-icon.orange {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
    }

    .info-box-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--gray-200);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .info-value {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-900);
    }

    .debouches-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .debouche-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        background-color: var(--light-green);
        color: var(--dark-green);
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .roadmap-card {
        background-color: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 2px solid var(--gray-200);
        transition: all 0.2s;
    }

    .roadmap-card:hover {
        border-color: var(--primary-blue);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .roadmap-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .roadmap-niveau {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem;
        background-color: var(--light-blue);
        color: var(--primary-blue);
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .roadmap-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }

    .roadmap-steps {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .pack-card {
        background-color: var(--white);
        border-radius: 12px;
        padding: 1.25rem;
        border: 2px solid var(--gray-200);
        transition: all 0.2s;
    }

    .pack-card:hover {
        border-color: var(--success-green);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .pack-name {
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.5rem;
    }

    .pack-priority {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .users-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .user-mini-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background-color: var(--gray-50);
        border-radius: 8px;
        border: 1px solid var(--gray-200);
    }

    .user-mini-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-blue), var(--success-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        flex-shrink: 0;
    }

    .user-mini-info {
        flex: 1;
        min-width: 0;
    }

    .user-mini-name {
        font-weight: 500;
        color: var(--gray-900);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.875rem;
    }

    .user-mini-date {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    @media (max-width: 768px) {
        .profile-hero-content {
            flex-direction: column;
        }

        .profile-hero-actions {
            width: 100%;
        }

        .profile-hero-actions .btn {
            flex: 1;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .users-list {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header" style="margin-bottom: 2rem;">
    <a href="{{ route('admin.profiles.index') }}" class="btn btn-secondary">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Retour
    </a>
</div>

<!-- Hero Section -->
<div class="profile-hero">
    <div class="profile-hero-content">
        <div class="profile-hero-info">
            <h1>{{ $profile->nom }}</h1>
            <div class="profile-hero-meta">
                <div class="profile-hero-meta-item">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>{{ $profile->secteur }}</span>
                </div>
                <div class="profile-hero-meta-item">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                    </svg>
                    <span>Niveau minimum: {{ $profile->niveau_minimum }}</span>
                </div>
                @if($profile->is_cfpam)
                <div class="profile-hero-meta-item">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Profil CFPAM</span>
                </div>
                @endif
                @if(!$profile->active)
                <div class="profile-hero-meta-item" style="color: #fca5a5;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Inactif</span>
                </div>
                @endif
            </div>
        </div>

        <div class="profile-hero-actions">
            <a href="{{ route('admin.profiles.edit', $profile) }}" class="btn btn-success">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Modifier
            </a>
        </div>
    </div>
</div>

<!-- Description -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h2 class="card-title">Description</h2>
    </div>
    <p style="color: var(--gray-700); line-height: 1.8;">{{ $profile->description }}</p>
</div>

<!-- Informations détaillées -->
<div class="info-grid">
    <!-- Débouchés -->
    <div class="info-box">
        <div class="info-box-header">
            <div class="info-box-icon green">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="info-box-title">Débouchés</h3>
        </div>
        
        @if($profile->debouches && count($profile->debouches) > 0)
            <div class="debouches-list">
                @foreach($profile->debouches as $debouche)
                    <div class="debouche-badge">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $debouche }}
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--gray-600); font-size: 0.875rem;">Aucun débouché défini</p>
        @endif
    </div>

    <!-- Statistiques -->
    <div class="info-box">
        <div class="info-box-header">
            <div class="info-box-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="info-box-title">Statistiques</h3>
        </div>
        
        <div class="info-row">
            <span class="info-label">Roadmaps</span>
            <span class="info-value">{{ $profile->roadmaps->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Packs associés</span>
            <span class="info-value">{{ $profile->packs->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Utilisateurs</span>
            <span class="info-value">{{ $profile->userChoices->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Créé le</span>
            <span class="info-value">{{ $profile->created_at->format('d/m/Y') }}</span>
        </div>
    </div>
</div>

<!-- Roadmaps -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h2 class="card-title">Roadmaps de parcours</h2>
    </div>

    @forelse($profile->roadmaps as $roadmap)
        <div class="roadmap-card">
            <div class="roadmap-header">
                <div>
                    <span class="roadmap-niveau">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        </svg>
                        Niveau {{ $roadmap->niveau_depart }}
                    </span>
                    <h4 class="roadmap-title">{{ $roadmap->titre }}</h4>
                    <p style="color: var(--gray-600); font-size: 0.875rem;">{{ $roadmap->description }}</p>
                </div>
            </div>
            
            <div class="roadmap-steps">
                <div style="color: var(--gray-600); font-size: 0.875rem;">
                    <strong>{{ $roadmap->steps->count() }}</strong> étapes
                    @if($roadmap->duree_estimee_mois)
                        • <strong>{{ $roadmap->duree_estimee_mois }}</strong> mois estimés
                    @endif
                </div>
            </div>
        </div>
    @empty
        <p style="text-align: center; color: var(--gray-600); padding: 2rem;">Aucune roadmap définie</p>
    @endforelse
</div>

<!-- Packs associés -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h2 class="card-title">Packs de formation associés</h2>
    </div>

    @if($profile->packs->count() > 0)
        <div style="display: grid; gap: 1rem;">
            @foreach($profile->packs as $pack)
                <div class="pack-card">
                    <div class="pack-name">{{ $pack->nom }}</div>
                    <div class="pack-priority">
                        Priorité: {{ $pack->pivot->priorite }}
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p style="text-align: center; color: var(--gray-600); padding: 2rem;">Aucun pack associé</p>
    @endif
</div>

<!-- Utilisateurs qui ont choisi ce profil -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            Utilisateurs ayant choisi ce profil 
            <span style="font-weight: normal; color: var(--gray-600);">({{ $profile->userChoices->count() }})</span>
        </h2>
    </div>

    @if($profile->userChoices->count() > 0)
        <div class="users-list">
            @foreach($profile->userChoices->take(12) as $choice)
                <a href="{{ route('admin.users.show', $choice->user) }}" class="user-mini-card" style="text-decoration: none; color: inherit;">
                    <div class="user-mini-avatar">
                        {{ strtoupper(substr($choice->user->prenom, 0, 1)) }}{{ strtoupper(substr($choice->user->nom, 0, 1)) }}
                    </div>
                    <div class="user-mini-info">
                        <div class="user-mini-name">{{ $choice->user->prenom }} {{ $choice->user->nom }}</div>
                        <div class="user-mini-date">{{ $choice->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            @endforeach
        </div>
        
        @if($profile->userChoices->count() > 12)
            <div style="text-align: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--gray-200);">
                <a href="{{ route('admin.users.index', ['profile' => $profile->id]) }}" class="btn btn-secondary">
                    Voir tous les utilisateurs ({{ $profile->userChoices->count() }})
                </a>
            </div>
        @endif
    @else
        <p style="text-align: center; color: var(--gray-600); padding: 2rem;">Aucun utilisateur n'a encore choisi ce profil</p>
    @endif
</div>

@endsection