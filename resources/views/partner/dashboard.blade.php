@extends('partner.layouts.app')

@section('title', 'Tableau de bord Partenaire')

@section('content')
<div class="space-y-8">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-r from-[#040D24] via-[#0A1535] to-[#1A3A8F] rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-bold uppercase tracking-wider mb-2 border border-blue-400/20">
                    Centre Partenaire Agréé
                </span>
                <h1 class="text-3xl font-extrabold tracking-tight">{{ $partner->nom }}</h1>
                <p class="text-slate-300 text-sm mt-1">Gérez vos apprenants, la comptabilité des 5 tranches et les plages horaires d'ouverture des cours.</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/15 text-center min-w-[200px]">
                <span class="text-xs text-slate-300 uppercase font-semibold tracking-wider block">Code Partenaire Unique</span>
                <span class="text-2xl font-black text-emerald-400 font-mono tracking-wider">{{ $partner->code_partenaire ?: 'NON DÉFINI' }}</span>
                <p class="text-[11px] text-slate-300 mt-1">À donner aux apprenants à l'inscription</p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Apprenants rattachés</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 mt-3">{{ $totalLearners }}</div>
            <div class="text-xs text-slate-500 mt-1">
                <span class="text-emerald-600 font-bold">{{ $activeLearners }} actif(s)</span> · {{ $failedLearners }} échec(s)
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Encaissé</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-700 mt-3">{{ number_format($totalEncaisse, 0, ',', ' ') }} <span class="text-sm font-semibold">FCFA</span></div>
            <div class="text-xs text-slate-500 mt-1">Tranches validées</div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Reste à Recouvrer</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-700 mt-3">{{ number_format($totalEnAttente + $totalEnRetard, 0, ',', ' ') }} <span class="text-sm font-semibold">FCFA</span></div>
            <div class="text-xs text-rose-600 font-semibold mt-1">{{ number_format($totalEnRetard, 0, ',', ' ') }} FCFA en retard</div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Formations & Plans</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
            </div>
            <div class="text-3xl font-black text-slate-900 mt-3">{{ $partner->paymentPlans->count() }}</div>
            <div class="text-xs text-slate-500 mt-1">Échéanciers actifs configurés</div>
        </div>
    </div>

    <!-- Quick Access Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <a href="{{ route('partner.comptabilite') }}" class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-md transition group">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xl group-hover:scale-105 transition">
                    💰
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 group-hover:text-blue-600 transition">Comptabilité des 5 Tranches</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Suivi des paiements, retards et encaissements guichet.</p>
                </div>
            </div>
        </a>

        <a href="{{ route('partner.plans') }}" class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-md transition group">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl group-hover:scale-105 transition">
                    📋
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 transition">Paramétrer les Échéanciers</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Définir les délais en jours des 5 tranches par pack.</p>
                </div>
            </div>
        </a>

        <a href="{{ route('partner.schedules') }}" class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-md transition group">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xl group-hover:scale-105 transition">
                    ⏰
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 group-hover:text-emerald-600 transition">Horaires d'Ouverture des Cours</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Programmer les créneaux d'accès pour vos élèves.</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Two-column widget area -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Learners -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900">Derniers apprenants rattachés</h2>
                <a href="{{ route('partner.comptabilite') }}" class="text-xs text-blue-600 hover:underline font-bold">Voir tous</a>
            </div>

            @if($partner->learners->isEmpty())
                <p class="text-xs text-slate-400 py-6 text-center">Aucun apprenant n'a encore utilisé votre code partenaire.</p>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($partner->learners->take(5) as $learner)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-sm text-slate-900">{{ $learner->full_name }}</div>
                                <div class="text-xs text-slate-500">{{ $learner->telephone ?: $learner->email }} · {{ $learner->ville ?: 'Ville non renseignée' }}</div>
                            </div>
                            <div>
                                @if($learner->formation_status === 'active')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Actif</span>
                                @elseif($learner->formation_status === 'complete')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Terminé</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Échoué</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Course Schedules -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900">Prochains créneaux de cours</h2>
                <a href="{{ route('partner.schedules') }}" class="text-xs text-blue-600 hover:underline font-bold">Gérer</a>
            </div>

            @if($schedules->isEmpty())
                <p class="text-xs text-slate-400 py-6 text-center">Aucun créneau horaire programmé.</p>
            @else
                <div class="space-y-3">
                    @foreach($schedules as $sched)
                        <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-xs text-slate-900">{{ $sched->pack->nom }}</div>
                                <div class="text-[11px] text-slate-500">
                                    {{ $sched->lesson ? $sched->lesson->titre : 'Toutes les leçons du pack' }}
                                </div>
                                <div class="text-[11px] text-blue-600 font-semibold mt-1">
                                    {{ \Carbon\Carbon::parse($sched->starts_at)->translatedFormat('d M Y à H:i') }}
                                    @if($sched->ends_at) ➔ {{ \Carbon\Carbon::parse($sched->ends_at)->format('H:i') }} @endif
                                </div>
                            </div>
                            <div>
                                @if($sched->isOpen())
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500 text-white animate-pulse">Ouvert</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700">Fermé</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection