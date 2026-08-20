@extends('opportunities.layouts.elite-base')
@section('title', 'Emplois & Concours — Elite 2.0')

@section('content')

{{-- Hero header --}}
<div class="relative overflow-hidden rounded-2xl mb-8" style="background: linear-gradient(160deg, #040D24 0%, #0A1535 40%, #0D2060 70%, #1A3A8F 100%);">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -left-20 top-0 w-1/2 h-full opacity-10" style="background:rgba(46,108,184,0.5);transform:skewX(18deg);"></div>
        <div class="absolute -right-20 top-0 w-1/2 h-full opacity-10" style="background:rgba(46,108,184,0.5);transform:skewX(-18deg);"></div>
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-48 rounded-full opacity-10" style="background:radial-gradient(circle,#4A90D9,transparent);"></div>
    </div>

    <svg class="absolute right-6 top-4 opacity-[0.07] w-24 h-24 pointer-events-none" viewBox="0 0 80 90" fill="white">
        <ellipse cx="40" cy="35" rx="22" ry="26"/>
        <ellipse cx="40" cy="67" rx="9" ry="14"/>
    </svg>

    <div class="relative z-10 px-6 py-8">
        <p class="text-xs font-semibold tracking-widest uppercase mb-2" style="color:rgba(255,255,255,0.45);">Elite 2.0</p>
        <div class="flex items-center gap-3 mb-2">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:rgba(255,255,255,0.10);border:1.5px solid rgba(255,255,255,0.14);">
                <i class="fas fa-rocket text-lg text-white"></i>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Opportunités</h1>
        </div>
        <p class="text-sm" style="color:rgba(255,255,255,0.50);">Emplois et concours disponibles pour vous</p>
    </div>

    {{-- Toggle intégré dans le header --}}
    <div class="relative z-10 px-6 pb-6 flex gap-3">
        <button onclick="toggleSection('emplois')" id="btn-emplois"
                class="flex-1 py-2.5 rounded-xl text-sm font-bold transition-all"
                style="background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.30);color:#fff;">
            <i class="fas fa-briefcase mr-1.5"></i>Emplois
            <span class="ml-1.5 text-xs px-2 py-0.5 rounded-full" style="background:rgba(255,255,255,0.20);">{{ count($emplois) }}</span>
        </button>
        <button onclick="toggleSection('concours')" id="btn-concours"
                class="flex-1 py-2.5 rounded-xl text-sm font-bold transition-all"
                style="background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.12);color:rgba(255,255,255,0.50);">
            <i class="fas fa-trophy mr-1.5"></i>Concours
            <span class="ml-1.5 text-xs px-2 py-0.5 rounded-full" style="background:rgba(255,255,255,0.10);">{{ count($concours) }}</span>
        </button>
    </div>

    <div style="height:3px;background:#2E6CB8;box-shadow:0 0 8px rgba(74,144,217,0.7);"></div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- SECTION EMPLOIS                             --}}
{{-- ═══════════════════════════════════════════ --}}
<div id="section-emplois" class="space-y-4">
    @forelse($emplois as $job)
    <div class="bg-white rounded-2xl overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"
         style="border:1.5px solid #EBF2FF;box-shadow:0 2px 8px rgba(26,58,143,0.06);">
        <div class="flex">
            {{-- Bandeau latéral bleu --}}
            <div class="w-1.5 rounded-l-2xl flex-shrink-0" style="background:linear-gradient(180deg,#1A3A8F,#2E6CB8);"></div>

            <div class="flex-1 p-5">
                {{-- Badges --}}
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg"
                          style="background:#EBF2FF;color:#1A3A8F;border:1px solid #D4E3F5;">
                        <i class="fas fa-file-contract mr-1"></i>{{ $job->type_contrat }}
                    </span>
                    @if($job->date_limite && \Carbon\Carbon::parse($job->date_limite)->isFuture())
                        <span class="text-xs font-medium px-2.5 py-1 rounded-lg"
                              style="background:#F8FAFF;color:#7FA5D0;border:1px solid #EBF2FF;">
                            <i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($job->date_limite)->diffForHumans() }}
                        </span>
                    @endif
                </div>

                {{-- Titre --}}
                <h3 class="font-black text-lg leading-snug mb-1" style="color:#0A1535;">{{ $job->titre }}</h3>

                {{-- Entreprise & ville --}}
                <p class="text-sm font-semibold mb-0.5" style="color:#1A3A8F;">
                    <i class="fas fa-building mr-1 opacity-60"></i>{{ $job->entreprise }}
                </p>
                <p class="text-xs mb-3" style="color:#7FA5D0;">
                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $job->ville }}
                </p>

                {{-- Description --}}
                <p class="text-sm leading-relaxed line-clamp-3" style="color:#4A6FA8;">{{ $job->description }}</p>

                {{-- Salaire --}}
                @if($job->salaire_min || $job->salaire_max)
                <div class="mt-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl"
                          style="background:#EBF2FF;color:#1A3A8F;border:1px solid #D4E3F5;">
                        <i class="fas fa-money-bill-wave" style="color:#F5A623;"></i>
                        @if($job->salaire_min && $job->salaire_max)
                            {{ number_format($job->salaire_min,0,',',' ') }} – {{ number_format($job->salaire_max,0,',',' ') }} FCFA
                        @elseif($job->salaire_min)
                            À partir de {{ number_format($job->salaire_min,0,',',' ') }} FCFA
                        @else
                            Jusqu'à {{ number_format($job->salaire_max,0,',',' ') }} FCFA
                        @endif
                    </span>
                </div>
                @endif

                {{-- Actions --}}
                <div class="mt-4 flex flex-wrap gap-2">
                    @if($job->contact_email)
                    <a href="mailto:{{ $job->contact_email }}"
                       class="inline-flex items-center gap-1.5 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all hover:opacity-90"
                       style="background:linear-gradient(135deg,#1A3A8F,#2E6CB8);">
                        <i class="fas fa-envelope"></i>Postuler par email
                    </a>
                    @endif
                    @if($job->contact_telephone)
                    <a href="tel:{{ $job->contact_telephone }}"
                       class="inline-flex items-center gap-1.5 text-xs font-bold px-4 py-2.5 rounded-xl transition-all hover:opacity-80"
                       style="background:#F8FAFF;color:#1A3A8F;border:1.5px solid #D4E3F5;">
                        <i class="fas fa-phone"></i>{{ $job->contact_telephone }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-20">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5" style="background:#EBF2FF;">
            <i class="fas fa-briefcase text-3xl" style="color:#7FA5D0;"></i>
        </div>
        <p class="font-bold text-gray-500 text-lg">Aucune offre d'emploi pour le moment</p>
        <p class="text-gray-400 text-sm mt-1">Revenez prochainement pour de nouvelles offres.</p>
    </div>
    @endforelse
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- SECTION CONCOURS                            --}}
{{-- ═══════════════════════════════════════════ --}}
<div id="section-concours" class="space-y-4 hidden">
    @forelse($concours as $c)
    <div class="bg-white rounded-2xl overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg"
         style="border:1.5px solid #EBF2FF;box-shadow:0 2px 8px rgba(26,58,143,0.06);">
        <div class="flex">
            {{-- Bandeau latéral or --}}
            <div class="w-1.5 rounded-l-2xl flex-shrink-0" style="background:linear-gradient(180deg,#F5A623,#e0841a);"></div>

            <div class="flex-1 p-5">
                {{-- Badges --}}
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg"
                          style="background:#FFFBF0;color:#92400E;border:1px solid #F5A62340;">
                        <i class="fas fa-trophy mr-1" style="color:#F5A623;"></i>Concours
                    </span>
                    @if($c->date_limite_inscription && \Carbon\Carbon::parse($c->date_limite_inscription)->isFuture())
                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg animate-pulse"
                              style="background:#FFF0E6;color:#C2410C;border:1px solid #FECDAA;">
                            <i class="fas fa-fire mr-1"></i>Inscriptions ouvertes
                        </span>
                    @endif
                </div>

                {{-- Titre & organisateur --}}
                <h3 class="font-black text-lg leading-snug mb-1" style="color:#0A1535;">{{ $c->titre }}</h3>
                <p class="text-sm font-semibold mb-3" style="color:#1A3A8F;">
                    <i class="fas fa-university mr-1 opacity-60"></i>{{ $c->organisateur }}
                </p>
                <p class="text-sm leading-relaxed" style="color:#4A6FA8;">{{ $c->description }}</p>

                {{-- Dates --}}
                <div class="mt-4 grid grid-cols-3 gap-2">
                    <div class="text-center rounded-xl py-3" style="background:#F8FAFF;border:1px solid #EBF2FF;">
                        <p class="text-[10px] font-bold uppercase tracking-wide mb-1" style="color:#7FA5D0;">Début</p>
                        <p class="text-xs font-black" style="color:#0A1535;">{{ \Carbon\Carbon::parse($c->date_debut)->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-center rounded-xl py-3" style="background:#F8FAFF;border:1px solid #EBF2FF;">
                        <p class="text-[10px] font-bold uppercase tracking-wide mb-1" style="color:#7FA5D0;">Fin</p>
                        <p class="text-xs font-black" style="color:#0A1535;">{{ \Carbon\Carbon::parse($c->date_fin)->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-center rounded-xl py-3" style="background:#FFFBF0;border:1px solid #F5A62340;">
                        <p class="text-[10px] font-bold uppercase tracking-wide mb-1" style="color:#F5A623;">Limite</p>
                        <p class="text-xs font-black" style="color:#92400E;">{{ \Carbon\Carbon::parse($c->date_limite_inscription)->format('d/m/Y') }}</p>
                    </div>
                </div>

                {{-- Conditions --}}
                @if($c->conditions)
                <div class="mt-3 rounded-xl p-3" style="background:#EBF2FF;border:1px solid #D4E3F5;">
                    <p class="text-xs leading-relaxed" style="color:#1A3A8F;">
                        <i class="fas fa-info-circle mr-1.5" style="color:#2E6CB8;"></i>
                        <strong>Conditions :</strong> {{ $c->conditions }}
                    </p>
                </div>
                @endif

                {{-- CTA --}}
                @if($c->lien_inscription)
                <div class="mt-4">
                    <a href="{{ $c->lien_inscription }}" target="_blank"
                       class="inline-flex items-center gap-2 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all hover:opacity-90 shadow-sm"
                       style="background:linear-gradient(135deg,#1A3A8F,#2E6CB8);">
                        <i class="fas fa-external-link-alt"></i>S'inscrire au concours
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-20">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5" style="background:#FFFBF0;">
            <i class="fas fa-trophy text-3xl" style="color:#F5A623;opacity:0.6;"></i>
        </div>
        <p class="font-bold text-gray-500 text-lg">Aucun concours disponible</p>
        <p class="text-gray-400 text-sm mt-1">Revenez prochainement pour de nouveaux concours.</p>
    </div>
    @endforelse
</div>

{{-- Points de progression --}}
<div class="flex justify-center gap-1.5 py-10">
    <div class="w-2 h-2 rounded-full" style="background:#1A3A8F;"></div>
    <div class="w-5 h-2 rounded-full" style="background:#2E6CB8;"></div>
    <div class="w-2 h-2 rounded-full" style="background:#1A3A8F;"></div>
</div>

@push('scripts')
<script>
function toggleSection(s) {
    const emplois  = document.getElementById('section-emplois');
    const concours = document.getElementById('section-concours');
    const be       = document.getElementById('btn-emplois');
    const bc       = document.getElementById('btn-concours');

    emplois.classList.toggle('hidden', s !== 'emplois');
    concours.classList.toggle('hidden', s !== 'concours');

    const activeStyle   = 'background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.30);color:#fff;';
    const inactiveStyle = 'background:rgba(255,255,255,0.06);border:1.5px solid rgba(255,255,255,0.12);color:rgba(255,255,255,0.50);';

    be.style.cssText = s === 'emplois'  ? activeStyle : inactiveStyle;
    bc.style.cssText = s === 'concours' ? activeStyle : inactiveStyle;
}
</script>
@endpush

@endsection