<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Code de vérification</title>
  <style>
    body { font-family: Arial, sans-serif; background: #F5F6FA; margin: 0; padding: 0; }
    .wrapper { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
    .header { background: linear-gradient(135deg, #6D28D9, #7C3AED); padding: 32px; text-align: center; }
    .header h1 { color: #fff; margin: 0; font-size: 22px; }
    .header p  { color: rgba(255,255,255,.8); margin: 6px 0 0; font-size: 13px; }
    .body { padding: 32px; text-align: center; }
    .body p { color: #374151; font-size: 15px; margin: 0 0 24px; }
    .code-box { display: inline-block; background: #EFF6FF; border: 2px dashed #6D28D9; border-radius: 12px; padding: 16px 40px; margin-bottom: 24px; }
    .code { font-size: 36px; font-weight: 700; color: #6D28D9; letter-spacing: 10px; }
    .note { font-size: 13px; color: #9CA3AF; }
    .footer { background: #F9FAFB; padding: 16px; text-align: center; font-size: 12px; color: #9CA3AF; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <h1>Elite 2.0</h1>
      <p>Vérification de votre adresse email</p>
    </div>
    <div class="body">
      <p>Bonjour <strong>{{ $prenom }}</strong>,<br>Voici votre code de vérification :</p>
      <div class="code-box">
        <div class="code">{{ $code }}</div>
      </div>
      <p class="note">Ce code est valable <strong>10 minutes</strong>.<br>Si vous n'avez pas demandé ce code, ignorez cet email.</p>
    </div>
    <div class="footer">© {{ date('Y') }} Elite 2.0 — Tous droits réservés</div>
  </div>
</body>
</html>