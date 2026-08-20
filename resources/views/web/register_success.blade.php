<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription réussie — Elite 2.0</title>
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
      padding: 48px 36px;
      width: 100%;
      max-width: 440px;
      text-align: center;
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .success-icon {
      width: 80px; height: 80px;
      background: linear-gradient(135deg, #10B981, #059669);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      font-size: 36px;
    }
    h1 { font-size: 26px; font-weight: 800; color: #111827; margin-bottom: 8px; }
    .subtitle { color: #6B7280; font-size: 15px; margin-bottom: 32px; line-height: 1.5; }
    .user-info {
      background: #F9FAFB;
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 28px;
      text-align: left;
    }
    .user-info p { font-size: 14px; color: #374151; margin-bottom: 4px; }
    .user-info strong { color: #111827; }
    .btn-store {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      padding: 16px;
      background: linear-gradient(135deg, #4F46E5, #7C3AED);
      color: #fff;
      border-radius: 16px;
      font-size: 17px;
      font-weight: 700;
      text-decoration: none;
      margin-bottom: 12px;
      transition: opacity 0.2s;
    }
    .btn-store:hover { opacity: 0.92; }
    .store-note { font-size: 13px; color: #9CA3AF; }
    .countdown { font-size: 13px; color: #4F46E5; margin-top: 8px; font-weight: 600; }
  </style>
</head>
<body>
<div class="card">
  <div class="success-icon">✓</div>
  <h1>Compte créé !</h1>
  <p class="subtitle">
    Bienvenue sur Elite 2.0, {{ $user->prenom }} !<br>
    Téléchargez l'application pour commencer votre formation.
  </p>

  <div class="user-info">
    <p>Nom : <strong>{{ $user->prenom }} {{ $user->nom }}</strong></p>
    @if($user->telephone)
    <p>Téléphone : <strong>{{ $user->telephone }}</strong></p>
    @endif
    @if($user->email)
    <p>Email : <strong>{{ $user->email }}</strong></p>
    @endif
    <p>Code d'invitation : <strong>{{ $user->referral_code }}</strong></p>
  </div>

  <a href="{{ $storeUrl }}" class="btn-store" id="storeBtn">
    📱 Télécharger Elite 2.0
  </a>

  <p class="store-note">Vous serez redirigé automatiquement dans <span id="countdown">5</span>s</p>
</div>

<script>
  let count = 5;
  const countdownEl = document.getElementById('countdown');
  const storeUrl = "{{ $storeUrl }}";

  const interval = setInterval(() => {
    count--;
    countdownEl.textContent = count;
    if (count <= 0) {
      clearInterval(interval);
      window.location.href = storeUrl;
    }
  }, 1000);
</script>
</body>
</html>