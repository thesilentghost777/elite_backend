@extends('opportunities.layouts.elite-base')
@section('title', 'Financements — Elite 2.0')

@section('content')

{{-- Hero header --}}
<div class="relative overflow-hidden rounded-2xl mb-8" style="background: linear-gradient(160deg, #040D24 0%, #0A1535 40%, #0D2060 70%, #1A3A8F 100%);">

    {{-- Panneaux diagonaux --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -left-20 top-0 w-1/2 h-full opacity-10" style="background:rgba(46,108,184,0.5);transform:skewX(18deg);"></div>
        <div class="absolute -right-20 top-0 w-1/2 h-full opacity-10" style="background:rgba(46,108,184,0.5);transform:skewX(-18deg);"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-48 rounded-full opacity-10" style="background:radial-gradient(circle,#4A90D9,transparent);"></div>
    </div>

    {{-- Filigrane Afrique --}}
    <svg class="absolute right-6 top-4 opacity-[0.07] w-24 h-24 pointer-events-none" viewBox="0 0 80 90" fill="white">
        <ellipse cx="40" cy="35" rx="22" ry="26"/>
        <ellipse cx="40" cy="67" rx="9" ry="14"/>
    </svg>

    <div class="relative z-10 px-6 py-8">
        <p class="text-xs font-semibold tracking-widest uppercase mb-2" style="color:rgba(255,255,255,0.45);">Elite 2.0</p>
        <div class="flex items-center gap-3 mb-2">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:rgba(255,255,255,0.10);border:1.5px solid rgba(255,255,255,0.14);">
                <i class="fas fa-coins text-lg" style="color:#F5A623;"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Financements</h1>
        </div>
        <p class="text-sm" style="color:rgba(255,255,255,0.50);">Bourses, subventions et opportunités de financement</p>
    </div>

    {{-- Liseré bleu vif --}}
    <div style="height:3px;background:#2E6CB8;box-shadow:0 0 8px rgba(74,144,217,0.7);"></div>
</div>

{{-- Grille des financements --}}
<div class="space-y-4">
    @forelse($financements as $f)
    @php
        $typeBadge = match($f->type) {
            'bourse'         => ['bg-violet-100 text-violet-800', 'fa-graduation-cap', '#7C3AED'],
            'subvention'     => ['bg-blue-100 text-blue-800',   'fa-hand-holding-usd','#1D4ED8'],
            'pret'           => ['bg-orange-100 text-orange-800','fa-piggy-bank',      '#C2410C'],
            'investissement' => ['bg-emerald-100 text-emerald-800','fa-chart-line',    '#065F46'],
            default          => ['bg-slate-100 text-slate-700', 'fa-coins',            '#475569'],
        };
    @endphp

    <div class="bg-white rounded-2xl overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"
         style="border:1.5px solid #EBF2FF;box-shadow:0 2px 8px rgba(26,58,143,0.06);">

        {{-- Bandeau gauche coloré selon le type --}}
        <div class="flex">
            <div class="w-1.5 rounded-l-2xl flex-shrink-0" style="background:{{ $typeBadge[2] }};"></div>

            <div class="flex-1 p-5">

                {{-- Badges ligne 1 --}}
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="{{ $typeBadge[0] }} text-xs font-bold px-2.5 py-1 rounded-lg">
                        <i class="fas {{ $typeBadge[1] }} mr-1"></i>{{ ucfirst($f->type) }}
                    </span>
                    @if($f->date_limite && \Carbon\Carbon::parse($f->date_limite)->isFuture())
                        <span class="text-xs font-medium px-2.5 py-1 rounded-lg" style="background:#EBF2FF;color:#4A6FA8;">
                            <i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($f->date_limite)->diffForHumans() }}
                        </span>
                    @endif
                </div>

                {{-- Titre & organisme --}}
                <h3 class="font-black text-gray-900 text-lg leading-snug mb-1">{{ $f->titre }}</h3>
                <p class="text-sm font-semibold mb-3" style="color:#1A3A8F;">
                    <i class="fas fa-landmark mr-1 opacity-60"></i>{{ $f->organisme }}
                </p>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $f->description }}</p>

                {{-- Montant --}}
                @if($f->montant_min || $f->montant_max)
                <div class="mt-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl"
                          style="background:#EBF2FF;color:#1A3A8F;border:1px solid #D4E3F5;">
                        <i class="fas fa-money-bill-wave"></i>
                        @if($f->montant_min && $f->montant_max)
                            {{ number_format($f->montant_min,0,',',' ') }} – {{ number_format($f->montant_max,0,',',' ') }} FCFA
                        @elseif($f->montant_min)
                            À partir de {{ number_format($f->montant_min,0,',',' ') }} FCFA
                        @else
                            Jusqu'à {{ number_format($f->montant_max,0,',',' ') }} FCFA
                        @endif
                    </span>
                </div>
                @endif

                {{-- Éligibilité --}}
                @if($f->conditions_eligibilite)
                <div class="mt-3 rounded-xl p-3" style="background:#FFFBF0;border:1px solid #F5A62340;">
                    <p class="text-xs leading-relaxed" style="color:#92400E;">
                        <i class="fas fa-exclamation-triangle mr-1" style="color:#F5A623;"></i>
                        <strong>Éligibilité :</strong> {{ $f->conditions_eligibilite }}
                    </p>
                </div>
                @endif

                {{-- CTA --}}
                <div class="mt-4 flex flex-wrap gap-2">
                    @php
                        $msg = urlencode("Bonjour, je suis intéressé(e) par l'offre de financement \"{$f->titre}\" proposée par {$f->organisme} sur Elite 2.0. Pouvez-vous m'orienter ? Merci !");
                    @endphp
                    <a href="https://wa.me/237659292001?text={{ $msg }}" target="_blank"
                       class="inline-flex items-center gap-2 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all hover:opacity-90 shadow-sm"
                       style="background:#25D366;">
                        <i class="fab fa-whatsapp text-base"></i>Souscrire via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-20">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5" style="background:#EBF2FF;">
            <i class="fas fa-coins text-3xl" style="color:#7FA5D0;"></i>
        </div>
        <p class="font-bold text-gray-500 text-lg">Aucune offre de financement disponible</p>
        <p class="text-gray-400 text-sm mt-1">Revenez prochainement pour de nouvelles opportunités.</p>
    </div>
    @endforelse
</div>

{{-- Points de progression --}}
<div class="flex justify-center gap-1.5 py-10">
    <div class="w-2 h-2 rounded-full" style="background:#1A3A8F;"></div>
    <div class="w-5 h-2 rounded-full" style="background:#2E6CB8;"></div>
    <div class="w-2 h-2 rounded-full" style="background:#1A3A8F;"></div>
</div>

@endsection