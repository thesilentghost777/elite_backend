@extends('admin.layouts.app')

@section('title', 'Détails de la Catégorie')

@push('styles')
<style>
    .details-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .details-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .category-header {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--success-green) 100%);
        color: var(--white);
        padding: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .category-header h2 {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0;
    }

    .header-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        background-color: rgba(255, 255, 255, 0.25);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        padding: 2rem;
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .stat-box {
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--success-green) 100%);
        color: var(--white);
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.813rem;
        opacity: 0.95;
    }

    .info-section {
        padding: 2rem;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--gray-100);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .info-item {
        background: var(--gray-50);
        padding: 1.25rem;
        border-radius: 8px;
        border-left: 4px solid var(--primary-blue);
    }

    .info-label {
        font-size: 0.75rem;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .info-value {
        font-size: 0.938rem;
        color: var(--gray-900);
        font-weight: 500;
    }

    .info-value code {
        background: var(--white);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.813rem;
        border: 1px solid var(--gray-200);
    }

    .color-display {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .color-preview-large {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        border: 2px solid var(--gray-200);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .image-preview-container {
        margin-top: 0.75rem;
    }

    .image-preview {
        max-width: 100%;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .packs-list {
        display: grid;
        gap: 1rem;
        margin-top: 1rem;
    }

    .pack-card {
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        padding: 1.25rem;
        transition: all 0.2s ease;
    }

    .pack-card:hover {
        border-color: var(--primary-blue);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        transform: translateY(-2px);
    }

    .pack-card h3 {
        color: var(--gray-900);
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .pack-card p {
        color: var(--gray-600);
        font-size: 0.875rem;
        margin: 0;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--gray-600);
        background: var(--gray-50);
        border-radius: 8px;
    }

    .empty-state p {
        margin: 0;
        font-size: 0.875rem;
    }

    .actions-bar {
        display: flex;
        gap: 1rem;
        padding: 1.5rem 2rem;
        background: var(--gray-50);
        border-top: 2px solid var(--gray-200);
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .category-header {
            padding: 1.5rem;
        }

        .category-header h2 {
            font-size: 1.375rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            padding: 1.5rem;
        }

        .info-section {
            padding: 1.5rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .actions-bar {
            padding: 1.5rem;
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="details-container">
    <div class="page-header">
        <h1 class="page-title">Détails de la Catégorie</h1>
        <p class="page-description">Consultez toutes les informations de la catégorie</p>
    </div>

    <div class="details-card">
        <div class="category-header">
            <h2>{{ $category->nom }}</h2>
            <span class="header-badge">
                {{ $category->active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ $category->packs->count() }}</div>
                <div class="stat-label">Packs associés</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ $category->ordre }}</div>
                <div class="stat-label">Position d'affichage</div>
            </div>
        </div>

        <div class="info-section">
            <h3 class="section-title">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Informations Générales
            </h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nom</div>
                    <div class="info-value">{{ $category->nom }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Slug (URL)</div>
                    <div class="info-value">
                        <code>{{ $category->slug }}</code>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Ordre</div>
                    <div class="info-value">{{ $category->ordre }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Couleur</div>
                    <div class="info-value">
                        @if($category->couleur)
                            <div class="color-display">
                                <span class="color-preview-large" style="background-color: {{ $category->couleur }}"></span>
                                <code>{{ $category->couleur }}</code>
                            </div>
                        @else
                            <span style="color: var(--gray-400);">Non définie</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($category->description)
            <div class="info-section">
                <h3 class="section-title">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    Description
                </h3>
                <div class="info-item">
                    <div class="info-value">{{ $category->description }}</div>
                </div>
            </div>
        @endif

        @if($category->image_url)
            <div class="info-section">
                <h3 class="section-title">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Image
                </h3>
                <div class="info-item">
                    <div class="info-value">
                        <a href="{{ $category->image_url }}" target="_blank" style="color: var(--primary-blue); text-decoration: underline;">{{ $category->image_url }}</a>
                        <div class="image-preview-container">
                            <img src="{{ $category->image_url }}" alt="{{ $category->nom }}" class="image-preview" onerror="this.style.display='none'">
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="info-section">
            <h3 class="section-title">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Dates
            </h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Créée le</div>
                    <div class="info-value">{{ $category->created_at->format('d/m/Y à H:i') }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Modifiée le</div>
                    <div class="info-value">{{ $category->updated_at->format('d/m/Y à H:i') }}</div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3 class="section-title">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Packs Associés ({{ $category->packs->count() }})
            </h3>

            @if($category->packs->count() > 0)
                <div class="packs-list">
                    @foreach($category->packs as $pack)
                        <div class="pack-card">
                            <h3>{{ $pack->nom ?? 'Pack #' . $pack->id }}</h3>
                            <p>Ordre: {{ $pack->ordre }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>Aucun pack associé à cette catégorie</p>
                </div>
            @endif
        </div>

        <div class="actions-bar">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary" style="flex: 1;">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Modifier
            </a>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: inline; flex: 1;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="width: 100%;">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
