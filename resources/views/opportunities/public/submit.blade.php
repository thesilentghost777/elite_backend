<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumettre une offre — Elite 2.0</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                elite: {
                    blue: { 500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e3a5f',900:'#0f2440' },
                    green: { 400:'#34d399',500:'#10b981',600:'#059669',700:'#047857' },
                }
            }}}
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Figtree', system-ui, sans-serif; }
        .gradient-elite { background: linear-gradient(135deg, #1e3a5f 0%, #064e3b 100%); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Header --}}
    <div class="gradient-elite text-white py-8 px-4 text-center">
        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-paper-plane text-2xl text-emerald-300"></i>
        </div>
        <h1 class="text-2xl font-black">Soumettre une offre</h1>
        <p class="text-white/70 text-sm mt-1">Partagez vos opportunités avec la communauté Elite 2.0</p>
    </div>

    @if(session('success'))
    <div class="max-w-lg mx-auto px-4 mt-4">
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-500"></i>{{ session('success') }}
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="max-w-lg mx-auto px-4 mt-4">
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    </div>
    @endif

    <div class="max-w-lg mx-auto px-4 py-6">

        {{-- Tabs --}}
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2" id="tabs">
            <button onclick="showTab('emploi')" data-tab="emploi" class="tab-btn active px-4 py-2 rounded-full text-sm font-semibold bg-elite-blue-800 text-white transition whitespace-nowrap">
                <i class="fas fa-briefcase mr-1"></i>Emploi
            </button>
            <button onclick="showTab('concours')" data-tab="concours" class="tab-btn px-4 py-2 rounded-full text-sm font-semibold bg-gray-200 text-gray-600 hover:bg-gray-300 transition whitespace-nowrap">
                <i class="fas fa-trophy mr-1"></i>Concours
            </button>
            <button onclick="showTab('financement')" data-tab="financement" class="tab-btn px-4 py-2 rounded-full text-sm font-semibold bg-gray-200 text-gray-600 hover:bg-gray-300 transition whitespace-nowrap">
                <i class="fas fa-coins mr-1"></i>Financement
            </button>
        </div>

        {{-- EMPLOI FORM --}}
        <form id="form-emploi" action="{{ route('public.submit.job') }}" method="POST" class="space-y-4">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border p-5 space-y-4">
                <h2 class="font-bold text-lg text-elite-blue-800"><i class="fas fa-briefcase mr-2 text-elite-green-500"></i>Offre d'emploi</h2>
                <input name="titre" placeholder="Titre du poste *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 focus:border-transparent outline-none" value="{{ old('titre') }}">
                <textarea name="description" placeholder="Description du poste *" required rows="4" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none resize-none">{{ old('description') }}</textarea>
                <div class="grid grid-cols-2 gap-3">
                    <input name="entreprise" placeholder="Entreprise *" required class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none" value="{{ old('entreprise') }}">
                    <input name="ville" placeholder="Ville *" required class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none" value="{{ old('ville') }}">
                </div>
                <select name="type_contrat" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none text-gray-600">
                    <option value="">Type de contrat *</option>
                    <option value="CDI">CDI</option>
                    <option value="CDD">CDD</option>
                    <option value="Stage">Stage</option>
                    <option value="Freelance">Freelance</option>
                    <option value="Temps partiel">Temps partiel</option>
                </select>
                <div class="grid grid-cols-2 gap-3">
                    <input name="salaire_min" type="number" placeholder="Salaire min (FCFA)" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                    <input name="salaire_max" type="number" placeholder="Salaire max (FCFA)" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                </div>
                <input name="date_limite" type="date" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none text-gray-500">
                <div class="grid grid-cols-2 gap-3">
                    <input name="contact_email" type="email" placeholder="Email de contact" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                    <input name="contact_telephone" placeholder="Téléphone" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                </div>
                <button type="submit" class="w-full gradient-elite text-white font-bold py-3 rounded-xl hover:opacity-90 transition text-sm">
                    <i class="fas fa-paper-plane mr-2"></i>Soumettre l'offre d'emploi
                </button>
            </div>
        </form>

        {{-- CONCOURS FORM --}}
        <form id="form-concours" action="{{ route('public.submit.concours') }}" method="POST" class="space-y-4 hidden">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border p-5 space-y-4">
                <h2 class="font-bold text-lg text-elite-blue-800"><i class="fas fa-trophy mr-2 text-yellow-500"></i>Concours</h2>
                <input name="titre" placeholder="Titre du concours *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                <textarea name="description" placeholder="Description *" required rows="4" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none resize-none"></textarea>
                <input name="organisateur" placeholder="Organisateur *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Date début *</label>
                        <input name="date_debut" type="date" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 mb-1 block">Date fin *</label>
                        <input name="date_fin" type="date" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="text-xs text-gray-500 mb-1 block">Date limite inscription *</label>
                    <input name="date_limite_inscription" type="date" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                </div>
                <textarea name="conditions" placeholder="Conditions de participation" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none resize-none"></textarea>
                <input name="lien_inscription" type="url" placeholder="Lien d'inscription (URL)" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                <button type="submit" class="w-full gradient-elite text-white font-bold py-3 rounded-xl hover:opacity-90 transition text-sm">
                    <i class="fas fa-paper-plane mr-2"></i>Soumettre le concours
                </button>
            </div>
        </form>

        {{-- FINANCEMENT FORM --}}
        <form id="form-financement" action="{{ route('public.submit.financement') }}" method="POST" class="space-y-4 hidden">
            @csrf
            <div class="bg-white rounded-2xl shadow-sm border p-5 space-y-4">
                <h2 class="font-bold text-lg text-elite-blue-800"><i class="fas fa-coins mr-2 text-elite-green-500"></i>Offre de financement</h2>
                <input name="titre" placeholder="Titre *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                <textarea name="description" placeholder="Description *" required rows="4" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none resize-none"></textarea>
                <input name="organisme" placeholder="Organisme / Bailleur *" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                <select name="type" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none text-gray-600">
                    <option value="">Type de financement *</option>
                    <option value="bourse">Bourse</option>
                    <option value="subvention">Subvention</option>
                    <option value="pret">Prêt</option>
                    <option value="investissement">Investissement</option>
                    <option value="autre">Autre</option>
                </select>
                <div class="grid grid-cols-2 gap-3">
                    <input name="montant_min" type="number" placeholder="Montant min (FCFA)" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                    <input name="montant_max" type="number" placeholder="Montant max (FCFA)" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                </div>
                <input name="date_limite" type="date" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                <textarea name="conditions_eligibilite" placeholder="Conditions d'éligibilité" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none resize-none"></textarea>
                <div class="grid grid-cols-2 gap-3">
                    <input name="contact_email" type="email" placeholder="Email" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                    <input name="contact_telephone" placeholder="Téléphone" class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-elite-blue-500 outline-none">
                </div>
                <button type="submit" class="w-full gradient-elite text-white font-bold py-3 rounded-xl hover:opacity-90 transition text-sm">
                    <i class="fas fa-paper-plane mr-2"></i>Soumettre l'offre de financement
                </button>
            </div>
        </form>
    </div>

    <script>
        function showTab(tab) {
            document.querySelectorAll('[id^="form-"]').forEach(f => f.classList.add('hidden'));
            document.getElementById('form-' + tab).classList.remove('hidden');
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-elite-blue-800', 'text-white');
                b.classList.add('bg-gray-200', 'text-gray-600');
            });
            document.querySelector(`[data-tab="${tab}"]`).classList.remove('bg-gray-200', 'text-gray-600');
            document.querySelector(`[data-tab="${tab}"]`).classList.add('bg-elite-blue-800', 'text-white');
        }
    </script>
</body>
</html>
