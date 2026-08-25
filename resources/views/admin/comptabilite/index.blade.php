@extends('admin.layouts.app')

@section('title', 'Comptabilité & Suivi des Apprenants')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span class="breadcrumb-separator">/</span>
    <span>Comptabilité & Rapports</span>
@endsection

@section('content')
<style>
    .page-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .stats-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 1.5rem;
        border: 1px solid var(--gray-200);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-blue);
    }

    .kpi-card.green::before { background: var(--success-green); }
    .kpi-card.amber::before { background: var(--warning); }
    .kpi-card.red::before { background: var(--danger); }
    .kpi-card.purple::before { background: #8b5cf6; }

    .kpi-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
    }

    .kpi-val {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--gray-900);
        line-height: 1.2;
    }

    .kpi-sub {
        font-size: 0.8rem;
        color: var(--gray-600);
        margin-top: 0.5rem;
    }

    .filter-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 1.5rem;
        border: 1px solid var(--gray-200);
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: flex-end;
    }

    .form-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--gray-700);
        margin-bottom: 0.35rem;
    }

    .form-control {
        width: 100%;
        padding: 0.6rem 0.85rem;
        border-radius: 8px;
        border: 1px solid var(--gray-300);
        font-size: 0.875rem;
        background: #ffffff;
        color: var(--gray-900);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .data-table-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid var(--gray-200);
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }

    .table-header-bar {
        padding: 1.25rem 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .styled-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .styled-table th {
        background: #f8fafc;
        padding: 0.85rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--gray-600);
        border-bottom: 1px solid var(--gray-200);
    }

    .styled-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.85rem;
        vertical-align: top;
    }

    .styled-table tr:hover {
        background: #fdfdfd;
    }

    .progress-bar-wrap {
        background: var(--gray-200);
        border-radius: 999px;
        height: 7px;
        overflow: hidden;
        margin-top: 0.35rem;
    }

    .progress-bar-inner {
        background: linear-gradient(90deg, var(--primary-blue), #3b82f6);
        height: 100%;
    }

    .badge-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.6rem;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-danger { background: #fee2e2; color: #991b1b; }
    .badge-info { background: #dbeafe; color: #1e40af; }
    .badge-gray { background: #f1f5f9; color: #475569; }

    .installment-micro-pill {
        display: inline-block;
        padding: 0.15rem 0.4rem;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 700;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
    }
</style>

<!-- Header Principal -->
<div class="page-header-flex">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--gray-900); margin: 0;">
            Comptabilité & Suivi des Apprenants
        </h1>
        <p style="color: var(--gray-600); margin: 0.25rem 0 0 0; font-size: 0.9rem;">
            Rapports consolidés, suivi des encaissements par tranche et relevés individuels des étudiants.
        </p>
    </div>

    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="{{ route('admin.comptabilite.report', request()->query()) }}" target="_blank" class="btn btn-primary" style="padding: 0.65rem 1.25rem; font-size: 0.875rem; font-weight: 700;">
            <i class="fas fa-print" style="margin-right: 0.4rem;"></i> Imprimer le Rapport Global (PDF)
        </a>
    </div>
</div>

<!-- Grille des Statistiques Clés (KPI) -->
<div class="stats-kpi-grid">
    <div class="kpi-card green">
        <div class="kpi-title">Total Encaissé</div>
        <div class="kpi-val" style="color: #059669;">
            {{ number_format($totalEncaisse, 0, ',', ' ') }} <span style="font-size: 0.9rem; font-weight: 600;">FCFA</span>
        </div>
        <div class="kpi-sub">
            Taux de recouvrement : <strong>{{ $tauxRecouvrement }}%</strong>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-title">Total Attendu</div>
        <div class="kpi-val" style="color: var(--primary-blue);">
            {{ number_format($totalAttendu, 0, ',', ' ') }} <span style="font-size: 0.9rem; font-weight: 600;">FCFA</span>
        </div>
        <div class="kpi-sub">
            Sur l'ensemble des tranches des apprenants rattachés
        </div>
    </div>

    <div class="kpi-card red">
        <div class="kpi-title">Impayés & En Retard</div>
        <div class="kpi-val" style="color: #dc2626;">
            {{ number_format($totalEnRetard, 0, ',', ' ') }} <span style="font-size: 0.9rem; font-weight: 600;">FCFA</span>
        </div>
        <div class="kpi-sub">
            Échéances dépassées non régularisées
        </div>
    </div>

    <div class="kpi-card amber">
        <div class="kpi-title">En Attente / Échéances Futures</div>
        <div class="kpi-val" style="color: #d97706;">
            {{ number_format($totalEnAttente, 0, ',', ' ') }} <span style="font-size: 0.9rem; font-weight: 600;">FCFA</span>
        </div>
        <div class="kpi-sub">
            Tranches futures non encore échues
        </div>
    </div>

    <div class="kpi-card purple">
        <div class="kpi-title">Effectif Apprenants</div>
        <div class="kpi-val" style="color: #7c3aed;">
            {{ $totalLearners }}
        </div>
        <div class="kpi-sub">
            <span style="color: #059669; font-weight: 700;">{{ $activeLearners }} actif(s)</span> &bull; 
            <span style="color: #2563eb; font-weight: 700;">{{ $completedLearners }} terminé(s)</span> &bull; 
            <span style="color: #dc2626; font-weight: 700;">{{ $failedLearners }} échec(s)</span>
        </div>
    </div>
</div>

<!-- Filtres de Recherche Avancés -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.comptabilite.index') }}">
        <div class="filter-grid">
            <div class="form-group">
                <label>Recherche Apprenant</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, matricule, tél, email..." class="form-control">
            </div>

            <div class="form-group">
                <label>Centre / Partenaire</label>
                <select name="partner_id" class="form-control">
                    <option value="">Tous les établissements</option>
                    <option value="none" {{ request('partner_id') === 'none' ? 'selected' : '' }}>Sans partenaire (Candidats libres)</option>
                    @foreach($partners as $p)
                        <option value="{{ $p->id }}" {{ (string)request('partner_id') === (string)$p->id ? 'selected' : '' }}>
                            {{ $p->nom }} ({{ $p->code_partenaire }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Formation / Pack</label>
                <select name="pack_id" class="form-control">
                    <option value="">Toutes les formations</option>
                    @foreach($packs as $pack)
                        <option value="{{ $pack->id }}" {{ (string)request('pack_id') === (string)$pack->id ? 'selected' : '' }}>
                            {{ $pack->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Statut Pédagogique</label>
                <select name="formation_status" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('formation_status') === 'active' ? 'selected' : '' }}>En cours (Actif)</option>
                    <option value="complete" {{ request('formation_status') === 'complete' ? 'selected' : '' }}>Validé / Terminé</option>
                    <option value="failed" {{ request('formation_status') === 'failed' ? 'selected' : '' }}>Échec / Retard</option>
                </select>
            </div>

            <div class="form-group">
                <label>Statut Comptable</label>
                <select name="payment_status" class="form-control">
                    <option value="">Tous les statuts financiers</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>100% Soldé</option>
                    <option value="late" {{ request('payment_status') === 'late' ? 'selected' : '' }}>Avec Impayé / Retard</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Tranches en attente</option>
                </select>
            </div>

            <div class="form-group">
                <label>Inscrit du</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Au</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.65rem;">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <a href="{{ route('admin.comptabilite.index') }}" class="btn btn-secondary" style="padding: 0.65rem 1rem;" title="Réinitialiser">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Tableau des Apprenants & Détail Comptable -->
<div class="data-table-card">
    <div class="table-header-bar">
        <div>
            <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--gray-900);">
                Relevé des Apprenants ({{ $learners->total() }})
            </h3>
            <div style="font-size: 0.8rem; color: var(--gray-600); margin-top: 0.15rem;">
                Affichage paginé &bull; Cliquez sur "Fiche PDF" pour éditer le relevé individuel complet.
            </div>
        </div>

        <a href="{{ route('admin.comptabilite.report', request()->query()) }}" target="_blank" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;">
            <i class="fas fa-file-pdf" style="color: #dc2626; margin-right: 0.35rem;"></i> Exporter cette vue en PDF
        </a>
    </div>

    @if($learners->isEmpty())
        <div style="padding: 3rem; text-align: center; color: var(--gray-600);">
            <i class="fas fa-user-graduate" style="font-size: 2.5rem; color: var(--gray-300); margin-bottom: 1rem; display: block;"></i>
            <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--gray-800); margin-bottom: 0.25rem;">Aucun apprenant trouvé</h4>
            <p style="font-size: 0.85rem;">Modifiez vos critères de recherche ou réinitialisez les filtres.</p>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Apprenant</th>
                        <th>Établissement / Centre</th>
                        <th>Formation & Progression</th>
                        <th>Situation Financière</th>
                        <th style="text-align: right;">Rapports & Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($learners as $learner)
                        @php
                            $userPack = $learner->userPacks->first();
                            $installments = $userPack ? $userPack->installments->sortBy('planInstallment.ordre') : collect();
                            $totalAtt = $installments->sum('montant_fcfa');
                            if ($totalAtt == 0 && $userPack && $userPack->pack) {
                                $totalAtt = (float)($userPack->pack->prix_fcfa ?: 0);
                            }
                            $totalEnc = $installments->where('statut', 'paye')->sum('montant_fcfa');
                            if ($installments->isEmpty() && $userPack && $userPack->statut === 'actif') {
                                $totalEnc = (float)($userPack->prix_paye ?: 0);
                            }
                            $totalRet = $installments->where('statut', 'en_retard')->sum('montant_fcfa');
                            $reste = max(0, $totalAtt - $totalEnc);
                            $progression = $userPack ? round($userPack->progression) : 0;
                        @endphp
                        <tr>
                            <!-- 1. Apprenant -->
                            <td>
                                <div style="font-weight: 700; color: var(--gray-900); font-size: 0.95rem;">
                                    {{ $learner->full_name }}
                                </div>
                                <div style="color: var(--gray-600); font-size: 0.8rem; margin-top: 0.15rem;">
                                    <i class="fas fa-phone-alt" style="font-size: 0.7rem; margin-right: 0.25rem;"></i> {{ $learner->telephone ?: '-' }}
                                    @if($learner->email)
                                        &bull; {{ $learner->email }}
                                    @endif
                                </div>
                                <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 0.15rem;">
                                    Inscrit le {{ $learner->created_at ? $learner->created_at->format('d/m/Y') : '-' }} &bull; Code: <strong>{{ $learner->referral_code }}</strong>
                                </div>
                            </td>

                            <!-- 2. Centre Partenaire -->
                            <td>
                                @if($learner->partner)
                                    <div style="font-weight: 700; color: var(--secondary-blue);">
                                        {{ $learner->partner->nom }}
                                    </div>
                                    <span class="badge-pill badge-info" style="margin-top: 0.25rem;">
                                        Code: {{ $learner->partner->code_partenaire ?: '-' }}
                                    </span>
                                @else
                                    <span class="badge-pill badge-gray">Candidat Libre</span>
                                @endif
                            </td>

                            <!-- 3. Formation & Progression -->
                            <td>
                                @if($userPack && $userPack->pack)
                                    <div style="font-weight: 600; color: var(--gray-900);">
                                        {{ $userPack->pack->nom }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.35rem;">
                                        <div style="flex: 1;">
                                            <div class="progress-bar-wrap">
                                                <div class="progress-bar-inner" style="width: {{ $progression }}%;"></div>
                                            </div>
                                        </div>
                                        <span style="font-weight: 700; font-size: 0.75rem; color: var(--gray-700);">{{ $progression }}%</span>
                                    </div>
                                @else
                                    <span style="color: var(--gray-500); font-style: italic;">Aucun pack souscrit</span>
                                @endif

                                <div style="margin-top: 0.4rem;">
                                    @if($learner->formation_status === 'active')
                                        <span class="badge-pill badge-success">En formation</span>
                                    @elseif($learner->formation_status === 'complete')
                                        <span class="badge-pill badge-info">Formation Validée</span>
                                    @elseif($learner->formation_status === 'failed')
                                        <span class="badge-pill badge-danger">Échec / Retard</span>
                                    @else
                                        <span class="badge-pill badge-gray">{{ $learner->formation_status ?: 'Non démarré' }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 4. Situation Financière & Tranches -->
                            <td>
                                <div style="display: flex; justify-content: space-between; gap: 0.5rem; font-size: 0.85rem;">
                                    <div>
                                        <div style="font-size: 0.75rem; color: var(--gray-600);">Payé :</div>
                                        <strong style="color: #059669;">{{ number_format($totalEnc, 0, ',', ' ') }} F</strong>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.75rem; color: var(--gray-600);">Total dû :</div>
                                        <strong style="color: var(--gray-900);">{{ number_format($totalAtt, 0, ',', ' ') }} F</strong>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.75rem; color: var(--gray-600);">Solde :</div>
                                        <strong style="color: {{ $reste > 0 ? '#dc2626' : '#059669' }};">{{ number_format($reste, 0, ',', ' ') }} F</strong>
                                    </div>
                                </div>

                                <!-- Découpage des tranches -->
                                @if($installments->isNotEmpty())
                                    <div style="margin-top: 0.5rem;">
                                        @foreach($installments as $inst)
                                            @php
                                                $shortLib = $inst->planInstallment->libelle ?? ('T' . $loop->iteration);
                                            @endphp
                                            <span class="installment-micro-pill {{ $inst->statut === 'paye' ? 'badge-success' : ($inst->statut === 'en_retard' ? 'badge-danger' : 'badge-gray') }}"
                                                  title="{{ $shortLib }} : {{ number_format($inst->montant_fcfa, 0, ',', ' ') }} FCFA ({{ $inst->statut }})">
                                                {{ $shortLib }} : {{ $inst->statut === 'paye' ? '✓' : ($inst->statut === 'en_retard' ? '!' : '…') }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <!-- 5. Actions & Fiche individuelle -->
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; align-items: center;">
                                    <a href="{{ route('admin.comptabilite.learner', $learner) }}" target="_blank" class="btn btn-primary" style="padding: 0.4rem 0.75rem; font-size: 0.75rem; font-weight: 700;">
                                        <i class="fas fa-file-invoice"></i> Fiche PDF
                                    </a>

                                    <a href="{{ route('admin.users.show', $learner) }}" class="btn btn-secondary" style="padding: 0.4rem 0.65rem; font-size: 0.75rem;" title="Voir le profil">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($learners->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--gray-200); background: #f8fafc;">
                {{ $learners->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
