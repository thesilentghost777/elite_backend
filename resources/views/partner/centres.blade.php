@extends('partner.layouts.app')

@section('title', 'Annuaire des Centres Partenaires Elite')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-bold uppercase tracking-wider mb-2">
                Réseau National CFPAM / Elite
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Centres Partenaires Agréés</h1>
            <p class="text-sm text-slate-500 mt-1">Consultez l'ensemble des centres et instituts partenaires connectés à l'écosystème Elite 2.0.</p>
        </div>
        <div class="bg-white px-4 py-2.5 rounded-xl border border-slate-200 shadow-sm text-center">
            <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Total Centres</span>
            <span class="text-xl font-black text-blue-600">{{ $centres->count() }} centres actifs</span>
        </div>
    </div>

    <!-- Grille des Centres -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($centres as $centre)
            @php
                $isCurrent = (auth('partner_web')->check() && auth('partner_web')->user()->id === $centre->id);
            @endphp
            <div class="bg-white rounded-2xl border {{ $isCurrent ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-slate-200' }} p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <!-- Badge & Code -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center font-black text-base shadow-sm">
                            {{ substr($centre->nom, 0, 1) }}
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $centre->code_partenaire ?: 'SANS CODE' }}
                            </span>
                            @if($isCurrent)
                                <span class="block text-[10px] text-blue-600 font-bold mt-1">● Votre Centre</span>
                            @endif
                        </div>
                    </div>

                    <!-- Nom & Contact -->
                    <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $centre->nom }}</h3>
                    <div class="mt-4 space-y-2 text-xs text-slate-600">
                        @if($centre->telephone)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>{{ $centre->telephone }}</span>
                            </div>
                        @endif

                        @if($centre->email)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span class="truncate">{{ $centre->email }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span>Partenaire Agréé</span>
                    <span>Depuis {{ $centre->created_at ? $centre->created_at->format('M Y') : '2026' }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection