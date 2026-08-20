@extends('admin.layouts.app')

@section('title', 'Détails du Pack')

@push('styles')
<style>
    .detail-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .detail-header {
        padding: 2rem;
        border-bottom: 2px solid var(--gray-100);
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--success-green) 100%);
        color: var(--white);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-content h1 {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-content p {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-active {
        background: rgba(255, 255, 255, 0.2);
    }

    .badge-inactive {
        background: rgba(239, 68, 68, 0.2);
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-action {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
    }

    .btn-edit {
        background: var(--white);
        color: var(--primary-blue);
    }

    .btn-edit:hover {
        background: #eff6ff;
        transform: translateY(-1px);
    }

    .btn-add-module {
        background: linear-gradient(135deg, var(--success-green) 0%, var(--primary-blue) 100%);
        color: var(--white);
    }

    .btn-add-module:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
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

    .detail-body {
        padding: 2rem;
    }

    .section {
        margin-bottom: 2rem;
    }

    .section:last-child {
        margin-bottom: 0;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .section-header h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .section-header svg {
        width: 24px;
        height: 24px;
        color: var(--primary-blue);
    }

    .description-text {
        font-size: 0.875rem;
        color: var(--gray-700);
        line-height: 1.6;
        margin: 0;
    }

    .modules-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .module-card {
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .module-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .module-header {
        padding: 1rem 1.5rem;
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .module-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .module-type {
        background: var(--primary-blue);
        color: var(--white);
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
    }

    .module-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .module-status {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
    }

    .module-status.inactive {
        background: #ef4444;
    }

    .module-body {
        padding: 1.5rem;
    }

    .module-description {
        font-size: 0.875rem;
        color: var(--gray-600);
        margin-bottom: 1rem;
    }

    .chapters-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .chapter-item {
        background: var(--gray-50);
        padding: 1rem;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chapter-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .chapter-info svg {
        width: 16px;
        height: 16px;
        color: var(--success-green);
    }

    .chapter-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .chapter-stats {
        display: flex;
        gap: 1rem;
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .module-actions {
        display: flex;
        gap: 0.5rem;
        margin-left: 1rem;
    }

    .btn-icon {
        padding: 0.5rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-icon:hover {
        transform: translateY(-1px);
    }

    .btn-edit-module {
        background: var(--success-green);
        color: var(--white);
    }

    .btn-delete-module {
        background: #ef4444;
        color: var(--white);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--gray-600);
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        margin-bottom: 1rem;
        opacity: 0.3;
        color: var(--gray-400);
    }

    .empty-state h3 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        font-size: 0.875rem;
        color: var(--gray-600);
    }

    .sidebar-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .info-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .info-card h3 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-card h3 svg {
        width: 20px;
        height: 20px;
    }

    .price-display {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--success-green) 100%);
        color: var(--white);
        padding: 2rem;
        border-radius: 8px;
        text-align: center;
    }

    .price-amount {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .price-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-top: 0.5rem;
    }

    .list-items {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .list-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: var(--gray-50);
        border-radius: 8px;
    }

    .list-item svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .list-item.blue svg {
        color: var(--primary-blue);
    }

    .list-item.green svg {
        color: var(--success-green);
    }

    .list-item.purple svg {
        color: #8b5cf6;
    }

    .list-text {
        font-size: 0.875rem;
        color: var(--gray-700);
        margin: 0;
    }

    .debouches-list {
        padding-left: 1.5rem;
        margin: 0;
    }

    .debouches-list li {
        font-size: 0.875rem;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .debouches-list li svg {
        width: 16px;
        height: 16px;
        color: #8b5cf6;
        margin-top: 0.125rem;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .detail-header {
            flex-direction: column;
            align-items: stretch;
        }

        .header-actions {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .module-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .module-actions {
            margin-left: 0;
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Détails du Pack</h1>
    <p class="page-description">Informations complètes du pack "{{ $pack->nom }}"</p>
</div>

@if(session('success'))
<div class="alert-success" style="animation: slideIn 0.3s ease-out;">
    <svg fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- En-tête du pack -->
<div class="detail-card">
    <div class="detail-header">
        <div class="header-content">
            <h1>
                {{ $pack->nom }}
                @if($pack->active)
                <span class="badge-status badge-active">
                    <svg fill="currentColor" viewBox="0 0 20 20" style="width: 16px; height: 16px;">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Actif
                </span>
                @else
                <span class="badge-status badge-inactive">
                    <svg fill="currentColor" viewBox="0 0 20 20" style="width: 16px; height: 16px;">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Inactif
                </span>
                @endif
            </h1>
            <p>{{ $pack->category->nom }} • {{ $pack->niveau_requis }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('admin.packs.edit', $pack) }}" class="btn-action btn-edit">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
            <a href="{{ route('admin.modules.create', $pack) }}" class="btn-action btn-add-module">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajouter Module
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="detail-body">
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-content">
                    <h3>Modules</h3>
                    <p>{{ $pack->modules->count() }}</p>
                </div>
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>

            <div class="stat-card green">
                <div class="stat-content">
                    <h3>Chapitres</h3>
                    <p>{{ $pack->modules->sum(fn($m) => $m->chapters->count()) }}</p>
                </div>
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>

            <div class="stat-card purple">
                <div class="stat-content">
                    <h3>Leçons</h3>
                    <p>{{ $pack->modules->sum(fn($m) => $m->chapters->sum(fn($c) => $c->lessons->count())) }}</p>
                </div>
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="stat-content">
                    <h3>Inscrits</h3>
                    <p>{{ $pack->userPacks->count() }}</p>
                </div>
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Contenu principal -->
            <div>
                <!-- Description -->
                <div class="section">
                    <div class="section-header">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2>Description</h2>
                    </div>
                    <p class="description-text">{{ $pack->description }}</p>
                </div>

                <!-- Modules -->
                <div class="section">
                    <div class="section-header">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h2>Modules ({{ $pack->modules->count() }})</h2>
                        <a href="{{ route('admin.modules.create', $pack) }}" style="margin-left: auto; font-size: 0.875rem; color: var(--primary-blue); text-decoration: none;">
                            + Ajouter
                        </a>
                    </div>

                    @if($pack->modules->count() > 0)
                    <div class="modules-list">
                        @foreach($pack->modules as $module)
                        <div class="module-card">
                            <div class="module-header">
                                <div class="module-title">
                                    <span class="module-type">{{ $module->type }}</span>
                                    <h3 class="module-name">{{ $module->nom }}</h3>
                                    <div class="module-status {{ $module->active ? '' : 'inactive' }}"></div>
                                </div>
                                <div class="module-actions">
                                    <a href="{{ route('admin.modules.edit', $module) }}" class="btn-icon btn-edit-module">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.modules.destroy', $module) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-delete-module" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce module ?');">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="module-body">
                                @if($module->description)
                                <p class="module-description">{{ $module->description }}</p>
                                @endif

                                @if($module->chapters->count() > 0)
                                <div class="chapters-list">
                                    @foreach($module->chapters as $chapter)
                                    <div class="chapter-item">
                                        <div class="chapter-info">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <h4 class="chapter-name">{{ $chapter->nom }}</h4>
                                        </div>
                                        <div class="chapter-stats">
                                            <span>{{ $chapter->lessons->count() }} leçons</span>
                                            @if($chapter->quiz)
                                            <span>• {{ $chapter->quiz->questions->count() }} questions</span>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p style="font-size: 0.875rem; color: var(--gray-500); text-align: center; padding: 1rem;">
                                    Aucun chapitre
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3>Aucun module disponible</h3>
                        <p>Commencez par ajouter un module</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar-grid">
                <!-- Prix -->
                <div class="info-card">
                    <h3>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Prix
                    </h3>
                    <div class="price-display">
                        <div class="price-amount">{{ $pack->prix_points }}</div>
                        <div class="price-label">Points</div>
                    </div>
                </div>

                
               

                <!-- Débouchés -->
                @if($pack->debouches && count($pack->debouches) > 0)
                <div class="info-card">
                    <h3>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Débouchés
                    </h3>
                    <ul class="debouches-list">
                        @foreach($pack->debouches as $debouche)
                        <li>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            {{ $debouche }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Profils -->
                @if($pack->profiles->count() > 0)
                <div class="info-card">
                    <h3>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profils Associés
                    </h3>
                    <div class="list-items">
                        @foreach($pack->profiles as $profile)
                        <div class="list-item purple">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="list-text">{{ $profile->nom }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
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
</style>
@endsection