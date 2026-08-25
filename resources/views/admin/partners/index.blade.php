@extends('admin.layouts.app')

@section('title', 'Gestion Multi-Centres CFPAM & Partenaires')

@section('breadcrumb')
    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
    </svg>
    <span>Centres CFPAM & Partenaires</span>
@endsection

@section('content')
<style>
    .partner-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .partner-stat-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.25rem;
        border: 1px solid var(--gray-200);
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .partner-stat-label {
        font-size: 0.8rem;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .partner-stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-900);
    }
    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media(max-width: 768px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }
    }
    .input-field {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        font-size: 0.875rem;
        transition: border-color 0.2s;
    }
    .input-field:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
    }
</style>

<div class="page-header">
    <h1 class="page-title">Super User CFPAM — Multi-Centres & Partenaires</h1>
    <p class="page-description">Supervision des centres partenaires agréés, suivi des cohortes d'apprenants et consolidation financière.</p>
</div>

<!-- Cartes Statistiques Multi-Centres -->
<div class="partner-stats-grid">
    <div class="partner-stat-card">
        <div class="partner-stat-label">Total Centres</div>
        <div class="partner-stat-value">{{ $totalPartners }}</div>
        <div style="font-size: 0.8rem; color: var(--success-green); margin-top: 0.25rem;">
            {{ $activePartners }} centres actifs
        </div>
    </div>
    <div class="partner-stat-card">
        <div class="partner-stat-label">Apprenants Partenaires</div>
        <div class="partner-stat-value">{{ $totalLearners }}</div>
        <div style="font-size: 0.8rem; color: var(--primary-blue); margin-top: 0.25rem;">
            Cohortes multi-centres
        </div>
    </div>
    <div class="partner-stat-card">
        <div class="partner-stat-label">Tranches Encaissées</div>
        <div class="partner-stat-value" style="color: var(--success-green);">
            {{ number_format($totalCollected, 0, ',', ' ') }} F
        </div>
        <div style="font-size: 0.8rem; color: var(--gray-600); margin-top: 0.25rem;">
            Recouvrement validé
        </div>
    </div>
    <div class="partner-stat-card">
        <div class="partner-stat-label">Tranches En Retard</div>
        <div class="partner-stat-value" style="color: var(--danger);">
            {{ number_format($totalLate, 0, ',', ' ') }} F
        </div>
        <div style="font-size: 0.8rem; color: var(--gray-600); margin-top: 0.25rem;">
            Alertes d'échéance
        </div>
    </div>
</div>

<div class="content-grid" style="grid-template-columns: 1fr 340px;">
    <!-- Liste des Centres Partenaires -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Centres CFPAM & Partenaires Enregistrés</h2>
            <span class="badge badge-info">{{ $partners->total() }} centre(s)</span>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Centre / Nom</th>
                        <th>Contact</th>
                        <th>Apprenants</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partners as $partner)
                    <tr>
                        <td>
                            <strong style="color: var(--primary-blue); letter-spacing: 0.5px;">{{ $partner->code_partenaire ?: 'N/A' }}</strong>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--gray-900);">{{ $partner->nom }}</div>
                            <div style="font-size: 0.75rem; color: var(--gray-500);">Inscrit le {{ $partner->created_at ? $partner->created_at->format('d/m/Y') : '-' }}</div>
                        </td>
                        <td>
                            <div>{{ $partner->email }}</div>
                            @if($partner->telephone)
                                <div style="font-size: 0.75rem; color: var(--gray-600);">{{ $partner->telephone }}</div>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                <span class="badge badge-info">{{ $partner->learners_count }} total</span>
                                @if($partner->active_learners_count > 0)
                                    <span class="badge badge-success">{{ $partner->active_learners_count }} actifs</span>
                                @endif
                                @if($partner->failed_learners_count > 0)
                                    <span class="badge badge-warning" style="background:#fee2e2;color:#991b1b;">{{ $partner->failed_learners_count }} retard</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($partner->active)
                                <span class="badge badge-success">Actif</span>
                            @else
                                <span class="badge badge-warning" style="background:#fee2e2;color:#991b1b;">Inactif</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.partners.toggle', $partner) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn {{ $partner->active ? 'btn-danger' : 'btn-success' }}" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;">
                                    {{ $partner->active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--gray-600); padding: 2rem;">
                            Aucun centre partenaire enregistré pour le moment.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($partners->hasPages())
            <div style="padding-top: 1rem;">
                {{ $partners->links() }}
            </div>
        @endif
    </div>

    <!-- Formulaire d'ajout d'un Centre CFPAM -->
    <div class="card" style="height: fit-content;">
        <div class="card-header">
            <h2 class="card-title" style="font-size: 1.1rem;">Nouveau Centre CFPAM</h2>
        </div>

        <form method="POST" action="{{ route('admin.partners.store') }}" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.35rem;">Nom du Centre / Partenaire *</label>
                <input name="nom" class="input-field" placeholder="Ex: CFPAM Douala Akwa" required>
            </div>

            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.35rem;">Code Partenaire</label>
                <input name="code_partenaire" class="input-field" placeholder="Ex: CFPAM-DLA (ou auto)" style="text-transform:uppercase">
                <small style="color: var(--gray-500); font-size: 0.75rem;">Généré automatiquement si vide</small>
            </div>

            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.35rem;">Email de Connexion *</label>
                <input name="email" type="email" class="input-field" placeholder="contact@cfpam-centre.com" required>
            </div>

            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.35rem;">Téléphone</label>
                <input name="telephone" class="input-field" placeholder="+237 6XX XX XX XX">
            </div>

            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--gray-700); margin-bottom: 0.35rem;">Mot de Passe Initial *</label>
                <input name="password" type="password" class="input-field" placeholder="Minimum 8 caractères" required>
            </div>

            <button type="submit" class="btn btn-primary" style="justify-content: center; margin-top: 0.5rem;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Créer le Centre Partenaire</span>
            </button>
        </form>
    </div>
</div>
@endsection