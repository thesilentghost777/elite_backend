@extends('admin.layouts.app')

@section('title', 'Détails utilisateur')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.users.index') }}">Utilisateurs</a>
    <span>/</span>
    <span>{{ $user->prenom }} {{ $user->nom }}</span>
@endsection

@section('content')
<style>
    .profile-header {
        background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
        border-radius: 12px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
    }

    .profile-content {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .profile-avatar-large {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: bold;
        border: 4px solid rgba(255, 255, 255, 0.3);
    }

    .profile-info {
        flex: 1;
    }

    .profile-name {
        font-size: 1.875rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .profile-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-top: 1rem;
        font-size: 0.875rem;
    }

    .profile-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        opacity: 0.9;
    }

    .profile-actions {
        display: flex;
        gap: 0.75rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-card {
        background-color: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .info-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gray-200);
    }

    .info-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background-color: var(--light-blue);
        color: var(--primary-blue);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .info-card-icon.green {
        background-color: var(--light-green);
        color: var(--success-green);
    }

    .info-card-title {
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

    .section-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--gray-200);
    }

    .tab-button {
        padding: 0.75rem 1.5rem;
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        color: var(--gray-600);
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: -2px;
    }

    .tab-button.active {
        color: var(--primary-blue);
        border-bottom-color: var(--primary-blue);
    }

    .tab-button:hover:not(.active) {
        color: var(--gray-900);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .transaction-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }

    .transaction-item:hover {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .transaction-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .transaction-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .transaction-icon.blue {
        background-color: var(--light-blue);
        color: var(--primary-blue);
    }

    .transaction-icon.green {
        background-color: var(--light-green);
        color: var(--success-green);
    }

    .transaction-details h4 {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
    }

    .transaction-details p {
        font-size: 0.75rem;
        color: var(--gray-600);
    }

    .transaction-amount {
        text-align: right;
    }

    .amount {
        font-size: 1.125rem;
        font-weight: bold;
    }

    .amount.positive {
        color: var(--success-green);
    }

    .amount.negative {
        color: var(--danger);
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal {
        background-color: var(--white);
        border-radius: 12px;
        padding: 2rem;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        margin-bottom: 1.5rem;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--gray-900);
    }

    .modal-footer {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
        margin-top: 1.5rem;
    }

    @media (max-width: 768px) {
        .profile-content {
            flex-direction: column;
            text-align: center;
        }

        .profile-meta {
            justify-content: center;
        }

        .profile-actions {
            width: 100%;
            flex-direction: column;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .section-tabs {
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }
    }
</style>

<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title">Profil utilisateur</h1>
            <p class="page-description">Informations complètes et historique</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour
        </a>
    </div>
</div>

<!-- Header du profil -->
<div class="profile-header">
    <div class="profile-content">
        <div class="profile-avatar-large">
            {{ strtoupper(substr($user->prenom, 0, 1)) }}{{ strtoupper(substr($user->nom, 0, 1)) }}
        </div>
        
        <div class="profile-info">
            <h2 class="profile-name">{{ $user->prenom }} {{ $user->nom }}</h2>
            
            <div class="profile-meta">
                <div class="profile-meta-item">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    {{ $user->telephone }}
                </div>
                
                @if($user->email)
                <div class="profile-meta-item">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    {{ $user->email }}
                </div>
                @endif
                
                <div class="profile-meta-item">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Inscrit le {{ $user->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <div class="profile-actions">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-success">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Modifier
            </a>
            <button onclick="openModal('addPointsModal')" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Ajouter points
            </button>
        </div>
    </div>
</div>

<!-- Cartes d'information -->
<div class="info-grid">
    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h3 class="info-card-title">Informations personnelles</h3>
        </div>
        
        <div class="info-row">
            <span class="info-label">Ville</span>
            <span class="info-value">{{ $user->ville }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dernier diplôme</span>
            <span class="info-value">{{ $user->dernier_diplome }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Code parrainage</span>
            <span class="info-value" style="font-family: monospace; color: var(--primary-blue);">{{ $user->referral_code }}</span>
        </div>
        @if($user->referred_by)
        <div class="info-row">
            <span class="info-label">Parrainé par</span>
            <span class="info-value">{{ $user->referred_by }}</span>
        </div>
        @endif
    </div>

    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-icon green">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
            </div>
            <h3 class="info-card-title">Points & Activité</h3>
        </div>
        
        <div class="info-row">
            <span class="info-label">Solde points</span>
            <span class="info-value" style="color: #f59e0b; font-weight: bold;">{{ number_format($user->solde_points, 0, ',', ' ') }} pts</span>
        </div>
        <div class="info-row">
            <span class="info-label">Packs actifs</span>
            <span class="info-value">{{ $user->packs->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Filleuls</span>
            <span class="info-value">{{ $user->filleuls->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Profil choisi</span>
            <span class="info-value">
                @if($user->profileChoice)
                    {{ $user->profileChoice->profile->nom }}
                @else
                    <span style="color: var(--gray-500);">Aucun</span>
                @endif
            </span>
        </div>
    </div>

    <div class="info-card">
        <div class="info-card-header">
            <div class="info-card-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="info-card-title">Progression</h3>
        </div>
        
        <div class="info-row">
            <span class="info-label">Formulaire</span>
            <span class="info-value">
                @if($user->correspondence_completed)
                    <span class="badge badge-success">✓ Complété</span>
                @else
                    <span class="badge badge-warning">En attente</span>
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Choix profil</span>
            <span class="info-value">
                @if($user->profile_chosen)
                    <span class="badge badge-success">✓ Fait</span>
                @else
                    <span class="badge badge-warning">En attente</span>
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Mode parcours</span>
            <span class="info-value">{{ $user->parcours_mode ?? 'Non défini' }}</span>
        </div>
    </div>
</div>

<!-- Onglets -->
<div class="section-tabs">
    <button class="tab-button active" onclick="showTab('transactions')">Transactions</button>
    <button class="tab-button" onclick="showTab('packs')">Packs</button>
    <button class="tab-button" onclick="showTab('filleuls')">Filleuls</button>
</div>

<!-- Contenu des onglets -->
<div class="card">
    <!-- Onglet Transactions -->
    <div id="transactions" class="tab-content active">
        <div class="card-header">
            <h3 class="card-title">Historique des transactions</h3>
        </div>
        
        @forelse($user->transactions->take(10) as $transaction)
        <div class="transaction-item">
            <div class="transaction-info">
                <div class="transaction-icon {{ in_array($transaction->type, ['depot', 'parrainage', 'code_caisse', 'bourse']) ? 'green' : 'blue' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if(in_array($transaction->type, ['depot', 'parrainage', 'code_caisse', 'bourse']))
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        @endif
                    </svg>
                </div>
                <div class="transaction-details">
                    <h4>{{ ucfirst(str_replace('_', ' ', $transaction->type)) }}</h4>
                    <p>{{ $transaction->created_at->format('d/m/Y à H:i') }} • {{ $transaction->reference }}</p>
                </div>
            </div>
            <div class="transaction-amount">
                <div class="amount {{ in_array($transaction->type, ['depot', 'parrainage', 'code_caisse', 'bourse']) ? 'positive' : 'negative' }}">
                    {{ in_array($transaction->type, ['depot', 'parrainage', 'code_caisse', 'bourse']) ? '+' : '-' }}{{ number_format($transaction->points, 0, ',', ' ') }} pts
                </div>
                <div class="badge badge-{{ $transaction->statut === 'complete' ? 'success' : 'warning' }}" style="font-size: 0.75rem;">
                    {{ ucfirst($transaction->statut) }}
                </div>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 3rem; color: var(--gray-600);">
            <p>Aucune transaction</p>
        </div>
        @endforelse
    </div>

    <!-- Onglet Packs -->
    <div id="packs" class="tab-content">
        <div class="card-header">
            <h3 class="card-title">Packs de formation</h3>
        </div>
        
        @forelse($user->packs as $userPack)
        <div class="transaction-item">
            <div class="transaction-info">
                <div class="transaction-icon blue">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="transaction-details">
                    <h4>{{ $userPack->pack->nom }}</h4>
                    <p>{{ $userPack->duree_choisie }} • Acheté le {{ $userPack->date_achat->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="transaction-amount">
                <div style="font-weight: 600; color: var(--gray-900); margin-bottom: 0.25rem;">
                    {{ number_format($userPack->progression, 0) }}%
                </div>
                <div class="badge badge-{{ $userPack->statut === 'actif' ? 'success' : 'warning' }}">
                    {{ ucfirst($userPack->statut) }}
                </div>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 3rem; color: var(--gray-600);">
            <p>Aucun pack acheté</p>
        </div>
        @endforelse
    </div>

    <!-- Onglet Filleuls -->
    <div id="filleuls" class="tab-content">
        <div class="card-header">
            <h3 class="card-title">Filleuls parrainés</h3>
        </div>
        
        @forelse($user->filleuls as $filleul)
        <div class="transaction-item">
            <div class="transaction-info">
                <div class="transaction-icon green">
                    {{ strtoupper(substr($filleul->prenom, 0, 1)) }}{{ strtoupper(substr($filleul->nom, 0, 1)) }}
                </div>
                <div class="transaction-details">
                    <h4>{{ $filleul->prenom }} {{ $filleul->nom }}</h4>
                    <p>{{ $filleul->telephone }} • Inscrit le {{ $filleul->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.users.show', $filleul) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                    Voir profil
                </a>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 3rem; color: var(--gray-600);">
            <p>Aucun filleul</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Ajouter des points -->
<div id="addPointsModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Ajouter des points</h3>
        </div>
        
        <form method="POST" action="{{ route('admin.users.add-points', $user) }}">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label">Nombre de points</label>
                <input type="number" name="points" class="form-control" step="0.01" min="0.01" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Motif</label>
                <textarea name="motif" class="form-control" rows="3" required placeholder="Raison de l'ajout de points..."></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addPointsModal')">Annuler</button>
                <button type="submit" class="btn btn-success">Ajouter les points</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showTab(tabName) {
        // Cacher tous les onglets
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Désactiver tous les boutons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Activer l'onglet sélectionné
        document.getElementById(tabName).classList.add('active');
        event.target.classList.add('active');
    }
    
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }
    
    // Fermer le modal en cliquant en dehors
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
</script>
@endpush
@endsection