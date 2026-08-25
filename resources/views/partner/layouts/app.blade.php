<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Espace Partenaire') - Elite 2.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
    </style>
</head>
<body class="min-h-screen text-slate-800 flex flex-col">
    <!-- Navbar -->
    <header class="bg-[#040D24] text-white border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('partner.dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-500 flex items-center justify-center font-black text-white text-xl shadow-md">
                            E
                        </div>
                        <div>
                            <span class="text-lg font-bold tracking-tight text-white">ELITE 2.0</span>
                            <span class="text-xs block text-blue-400 font-semibold uppercase tracking-wider">Espace Partenaire</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('partner.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('partner.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                        Tableau de bord
                    </a>
                    <a href="{{ route('partner.comptabilite') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('partner.comptabilite') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                        Comptabilité & Apprenants
                    </a>
                    <a href="{{ route('partner.plans') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('partner.plans*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                        Échéanciers (5 Tranches)
                    </a>
                    <a href="{{ route('partner.schedules') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('partner.schedules*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                        Horaires de cours
                    </a>
                    <a href="{{ route('partner.centres') }}" class="px-3 py-2 rounded-lg text-sm font-semibold {{ request()->routeIs('partner.centres') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }} transition">
                        Centres Elite
                    </a>
                </nav>

                <!-- User Profile & Logout -->
                <div class="flex items-center space-x-3">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold text-white">{{ auth('partner_web')->user()->nom }}</div>
                        <div class="text-xs text-emerald-400 font-mono font-semibold">Code: {{ auth('partner_web')->user()->code_partenaire ?: '-' }}</div>
                    </div>
                    <form method="POST" action="{{ route('partner.logout') }}">
                        @csrf
                        <button type="submit" class="px-3 py-2 rounded-lg text-sm font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20 hover:bg-rose-500 hover:text-white transition">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Alerts -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-sm">
                <div class="flex items-center mb-1">
                    <svg class="w-5 h-5 text-rose-600 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-bold">Veuillez corriger les erreurs suivantes :</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1 pl-6 text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Elite 2.0 · Plateforme d'Orientation, Formation & Insertion Professionnelle. Tous droits réservés.
    </footer>
</body>
</html>
