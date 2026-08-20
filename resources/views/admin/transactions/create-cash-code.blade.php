@extends('admin.layouts.app')

@section('title', 'Créer un Code Caisse')

@push('styles')
<style>
    .form-card {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .form-header {
        padding: 1.5rem 2rem;
        border-bottom: 2px solid var(--gray-100);
        background: linear-gradient(135deg, var(--primary-blue) 0%, var(--success-green) 100%);
        color: var(--white);
    }

    .form-header h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-header svg {
        width: 24px;
        height: 24px;
    }

    .form-body {
        padding: 2rem;
    }

    .preview-card {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        border-radius: 12px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
        position: relative;
        overflow: hidden;
    }

    .preview-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    }

    .preview-content {
        position: relative;
        z-index: 1;
    }

    .preview-label {
        font-size: 0.75rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 0.5rem;
    }

    .preview-code {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        margin-bottom: 1rem;
    }

    .preview-value {
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .form-label .required {
        color: #ef4444;
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

    .form-input.error {
        border-color: #ef4444;
    }

    .calculation-info {
        background-color: #eff6ff;
        border: 1px solid #3b82f6;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: #1e40af;
    }

    .quick-amounts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 0.75rem;
        margin-top: 0.75rem;
    }

    .quick-amount-btn {
        padding: 0.75rem;
        background-color: var(--gray-100);
        border: 2px solid var(--gray-300);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
        text-align: center;
        border: none;
        font-size: 0.875rem;
    }

    .quick-amount-btn:hover {
        background-color: #dbeafe;
        border-color: #3b82f6;
        color: #1e40af;
    }

    .quick-amount-btn.active {
        background-color: #3b82f6;
        color: white;
    }

    .user-search {
        position: relative;
    }

    .user-search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: var(--white);
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        margin-top: 0.25rem;
        max-height: 200px;
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 10;
        display: none;
    }

    .user-search-results.active {
        display: block;
    }

    .user-search-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background-color 0.2s;
        border-bottom: 1px solid var(--gray-200);
    }

    .user-search-item:hover {
        background-color: var(--gray-50);
    }

    .user-search-item:last-child {
        border-bottom: none;
    }

    .selected-user {
        background-color: #d1fae5;
        border: 1px solid #10b981;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .selected-user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .selected-user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-blue), var(--success-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }

    .form-help {
        font-size: 0.813rem;
        color: var(--gray-600);
        margin-top: 0.25rem;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid var(--gray-200);
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

    .error-message {
        font-size: 0.813rem;
        color: #ef4444;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    @media (max-width: 768px) {
        .form-body {
            padding: 1.5rem;
        }

        .quick-amounts {
            grid-template-columns: repeat(2, 1fr);
        }

        .form-actions {
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
    <a href="{{ route('admin.cash-codes.index') }}">Codes caisse</a>
    <span>/</span>
    <span>Créer</span>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Créer un Code Caisse</h1>
    <p class="page-description">Générer un nouveau code de rechargement</p>
</div>

<!-- Prévisualisation du code -->
<div class="preview-card">
    <div class="preview-content">
        <div class="preview-label">Code de rechargement</div>
        <div class="preview-code" id="previewCode">CASH-XXXXXXXX</div>
        <div class="preview-value">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <span id="previewPoints">0</span>
            <span style="opacity: 0.8; font-size: 0.875rem;">points</span>
        </div>
    </div>
</div>

<!-- Formulaire -->
<form method="POST" action="{{ route('admin.cash-codes.store') }}">
    @csrf

    <div class="form-card">
        <div class="form-header">
            <h2>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Valeur du code
            </h2>
        </div>
        <div class="form-body">
            <div class="form-group">
                <label class="form-label">
                    Montant en FCFA <span class="required">*</span>
                </label>
                <input 
                    type="number" 
                    name="montant_fcfa" 
                    id="montantFcfa"
                    class="form-input @error('montant_fcfa') error @enderror" 
                    value="{{ old('montant_fcfa') }}" 
                    min="100"
                    step="100"
                    required
                    oninput="calculatePoints(this.value)"
                >
                
                <!-- Montants rapides -->
                <div class="quick-amounts">
                    <button type="button" class="quick-amount-btn" onclick="setAmount(1000)">1 000 F</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(2500)">2 500 F</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(5000)">5 000 F</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(10000)">10 000 F</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(25000)">25 000 F</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(50000)">50 000 F</button>
                </div>

                <div class="calculation-info" id="calculationInfo" style="display: none;">
                    <strong>Calcul :</strong> <span id="calculationDetail"></span>
                </div>

                @error('montant_fcfa')
                    <div class="error-message">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    Assigner à un utilisateur (optionnel)
                </label>
                <div class="user-search">
                    <input 
                        type="text" 
                        id="userSearch"
                        class="form-input" 
                        placeholder="Rechercher un utilisateur..."
                        autocomplete="off"
                    >
                    <input type="hidden" name="user_id" id="selectedUserId">
                    
                    <div class="user-search-results" id="searchResults"></div>
                </div>
                
                <div id="selectedUserDisplay" style="display: none;" class="selected-user">
                    <div class="selected-user-info">
                        <div class="selected-user-avatar" id="selectedUserAvatar"></div>
                        <div>
                            <div style="font-weight: 600; color: var(--gray-900);" id="selectedUserName"></div>
                            <div style="font-size: 0.75rem; color: var(--gray-600);" id="selectedUserPhone"></div>
                        </div>
                    </div>
                    <button type="button" class="btn-cancel" onclick="clearUserSelection()" style="padding: 0.5rem;">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <div class="form-help">
                    Si vous assignez ce code à un utilisateur, seul lui pourra l'utiliser
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Date d'expiration (optionnel)
                </label>
                <input 
                    type="date" 
                    name="expires_at" 
                    class="form-input" 
                    value="{{ old('expires_at') }}"
                    min="{{ date('Y-m-d') }}"
                >
                <div class="form-help">
                    Laissez vide pour un code sans expiration
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <a href="{{ route('admin.cash-codes.index') }}" class="btn-cancel">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Annuler
            </a>
            <button type="submit" class="btn-submit">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Créer le code
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const tauxConversion = {{ $tauxConversion ?? 650 }};
    let users = @json($users ?? []);

    function calculatePoints(montant) {
        const points = Math.floor(montant / tauxConversion);
        document.getElementById('previewPoints').textContent = points.toLocaleString('fr-FR');
        
        if (montant > 0) {
            document.getElementById('calculationInfo').style.display = 'block';
            document.getElementById('calculationDetail').textContent = 
                `${parseInt(montant).toLocaleString('fr-FR')} FCFA ÷ ${tauxConversion} = ${points} points`;
        } else {
            document.getElementById('calculationInfo').style.display = 'none';
        }

        // Générer aperçu du code
        const randomCode = 'CASH-' + Math.random().toString(36).substring(2, 10).toUpperCase();
        document.getElementById('previewCode').textContent = randomCode;
    }

    function setAmount(amount) {
        document.getElementById('montantFcfa').value = amount;
        calculatePoints(amount);
        
        // Visual feedback
        document.querySelectorAll('.quick-amount-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
    }

    // Recherche d'utilisateurs
    const userSearch = document.getElementById('userSearch');
    const searchResults = document.getElementById('searchResults');

    userSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        
        if (query.length < 2) {
            searchResults.classList.remove('active');
            return;
        }

        const filtered = users.filter(user => 
            user.prenom.toLowerCase().includes(query) ||
            user.nom.toLowerCase().includes(query) ||
            (user.telephone && user.telephone.includes(query))
        );

        if (filtered.length > 0) {
            searchResults.innerHTML = filtered.map(user => `
                <div class="user-search-item" onclick="selectUser(${user.id}, '${user.prenom}', '${user.nom}', '${user.telephone}')">
                    <div style="font-weight: 500;">${user.prenom} ${user.nom}</div>
                    <div style="font-size: 0.75rem; color: var(--gray-600);">${user.telephone || 'Pas de téléphone'}</div>
                </div>
            `).join('');
            searchResults.classList.add('active');
        } else {
            searchResults.innerHTML = '<div class="user-search-item" style="color: var(--gray-600); text-align: center;">Aucun utilisateur trouvé</div>';
            searchResults.classList.add('active');
        }
    });

    function selectUser(id, prenom, nom, telephone) {
        document.getElementById('selectedUserId').value = id;
        document.getElementById('selectedUserAvatar').textContent = 
            prenom.charAt(0).toUpperCase() + nom.charAt(0).toUpperCase();
        document.getElementById('selectedUserName').textContent = `${prenom} ${nom}`;
        document.getElementById('selectedUserPhone').textContent = telephone || 'Pas de téléphone';
        
        document.getElementById('selectedUserDisplay').style.display = 'flex';
        document.getElementById('userSearch').value = '';
        searchResults.classList.remove('active');
    }

    function clearUserSelection() {
        document.getElementById('selectedUserId').value = '';
        document.getElementById('selectedUserDisplay').style.display = 'none';
    }

    // Fermer les résultats en cliquant ailleurs
    document.addEventListener('click', function(e) {
        if (!userSearch.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.remove('active');
        }
    });

    // Initialiser avec une valeur par défaut si existe
    @if(old('montant_fcfa'))
        calculatePoints({{ old('montant_fcfa') }});
    @endif
</script>
@endpush