<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Comptable & Suivi des Étudiants - {{ $partner->nom }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            line-height: 1.5;
            font-size: 12px;
        }

        .print-container {
            max-width: 1200px;
            margin: 20px auto;
            background: #ffffff;
            padding: 35px 45px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        /* Toolbar Screen Only */
        .no-print-toolbar {
            background: #040D24;
            color: #ffffff;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 9999;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-toolbar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }

        .btn-print {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-print:hover {
            background: #1d4ed8;
        }

        .btn-back {
            background: #1e293b;
            color: #cbd5e1;
        }

        .btn-back:hover {
            background: #334155;
            color: #ffffff;
        }

        /* Document Header */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #040D24;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .brand-block {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-box {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #1d4ed8, #4338ca);
            color: #ffffff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 900;
        }

        .brand-title {
            font-size: 20px;
            font-weight: 800;
            color: #040D24;
            letter-spacing: -0.02em;
        }

        .brand-sub {
            font-size: 11px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .doc-meta {
            text-align: right;
            font-size: 11px;
            color: #475569;
        }

        .doc-title-main {
            font-size: 15px;
            font-weight: 800;
            color: #040D24;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Summary Stats Cards */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 25px;
        }

        .stat-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            border-radius: 8px;
        }

        .stat-box.highlight-green {
            border-color: #86efac;
            background: #f0fdf4;
        }

        .stat-box.highlight-red {
            border-color: #fca5a5;
            background: #fef2f2;
        }

        .stat-box-lbl {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-box-val {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 4px;
        }

        /* Filter Pills */
        .filters-summary {
            background: #f1f5f9;
            padding: 8px 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 11px;
            color: #334155;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Report Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .report-table th {
            background: #040D24;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #040D24;
        }

        .report-table td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
            vertical-align: middle;
        }

        .report-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .report-table tfoot td {
            background: #e2e8f0;
            font-weight: 800;
            font-size: 11px;
            border-top: 2px solid #040D24;
        }

        .badge-print {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-ok { background: #dcfce7; color: #166534; }
        .badge-fail { background: #fee2e2; color: #991b1b; }
        .badge-progress { background: #e0f2fe; color: #0369a1; }

        /* Signatures block */
        .signatures-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 35px;
            page-break-inside: avoid;
        }

        .signature-card {
            border: 1px dashed #94a3b8;
            border-radius: 8px;
            padding: 16px;
            min-height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .signature-title {
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            color: #334155;
        }

        .signature-space {
            font-size: 10px;
            color: #94a3b8;
            font-style: italic;
            text-align: right;
            margin-top: 40px;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm 12mm;
            }

            body {
                background: #ffffff;
                color: #000000;
                font-size: 11px;
            }

            .no-print-toolbar {
                display: none !important;
            }

            .print-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .report-table th {
                background-color: #040D24 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .stat-box, .stat-box.highlight-green, .stat-box.highlight-red {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-print {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .report-table tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- Barre d'outils Écran -->
    <div class="no-print-toolbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('partner.comptabilite', request()->query()) }}" class="btn-toolbar btn-back">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <span style="font-size: 13px; font-weight: 600;">
                <i class="fas fa-file-invoice-dollar" style="color: #60a5fa; margin-right: 6px;"></i>
                Rapport Comptable Global &bull; {{ $partner->nom }}
            </span>
        </div>

        <button onclick="window.print()" class="btn-toolbar btn-print">
            <i class="fas fa-print"></i> Imprimer / Télécharger en PDF
        </button>
    </div>

    <!-- Document Principal -->
    <div class="print-container">
        <!-- En-tête Institutionnel Partenaire -->
        <div class="report-header">
            <div class="brand-block">
                <div class="logo-box">E</div>
                <div>
                    <div class="brand-title">{{ $partner->nom }}</div>
                    <div class="brand-sub">Centre Agréé Elite 2.0 &bull; Code Partenaire : <strong>{{ $partner->code_partenaire ?: '-' }}</strong></div>
                </div>
            </div>

            <div class="doc-meta">
                <div class="doc-title-main">Rapport de Comptabilité & Suivi Étudiants</div>
                <div>Date : <strong>{{ $generatedAt->format('d/m/Y à H:i') }}</strong></div>
                <div>Contact : <strong>{{ $partner->telephone ?: $partner->email }}</strong></div>
            </div>
        </div>

        <!-- Filtres actifs -->
        <div class="filters-summary">
            <span><strong>Filtres :</strong></span>
            <span>Statut Formation : <strong>{{ request('statut') ? ucfirst(request('statut')) : 'Tous statuts' }}</strong></span>
            <span>Statut Paiement : <strong>{{ request('payment_status') ? ucfirst(request('payment_status')) : 'Tous' }}</strong></span>
            <span>Total Étudiants Rattachés : <strong>{{ $learners->count() }}</strong></span>
        </div>

        <!-- Synthèse Financière -->
        <div class="stats-strip">
            <div class="stat-box highlight-green">
                <div class="stat-box-lbl">Total Encaissé (Guichet + Points)</div>
                <div class="stat-box-val" style="color: #166534;">
                    {{ number_format($totalEncaisse, 0, ',', ' ') }} FCFA
                </div>
            </div>

            <div class="stat-box">
                <div class="stat-box-lbl">Total Attendu (5 Tranches)</div>
                <div class="stat-box-val" style="color: #1e3a8a;">
                    {{ number_format($totalAttendu, 0, ',', ' ') }} FCFA
                </div>
            </div>

            <div class="stat-box highlight-red">
                <div class="stat-box-lbl">Tranches en Retard / Impayés</div>
                <div class="stat-box-val" style="color: #991b1b;">
                    {{ number_format($totalEnRetard, 0, ',', ' ') }} FCFA
                </div>
            </div>

            <div class="stat-box">
                <div class="stat-box-lbl">Taux de Recouvrement</div>
                <div class="stat-box-val">
                    {{ $tauxRecouvrement }}%
                </div>
            </div>
        </div>

        <!-- Tableau Détaillé -->
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 30px;">#</th>
                    <th>Étudiant / Matricule</th>
                    <th>Contact</th>
                    <th>Formation Inscrite</th>
                    <th style="text-align: center;">Prog.</th>
                    <th style="text-align: right;">Total Attendu</th>
                    <th style="text-align: right;">Encaissé</th>
                    <th style="text-align: right;">Solde Restant</th>
                    <th style="text-align: center;">Statut Formation</th>
                </tr>
            </thead>
            <tbody>
                @forelse($learners as $index => $learner)
                    @php
                        $userPack = $learner->userPacks->first();
                        $installments = $userPack ? $userPack->installments->sortBy('planInstallment.ordre') : collect();
                        $totalAtt = $installments->sum('montant_fcfa');
                        $totalEnc = $installments->where('statut', 'paye')->sum('montant_fcfa');
                        $reste = max(0, $totalAtt - $totalEnc);
                        $prog = $userPack ? round($userPack->progression) : 0;
                    @endphp
                    <tr>
                        <td style="text-align: center; color: #64748b;">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $learner->full_name }}</strong>
                            <div style="font-size: 10px; color: #64748b;">Matricule: {{ $learner->referral_code }}</div>
                        </td>
                        <td>
                            {{ $learner->telephone ?: $learner->email }}
                        </td>
                        <td>
                            {{ $userPack && $userPack->pack ? $userPack->pack->nom : 'Pack non sélectionné' }}
                        </td>
                        <td style="text-align: center; font-weight: 700;">
                            {{ $prog }}%
                        </td>
                        <td style="text-align: right; font-weight: 600;">
                            {{ number_format($totalAtt, 0, ',', ' ') }} F
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #166534;">
                            {{ number_format($totalEnc, 0, ',', ' ') }} F
                        </td>
                        <td style="text-align: right; font-weight: 700; color: {{ $reste > 0 ? '#991b1b' : '#166534' }};">
                            {{ number_format($reste, 0, ',', ' ') }} F
                        </td>
                        <td style="text-align: center;">
                            @if($learner->formation_status === 'complete')
                                <span class="badge-print badge-ok">Validé</span>
                            @elseif($learner->formation_status === 'failed')
                                <span class="badge-print badge-fail">Échec</span>
                            @else
                                <span class="badge-print badge-progress">En cours</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px; color: #64748b;">
                            Aucun apprenant enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right; font-size: 11px;">TOTAUX DU CENTRE :</td>
                    <td style="text-align: right;">{{ number_format($totalAttendu, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right; color: #166534;">{{ number_format($totalEncaisse, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align: right; color: {{ ($totalAttendu - $totalEncaisse) > 0 ? '#991b1b' : '#166534' }};">
                        {{ number_format(max(0, $totalAttendu - $totalEncaisse), 0, ',', ' ') }} FCFA
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="signatures-grid">
            <div class="signature-card">
                <div class="signature-title">Le Comptable / Guichet du Centre</div>
                <div class="signature-space">Visa & Signature</div>
            </div>

            <div class="signature-card">
                <div class="signature-title">Le Directeur du Centre ({{ $partner->nom }})</div>
                <div class="signature-space">Cachet Officiel & Signature</div>
            </div>
        </div>
    </div>

</body>
</html>
