@extends('opportunities.layouts.elite-base')
@section('title', $livre->titre . ' — Bibliothèque Elite 2.0')

@section('content')

{{-- Retour --}}
<a href="{{ route('elite.bibliotheque') }}"
   class="inline-flex items-center gap-2 text-sm font-semibold px-4 py-2.5 rounded-xl mb-6 transition-all hover:opacity-80"
   style="background:#EBF2FF;color:#1A3A8F;border:1px solid #D4E3F5;">
    <i class="fas fa-arrow-left text-xs"></i>Retour à la bibliothèque
</a>

{{-- Hero header --}}
<div class="relative overflow-hidden rounded-2xl mb-6" style="background: linear-gradient(160deg, #040D24 0%, #0A1535 40%, #0D2060 70%, #1A3A8F 100%);">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -left-20 top-0 w-1/2 h-full opacity-10" style="background:rgba(46,108,184,0.5);transform:skewX(18deg);"></div>
        <div class="absolute -right-20 top-0 w-1/2 h-full opacity-10" style="background:rgba(46,108,184,0.5);transform:skewX(-18deg);"></div>
    </div>
    <svg class="absolute right-6 top-4 opacity-[0.07] w-20 h-20 pointer-events-none" viewBox="0 0 80 90" fill="white">
        <ellipse cx="40" cy="35" rx="22" ry="26"/>
        <ellipse cx="40" cy="67" rx="9" ry="14"/>
    </svg>
    <div class="relative z-10 px-6 py-6">
        <p class="text-xs font-semibold tracking-widest uppercase mb-1" style="color:rgba(255,255,255,0.40);">Bibliothèque</p>
        <h1 class="text-xl font-black text-white leading-snug">{{ $livre->titre }}</h1>
        @if($livre->auteur)
            <p class="text-sm mt-1" style="color:rgba(255,255,255,0.55);">
                <i class="fas fa-pen-fancy mr-1"></i>{{ $livre->auteur }}
            </p>
        @endif
    </div>
    <div style="height:3px;background:#2E6CB8;box-shadow:0 0 8px rgba(74,144,217,0.7);"></div>
</div>

{{-- Contenu principal --}}
<div class="bg-white rounded-2xl overflow-hidden" style="border:1.5px solid #EBF2FF;box-shadow:0 4px 16px rgba(26,58,143,0.08);">
    <div class="sm:flex">

        {{-- Cover --}}
        <div class="sm:w-2/5 lg:w-1/3 flex-shrink-0">
            <div class="aspect-[3/4] relative flex items-center justify-center overflow-hidden"
                 style="background:linear-gradient(135deg,#040D24 0%,#0D2060 50%,#1A3A8F 100%);">
                @if($livre->cover_image)
                    <img src="{{ asset('storage/' . $livre->cover_image) }}" alt="{{ $livre->titre }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="text-center px-8">
                        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4"
                             style="background:rgba(255,255,255,0.10);border:1.5px solid rgba(255,255,255,0.18);">
                            <i class="fas fa-book text-3xl text-white opacity-60"></i>
                        </div>
                        <p class="text-white font-bold text-sm leading-snug opacity-80">{{ $livre->titre }}</p>
                    </div>
                @endif
                {{-- Badge PDF --}}
                <div class="absolute top-3 right-3">
                    <span class="text-xs font-bold px-3 py-1 rounded-full"
                          style="background:rgba(0,0,0,0.60);color:#F5A623;backdrop-filter:blur(4px);">
                        <i class="fas fa-file-pdf mr-1"></i>PDF
                    </span>
                </div>
            </div>
        </div>

        {{-- Détails --}}
        <div class="flex-1 p-6 flex flex-col justify-between">
            <div>
                {{-- Badge catégorie --}}
                <span class="inline-flex items-center text-xs font-bold px-3 py-1.5 rounded-xl mb-4"
                      style="background:#EBF2FF;color:#1A3A8F;border:1px solid #D4E3F5;">
                    <i class="fas fa-tag mr-1.5"></i>{{ ucfirst(str_replace('_', ' ', $livre->categorie)) }}
                </span>

                {{-- Description --}}
                @if($livre->description)
                    <p class="text-sm leading-relaxed mb-6" style="color:#4A6FA8;">{{ $livre->description }}</p>
                @endif

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-3 mb-6">
                    @if($livre->nombre_pages)
                    <div class="text-center rounded-xl py-3" style="background:#F8FAFF;border:1px solid #EBF2FF;">
                        <i class="fas fa-file-alt text-lg mb-1" style="color:#2E6CB8;"></i>
                        <p class="text-xs font-bold" style="color:#0A1535;">{{ $livre->nombre_pages }}</p>
                        <p class="text-[10px]" style="color:#7FA5D0;">pages</p>
                    </div>
                    @endif
                    <div class="text-center rounded-xl py-3" style="background:#F8FAFF;border:1px solid #EBF2FF;">
                        <i class="fas fa-eye text-lg mb-1" style="color:#4A90D9;"></i>
                        <p class="text-xs font-bold" style="color:#0A1535;">{{ $livre->vues }}</p>
                        <p class="text-[10px]" style="color:#7FA5D0;">vues</p>
                    </div>
                    <div class="text-center rounded-xl py-3" style="background:#F8FAFF;border:1px solid #EBF2FF;">
                        <i class="fas fa-download text-lg mb-1" style="color:#1A3A8F;"></i>
                        <p class="text-xs font-bold" style="color:#0A1535;">{{ $livre->telechargements }}</p>
                        <p class="text-[10px]" style="color:#7FA5D0;">téléchargements</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('elite.bibliotheque.download', $livre) }}"
                   class="inline-flex items-center gap-2 text-white font-bold px-6 py-3 rounded-xl transition-all hover:opacity-90 shadow-sm"
                   style="background:linear-gradient(135deg,#1A3A8F,#2E6CB8);">
                    <i class="fas fa-download"></i>Télécharger le PDF
                </a>
                <a href="{{ asset('storage/' . $livre->fichier_pdf) }}" target="_blank"
                   class="inline-flex items-center gap-2 font-bold px-6 py-3 rounded-xl transition-all hover:opacity-90"
                   style="background:#F8FAFF;color:#1A3A8F;border:1.5px solid #D4E3F5;">
                    <i class="fas fa-external-link-alt"></i>Ouvrir dans le navigateur
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Points de progression --}}
<div class="flex justify-center gap-1.5 py-10">
    <div class="w-2 h-2 rounded-full" style="background:#1A3A8F;"></div>
    <div class="w-5 h-2 rounded-full" style="background:#2E6CB8;"></div>
    <div class="w-2 h-2 rounded-full" style="background:#1A3A8F;"></div>
</div>

@endsection