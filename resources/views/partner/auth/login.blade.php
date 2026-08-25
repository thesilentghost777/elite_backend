<!doctype html>
<html lang="fr" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion Espace Partenaire - Elite 2.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-[#040D24] via-[#0A1535] to-[#1A3A8F] text-slate-100">
    <div class="max-w-md w-full space-y-8 bg-white/10 backdrop-blur-xl p-8 sm:p-10 rounded-3xl border border-white/15 shadow-2xl">
        <!-- Brand / Header -->
        <div class="text-center">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center font-black text-white text-3xl shadow-lg mx-auto mb-4">
                E
            </div>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">Espace Partenaire</h2>
            <p class="mt-2 text-xs sm:text-sm text-slate-300">
                Connectez-vous pour gérer votre centre, vos apprenants et la comptabilité des tranches.
            </p>
        </div>

        <!-- Erreurs -->
        @if ($errors->any())
            <div class="bg-rose-500/20 border border-rose-500/40 text-rose-200 px-4 py-3 rounded-xl text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Formulaire de Connexion -->
        <form method="POST" action="{{ route('partner.login.submit') }}" class="mt-8 space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Adresse Email du Centre
                </label>
                <input id="email" name="email" type="email" autocomplete="email" required
                    value="{{ old('email') }}"
                    placeholder="directeur@centre-partenaire.com"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent text-sm transition">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Mot de passe
                </label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    placeholder="••••••••••••"
                    class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent text-sm transition">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-sm shadow-lg hover:shadow-blue-500/30 transition transform active:scale-[0.99]">
                    Se connecter à l'Espace Partenaire
                </button>
            </div>
        </form>

        <!-- Footer Help -->
        <div class="text-center pt-4 border-t border-white/10">
            <p class="text-xs text-slate-400">
                Vous n'avez pas encore d'identifiants partenaires ?<br>
                Contactez l'administration centrale Elite / CFPAM.
            </p>
        </div>
    </div>
</body>
</html>