<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription — Elite 2.0</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .card {
      background: #fff;
      border-radius: 24px;
      padding: 40px 36px;
      width: 100%;
      max-width: 480px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .logo {
      text-align: center;
      margin-bottom: 28px;
    }
    .logo h1 {
      font-size: 28px;
      font-weight: 800;
      background: linear-gradient(135deg, #4F46E5, #7C3AED);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .logo p { color: #6B7280; font-size: 14px; margin-top: 4px; }

    .referral-banner {
      background: linear-gradient(135deg, #ECFDF5, #D1FAE5);
      border: 1px solid #6EE7B7;
      border-radius: 12px;
      padding: 12px 16px;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .referral-banner .icon { font-size: 20px; }
    .referral-banner .text { font-size: 13px; color: #065F46; line-height: 1.4; }
    .referral-banner strong { color: #047857; }

    .tabs {
      display: flex;
      background: #F3F4F6;
      border-radius: 12px;
      padding: 4px;
      margin-bottom: 28px;
    }
    .tab {
      flex: 1;
      padding: 10px;
      text-align: center;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      color: #6B7280;
      cursor: pointer;
      transition: all 0.2s;
      border: none;
      background: none;
    }
    .tab.active { background: #fff; color: #4F46E5; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

    .form-group { margin-bottom: 16px; }
    label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    input, select {
      width: 100%;
      padding: 12px 16px;
      border: 1.5px solid #E5E7EB;
      border-radius: 12px;
      font-size: 15px;
      color: #111827;
      transition: border-color 0.2s;
      outline: none;
    }
    input:focus, select:focus { border-color: #4F46E5; }
    input.readonly { background: #F9FAFB; color: #6B7280; cursor: not-allowed; }

    .referral-code-field {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .referral-code-field input { flex: 1; }
    .badge-code {
      background: #EEF2FF;
      color: #4F46E5;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
      white-space: nowrap;
    }

    .otp-section { display: none; }
    .otp-section.visible { display: block; }
    .otp-row { display: flex; gap: 8px; }
    .otp-row input { flex: 1; text-align: center; font-size: 20px; font-weight: bold; letter-spacing: 2px; }
    .send-otp-btn {
      padding: 12px 16px;
      border: 1.5px solid #4F46E5;
      border-radius: 12px;
      color: #4F46E5;
      font-weight: 600;
      font-size: 13px;
      background: none;
      cursor: pointer;
      white-space: nowrap;
    }
    .send-otp-btn:disabled { opacity: 0.5; cursor: not-allowed; }

    .btn-primary {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #4F46E5, #7C3AED);
      color: #fff;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 8px;
      transition: opacity 0.2s;
    }
    .btn-primary:hover { opacity: 0.92; }
    .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

    .divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 20px 0;
      color: #9CA3AF;
      font-size: 13px;
    }
    .divider::before, .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #E5E7EB;
    }

    .btn-google {
      width: 100%;
      padding: 13px;
      border: 1.5px solid #E5E7EB;
      border-radius: 12px;
      background: #fff;
      font-size: 15px;
      font-weight: 600;
      color: #374151;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: background 0.2s;
      margin-bottom: 10px;
    }
    .btn-google:hover { background: #F9FAFB; }

    .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 14px; }
    .alert-error { background: #FEF2F2; color: #B91C1C; border: 1px solid #FCA5A5; }
    .alert-success { background: #ECFDF5; color: #065F46; border: 1px solid #6EE7B7; }

    .terms { font-size: 12px; color: #9CA3AF; text-align: center; margin-top: 16px; }
    .terms a { color: #4F46E5; }

    @media (max-width: 480px) {
      .card { padding: 28px 20px; }
    }
  </style>
</head>
<body>
<div class="card">
  <div class="logo">
    <h1>Elite 2.0</h1>
    <p>Votre plateforme de formation professionnelle</p>
  </div>

  @if($referralCode && $parrainName)
  <div class="referral-banner">
    <span class="icon">🎉</span>
    <div class="text">
      Vous avez été invité(e) par <strong>{{ $parrainName }}</strong>.<br>
      Inscrivez-vous et bénéficiez jusqu'à <strong>3 Millions FCFA de subvention</strong> !
    </div>
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-error">{{ session('error') }}</div>
  @endif

  <!-- Onglets -->
  <div class="tabs">
    <button class="tab active" onclick="switchTab('phone')">📱 Téléphone</button>
    <button class="tab" onclick="switchTab('email')">📧 Email</button>
  </div>

  <!-- Connexion Google -->
  <a href="{{ route('web.auth.google', $referralCode ? ['ref' => $referralCode] : []) }}" style="text-decoration:none;">
    <button type="button" class="btn-google">
      <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
      Continuer avec Google
    </button>
  </a>

  <div class="divider">ou remplissez le formulaire</div>

  <!-- Formulaire -->
  <form id="registerForm" action="{{ route('web.register.submit') }}" method="POST">
    @csrf

    <div class="form-group">
      <label>Prénom *</label>
      <input type="text" name="prenom" required placeholder="Votre prénom" value="{{ old('prenom') }}">
    </div>

    <div class="form-group">
      <label>Nom *</label>
      <input type="text" name="nom" required placeholder="Votre nom" value="{{ old('nom') }}">
    </div>

    <!-- Champ téléphone (tab phone) -->
    <div id="phone-fields">
      <div class="form-group">
        <label>Numéro de téléphone *</label>
        <div class="otp-row">
          <input type="tel" name="telephone" id="telephone" placeholder="6XXXXXXXX" value="{{ old('telephone') }}">
          <button type="button" class="send-otp-btn" id="sendOtpBtn" onclick="sendOtp()">Envoyer OTP</button>
        </div>
      </div>
      <div class="form-group otp-section" id="otpSection">
        <label>Code OTP reçu *</label>
        <input type="text" name="otp_code" id="otpCode" placeholder="_ _ _ _ _ _" maxlength="6">
        <input type="hidden" name="otp_verified" id="otpVerified" value="0">
      </div>
    </div>

    <!-- Champ email (tab email) -->
    <div id="email-fields" style="display:none">
      <div class="form-group">
        <label>Email *</label>
        <input type="email" name="email" placeholder="votre@email.com" value="{{ old('email') }}">
      </div>
    </div>

    <div class="form-group">
      <label>Dernier diplôme *</label>
      <select name="dernier_diplome" required>
        <option value="">Sélectionner...</option>
        <option value="BEPC">BEPC</option>
        <option value="Probatoire">Probatoire</option>
        <option value="BAC">BAC</option>
        <option value="BTS">BTS</option>
        <option value="Licence">Licence</option>
        <option value="Master">Master</option>
        <option value="Doctorat">Doctorat</option>
        <option value="Autre">Autre</option>
      </select>
    </div>

    <div class="form-group">
      <label>Ville *</label>
      <input type="text" name="ville" required placeholder="Votre ville" value="{{ old('ville') }}">
    </div>

    <div class="form-group">
      <label>Mot de passe *</label>
      <input type="password" name="password" required placeholder="Minimum 6 caractères">
    </div>

    <div class="form-group">
      <label>Confirmer le mot de passe *</label>
      <input type="password" name="password_confirmation" required placeholder="Répétez le mot de passe">
    </div>

    <!-- Code de parrainage (pré-rempli depuis l'URL) -->
    <div class="form-group">
      <label>Code d'invitation</label>
      <div class="referral-code-field">
        <input
          type="text"
          name="referral_code"
          id="referralCodeInput"
          value="{{ $referralCode }}"
          placeholder="Optionnel"
          {{ $referralCode ? 'class=readonly readonly' : '' }}
        >
        @if($referralCode)
          <span class="badge-code">✓ Appliqué</span>
        @endif
      </div>
    </div>

    <button type="submit" class="btn-primary" id="submitBtn">
      Créer mon compte
    </button>
  </form>

  <p class="terms">
    En vous inscrivant, vous acceptez nos
    <a href="#">Conditions d'utilisation</a> et notre
    <a href="#">Politique de confidentialité</a>.
  </p>
</div>

<script>
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  function switchTab(tab) {
    document.querySelectorAll('.tab').forEach((t, i) => {
      t.classList.toggle('active', (i === 0 && tab === 'phone') || (i === 1 && tab === 'email'));
    });
    document.getElementById('phone-fields').style.display = tab === 'phone' ? 'block' : 'none';
    document.getElementById('email-fields').style.display = tab === 'email' ? 'block' : 'none';
  }

  let otpSent = false;
  let otpVerified = false;

  async function sendOtp() {
    const telephone = document.getElementById('telephone').value;
    if (!telephone) { alert('Veuillez saisir votre numéro de téléphone.'); return; }

    const btn = document.getElementById('sendOtpBtn');
    btn.disabled = true;
    btn.textContent = 'Envoi...';

    try {
      const res = await fetch('{{ route("web.send-otp") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ telephone }),
      });
      const data = await res.json();
      if (data.success) {
        document.getElementById('otpSection').classList.add('visible');
        btn.textContent = 'Renvoi (60s)';
        let countdown = 60;
        const interval = setInterval(() => {
          countdown--;
          btn.textContent = `Renvoi (${countdown}s)`;
          if (countdown <= 0) {
            clearInterval(interval);
            btn.disabled = false;
            btn.textContent = 'Envoyer OTP';
          }
        }, 1000);
        otpSent = true;
        // En dev, afficher le code OTP
        if (data.dev_otp) alert(`DEV MODE — OTP : ${data.dev_otp}`);
      } else {
        alert('Erreur : ' + data.message);
        btn.disabled = false;
        btn.textContent = 'Envoyer OTP';
      }
    } catch(e) {
      alert('Erreur réseau.');
      btn.disabled = false;
      btn.textContent = 'Envoyer OTP';
    }
  }

  // Vérification OTP en temps réel
  document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otpCode');
    if (otpInput) {
      otpInput.addEventListener('input', async function() {
        if (this.value.length === 6) {
          const telephone = document.getElementById('telephone').value;
          const res = await fetch('{{ route("web.verify-otp") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ otp: this.value, telephone }),
          });
          const data = await res.json();
          if (data.success) {
            this.style.borderColor = '#10B981';
            document.getElementById('otpVerified').value = '1';
            otpVerified = true;
          } else {
            this.style.borderColor = '#EF4444';
          }
        }
      });
    }
  });
</script>
</body>
</html>