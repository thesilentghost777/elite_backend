<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Individuelle & Relevé Comptable - {{ $user->full_name }}</title>
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
            font-size: 13px;
        }

        .print-container {
            max-width: 900px;
            margin: 20px auto;
            background: #ffffff;
            padding: 40px 45px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        /* Toolbar Screen Only */
        .no-print-toolbar {
            background: #0f172a;
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
            background: #334155;
            color: #e2e8f0;
        }

        .btn-back:hover {
            background: #475569;
            color: #ffffff;
        }

        /* Header Document */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0f172a;
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
            background: #0f172a;
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
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .brand-sub {
            font-size: 11px;
            color: #64748b;
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
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Section Cards */
        .section-box {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 20px;
            background: #ffffff;
        }

        .section-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0f172a;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 8px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
        }

        .info-val {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Table Installments */
        .inst-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .inst-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }

        .inst-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
        }

        .badge-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-paid { background: #dcfce7; color: #166534; }
        .badge-late { background: #fee2e2; color: #991b1b; }
        .badge-pending { background: #f1f5f9; color: #475569; }

        /* Total Box */
        .total-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 14px;
        }

        .signatures-area {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .sig-card {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 16px;
            min-height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sig-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
        }

        .sig-footer {
            font-size: 10px;
            color: #94a3b8;
            font-style: italic;
            text-align: right;
        }

        /* Print Media */
        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm 14mm;
            }

            body {
                background: #ffffff;
                color: #000000;
                font-size: 12px;
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

            .section-box, .total-box {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-status {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Barre d'outils Écran -->
    <div class="no-print-toolbar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('admin.comptabilite.index') }}" class="btn-toolbar btn-back">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <span style="font-size: 13px; font-weight: 600;">
                <i class="fas fa-user-graduate" style="color: #60a5fa; margin-right: 6px;"></i>
                Fiche Étudiant : {{ $user->full_name }}
            </span>
        </div>

        <button onclick="window.print()" class="btn-toolbar btn-print">
            <i class="fas fa-print"></i> Imprimer / Télécharger en PDF
        </button>
    </div>

    <!-- Document A4 -->
    <div class="print-container">
        <!-- En-tête -->
        <div class="doc-header">
            <div class="brand-block">
                <div class="logo-box">E</div>
                <div>
                    <div class="brand-title">ELITE 2.0</div>
                    <div class="brand-sub">Fiche de Scolarité & Relevé Comptable Individuel</div>
                </div>
            </div>

            <div class="doc-meta">
                <div class="doc-title-main">Bulletin Individuel Apprenant</div>
                <div>Date : <strong>{{ now()->format('d/m/Y') }}</strong></div>
                <div>Matricule / Code : <strong>{{ $user->referral_code }}</strong></div>
            </div>
        </div>

        <!-- 1. Informations Personnelles & Centre -->
        <div class="section-box">
            <div class="section-title">
                <i class="fas fa-id-card" style="color: #2563eb;"></i> 1. Identité de l'Apprenant & Centre de Rattachement
            </div>
            <div class="grid-3">
                <div class="info-row">
                    <div class="info-label">Nom & Prénom</div>
                    <div class="info-val">{{ $user->full_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Téléphone</div>
                    <div class="info-val">{{ $user->telephone ?: '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-val">{{ $user->email ?: '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Ville de Résidence</div>
                    <div class="info-val">{{ $user->ville ?: 'Non précisée' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dernier Diplôme</div>
                    <div class="info-val">{{ $user->dernier_diplome ?: '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Centre Partenaire</div>
                    <div class="info-val" style="color: #1e40af;">
                        {{ $user->partner ? $user->partner->nom . ' (' . $user->partner->code_partenaire . ')' : 'Candidat Libre' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Parcours Pédagogique -->
        <div class="section-box">
            <div class="section-title">
                <i class="fas fa-graduation-cap" style="color: #059669;"></i> 2. Statut Pédagogique & Progression
            </div>
            <div class="grid-3">
                <div class="info-row">
                    <div class="info-label">Formation / Pack</div>
                    <div class="info-val">{{ $userPack && $userPack->pack ? $userPack->pack->nom : 'Aucun pack souscrit' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Progression des Modules</div>
                    <div class="info-val" style="color: #2563eb;">
                        {{ $userPack ? round($userPack->progression) : 0 }}% Complété
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Statut Actuel</div>
                    <div class="info-val">
                        @if($user->formation_status === 'active')
                            <span class="badge-status badge-paid">En cours de formation</span>
                        @elseif($user->formation_status === 'complete')
                            <span class="badge-status badge-paid">Formation Validée</span>
                        @elseif($user->formation_status === 'failed')
                            <span class="badge-status badge-late">Parcours Arrêté</span>
                        @else
                            <span class="badge-status badge-pending">{{ $user->formation_status ?: 'Non démarré' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quiz Résultats -->
            @if($user->quizResults->isNotEmpty())
                <div style="margin-top: 12px; border-top: 1px dashed #e2e8f0; padding-top: 10px;">
                    <div class="info-label" style="margin-bottom: 6px;">Évaluations & Quiz Réalisés :</div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        @foreach($user->quizResults as $qr)
                            <span style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 4px 8px; border-radius: 6px; font-size: 11px;">
                                Quiz #{{ $qr->quiz_id }} : <strong>{{ $qr->note }}/20</strong> ({{ $qr->bonnes_reponses }}/10 rép.)
                                @if($qr->reussi) <span style="color: #166534; font-weight: bold;">✓ Réussi</span> @else <span style="color: #991b1b;">✗ Non validé</span> @endif
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- 3. Situation Comptable des 5 Tranches -->
        <div class="section-box">
            <div class="section-title">
                <i class="fas fa-coins" style="color: #d97706;"></i> 3. Échéancier Financier & Encaissements
            </div>

            @if($installments->isEmpty())
                <div style="padding: 12px; background: #f8fafc; border-radius: 6px; color: #64748b; font-size: 12px;">
                    Aucun échéancier de tranches configuré pour cet apprenant. 
                    @if($userPack && $userPack->prix_paye)
                        Paiement direct enregistré : <strong>{{ number_format($userPack->prix_paye, 0, ',', ' ') }} FCFA</strong>.
                    @endif
                </div>
            @else
                <table class="inst-table">
                    <thead>
                        <tr>
                            <th>Tranche</th>
                            <th>Montant</th>
                            <th>Date d'échéance</th>
                            <th>Date de règlement</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($installments as $inst)
                            <tr>
                                <td><strong>{{ $inst->planInstallment->libelle ?? ('Tranche ' . $loop->iteration) }}</strong></td>
                                <td style="font-weight: 700;">{{ number_format($inst->montant_fcfa, 0, ',', ' ') }} FCFA</td>
                                <td>{{ $inst->due_at ? \Carbon\Carbon::parse($inst->due_at)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $inst->paid_at ? \Carbon\Carbon::parse($inst->paid_at)->format('d/m/Y à H:i') : '-' }}</td>
                                <td>
                                    @if($inst->statut === 'paye')
                                        <span class="badge-status badge-paid">✓ Réglé</span>
                                    @elseif($inst->statut === 'en_retard')
                                        <span class="badge-status badge-late">! En Retard</span>
                                    @else
                                        <span class="badge-status badge-pending">En Attente</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <!-- Résumé Financier -->
            <div class="total-box">
                <div>
                    <div class="info-label">Coût Total Formation</div>
                    <div style="font-size: 16px; font-weight: 800; color: #0f172a;">
                        {{ number_format($totalAttendu, 0, ',', ' ') }} FCFA
                    </div>
                </div>
                <div>
                    <div class="info-label">Total Encaissé</div>
                    <div style="font-size: 16px; font-weight: 800; color: #166534;">
                        {{ number_format($totalEncaisse, 0, ',', ' ') }} FCFA
                    </div>
                </div>
                <div>
                    <div class="info-label">Reste à Payer</div>
                    <div style="font-size: 16px; font-weight: 800; color: {{ $resteAPayer > 0 ? '#991b1b' : '#166534' }};">
                        {{ number_format($resteAPayer, 0, ',', ' ') }} FCFA
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Signatures -->
        <div class="signatures-area">
            <div class="sig-card">
                <div class="sig-title">L'Apprenant / Étudiant</div>
                <div class="sig-footer">Lu et approuvé &bull; Date & Signature</div>
            </div>
            <div class="sig-card">
                <div class="sig-title">Direction de l'Établissement / Administration</div>
                <div class="sig-footer">Cachet & Signature Autorisé(e)</div>
            </div>
        </div>
    </div>

</body>
</html>
