@extends('admin.layouts.app')

@section('title', 'Paramètres financiers')

@push('styles')
<style>
    .settings-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .setting-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        overflow: hidden;
        border-left: 4px solid var(--primary-blue);
    }

    .setting-card.green {
        border-left-color: var(--success-green);
    }

    .setting-card.orange {
        border-left-color: #f59e0b;
    }

    .setting-header {
        padding: 1.5rem 2rem;
        border-bottom: 2px solid var(--gray-100);
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
        color: var(--white);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .setting-card.green .setting-header {
        background: linear-gradient(135deg, var(--success-green) 0%, var(--dark-green) 100%);
    }

    .setting-card.orange .setting-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .setting-header h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .setting-header svg {
        width: 24px;
        height: 24px;
    }

    .setting-body {
        padding: 2rem;
    }

    .setting-group {
        margin-bottom: 1.5rem;
    }

    .setting-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .setting-label .required {
        color: #ef4444;
    }

    .setting-description {
        font-size: 0.813rem;
        color: var(--gray-600);
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .input-with-unit {
        display: flex;
        align-items: center;
        max-width: 300px;
    }

    .input-with-unit .form-input {
        border-radius: 8px 0 0 8px;
        border-right: none;
    }

    .input-unit {
        padding: 0.75rem 1rem;
        background-color: var(--gray-100);
        border: 1px solid var(--gray-300);
        border-radius: 0 8px 8px 0;
        font-size: 0.875rem;
        color: var(--gray-700);
        font-weight: 500;
        min-width: 80px;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        transition: all 0.2s ease;
        background: var(--white);
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .example-box {
        background-color: #eff6ff;
        border: 1px solid #3b82f6;
        border-radius: 8px;
        padding: 1.25rem;
        margin-top: 1rem;
    }

    .setting-card.green .example-box {
        background-color: #d1fae5;
        border-color: #10b981;
    }

    .setting-card.orange .example-box {
        background-color: #ffedd5;
        border-color: #f59e0b;
    }

    .example-title {
        font-weight: 600;
        color: #1e40af;
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .setting-card.green .example-title {
        color: #065f46;
    }

    .setting-card.orange .example-title {
        color: #92400e;
    }

    .example-steps {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .example-step {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        color: #1e40af;
    }

    .setting-card.green .example-step {
        color: #065f46;
    }

    .setting-card.orange .example-step {
        color: #92400e;
    }

    .example-step span:first-child {
        flex: 1;
    }

    .example-arrow {
        color: #3b82f6;
        font-weight: 600;
    }

    .setting-card.green .example-arrow {
        color: #10b981;
    }

    .setting-card.orange .example-arrow {
        color: #f59e0b;
    }

    .warning-alert {
        background-color: #fffbeb;
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .warning-icon {
        color: #d97706;
        flex-shrink: 0;
        margin-top: 0.125rem;
    }

    .warning-content h3 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #92400e;
        margin-bottom: 0.25rem;
    }

    .warning-content p {
        font-size: 0.813rem;
        color: #92400e;
        line-height: 1.5;
        margin: 0;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--gray-200);
        flex-wrap: wrap;
        gap: 1rem;
    }

    .action-info {
        font-size: 0.813rem;
        color: var(--gray-600);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
    }

    .btn-cancel {
        padding: 0.75rem 1.5rem;
        background: var(--gray-200);
        color: var(--gray-700);
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-cancel:hover {
        background: var(--gray-300);
        transform: translateY(-1px);
    }

    .btn-submit {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--success-green) 100%);
        color: var(--white);
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }

    @media (max-width: 768px) {
        .setting-body {
            padding: 1.5rem;
        }

        .input-with-unit {
            max-width: 100%;
        }

        .form-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-cancel,
        .btn-submit {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Tableau de bord</a>
    <span>/</span>
    <a href="{{ route('admin.transactions.index') }}">Transactions</a>
    <span>/</span>
    <span>Paramètres</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Paramètres financiers</h1>
    <p class="page-description">Configuration des taux de conversion et des récompenses</p>
</div>

<!-- Avertissement -->
<div class="warning-alert">
    <div class="warning-icon">
        <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
    </div>
    <div class="warning-content">
        <h3>Attention</h3>
        <p>La modification de ces paramètres affectera tous les futurs calculs de points. Les transactions passées ne seront pas modifiées.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.transactions.update-settings') }}">
    @csrf
    @method('PUT')

    <!-- Taux de conversion -->
    <div class="setting-card">
        <div class="setting-header">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Taux de conversion FCFA → Points
            </h2>
        </div>
        <div class="setting-body">
            <div class="setting-group">
                <p class="setting-description">
                    Définit combien de FCFA équivalent à 1 point. Ce taux est utilisé pour la conversion lors des dépôts.
                </p>
                
                <label class="setting-label">
                    FCFA par point <span class="required">*</span>
                </label>
                <div class="input-with-unit">
                    <input 
                        type="number" 
                        name="taux_conversion_fcfa_points" 
                        id="tauxConversion"
                        class="form-input" 
                        value="{{ old('taux_conversion_fcfa_points', $settings['taux_conversion_fcfa_points'] ?? 500) }}"
                        min="1"
                        step="1"
                        required
                    >
                    <span class="input-unit">FCFA</span>
                </div>

                <div class="example-box">
                    <div class="example-title">
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 16px; height: 16px;">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                        </svg>
                        Exemples de conversion
                    </div>
                    <div class="example-steps">
                        <div class="example-step">
                            <span>1 000 FCFA</span>
                            <span class="example-arrow">→</span>
                            <span id="example1">2 points</span>
                        </div>
                        <div class="example-step">
                            <span>5 000 FCFA</span>
                            <span class="example-arrow">→</span>
                            <span id="example2">10 points</span>
                        </div>
                        <div class="example-step">
                            <span>25 000 FCFA</span>
                            <span class="example-arrow">→</span>
                            <span id="example3">50 points</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Récompense parrainage -->
    <div class="setting-card green">
        <div class="setting-header">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Récompense de parrainage
            </h2>
        </div>
        <div class="setting-body">
            <div class="setting-group">
                <p class="setting-description">
                    Nombre de points attribués au parrain lorsqu'un filleul s'inscrit avec son code.
                </p>
                
                <label class="setting-label">
                    Points par filleul <span class="required">*</span>
                </label>
                <div class="input-with-unit">
                    <input 
                        type="number" 
                        name="points_parrainage" 
                        id="pointsParrainage"
                        class="form-input" 
                        value="{{ old('points_parrainage', $settings['points_parrainage'] ?? 1) }}"
                        min="0.1"
                        step="0.1"
                        required
                    >
                    <span class="input-unit">Points</span>
                </div>

                <div class="example-box">
                    <div class="example-title">
                        <svg fill="currentColor" viewBox="0 0 20 20" style="width: 16px; height: 16px;">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                        </svg>
                        Impact du parrainage
                    </div>
                    <div class="example-steps">
                        <div class="example-step">
                            <span>1 filleul</span>
                            <span class="example-arrow">→</span>
                            <span id="referralExample1">1 point</span>
                        </div>
                        <div class="example-step">
                            <span>10 filleuls</span>
                            <span class="example-arrow">→</span>
                            <span id="referralExample2">10 points</span>
                        </div>
                        <div class="example-step">
                            <span>50 filleuls</span>
                            <span class="example-arrow">→</span>
                            <span id="referralExample3">50 points</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Code parrainage par défaut -->
    <div class="setting-card orange">
        <div class="setting-header">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
                Code de parrainage par défaut
            </h2>
        </div>
        <div class="setting-body">
            <div class="setting-group">
                <p class="setting-description">
                    Code utilisé lorsqu'un utilisateur s'inscrit sans code parrain. Utile pour les campagnes marketing.
                </p>
                
                <label class="setting-label">
                    Code par défaut <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    name="code_parrainage_defaut" 
                    id="codeParrainageDefaut"
                    class="form-input" 
                    value="{{ old('code_parrainage_defaut', $settings['code_parrainage_defaut'] ?? 'ELITE2024') }}"
                    maxlength="20"
                    style="max-width: 300px; font-family: 'Monaco', 'Menlo', monospace; font-weight: 600; letter-spacing: 0.05em;"
                    required
                >
            </div>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="form-actions">
        <div class="action-info">
            <svg fill="currentColor" viewBox="0 0 20 20" style="width: 16px; height: 16px;">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            Les modifications seront appliquées immédiatement après la sauvegarde
        </div>
        <div class="action-buttons">
            <a href="{{ route('admin.transactions.index') }}" class="btn-cancel">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Annuler
            </a>
            <button type="submit" class="btn-submit">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les paramètres
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Mise à jour des exemples de conversion en temps réel
    const tauxInput = document.getElementById('tauxConversion');
    const pointsInput = document.getElementById('pointsParrainage');
    const codeInput = document.getElementById('codeParrainageDefaut');

    function updateConversionExamples() {
        const taux = parseFloat(tauxInput.value) || 500;
        document.getElementById('example1').textContent = Math.floor(1000 / taux) + ' points';
        document.getElementById('example2').textContent = Math.floor(5000 / taux) + ' points';
        document.getElementById('example3').textContent = Math.floor(25000 / taux) + ' points';
    }

    function updateReferralExamples() {
        const points = parseFloat(pointsInput.value) || 1;
        document.getElementById('referralExample1').textContent = (1 * points).toFixed(1) + ' point' + (points !== 1 ? 's' : '');
        document.getElementById('referralExample2').textContent = (10 * points).toFixed(1) + ' points';
        document.getElementById('referralExample3').textContent = (50 * points).toFixed(1) + ' points';
    }

    // Convertir le code en majuscules
    codeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Écouter les changements
    tauxInput.addEventListener('input', updateConversionExamples);
    pointsInput.addEventListener('input', updateReferralExamples);

    // Initialiser les exemples
    updateConversionExamples();
    updateReferralExamples();
</script>
@endpush