@extends('opportunities.layouts.elite-base')
@section('title', 'Bibliothèque — Elite 2.0')

@section('content')

{{-- Hero header --}}
<div class="relative overflow-hidden rounded-2xl mb-8" style="background: linear-gradient(160deg, #040D24 0%, #0A1535 40%, #0D2060 70%, #1A3A8F 100%);">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -left-20 top-0 w-1/2 h-full opacity-10" style="background:rgba(46,108,184,0.5);transform:skewX(18deg);"></div>
        <div class="absolute -right-20 top-0 w-1/2 h-full opacity-10" style="background:rgba(46,108,184,0.5);transform:skewX(-18deg);"></div>
    </div>

    <svg class="absolute right-6 top-4 opacity-[0.07] w-24 h-24 pointer-events-none" viewBox="0 0 80 90" fill="white">
        <ellipse cx="40" cy="35" rx="22" ry="26"/>
        <ellipse cx="40" cy="67" rx="9" ry="14"/>
    </svg>

    <div class="relative z-10 px-6 py-8">
        <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:rgba(255,255,255,0.10);border:1.5px solid rgba(255,255,255,0.14);">
                    <i class="fas fa-book-open text-lg text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight">Bibliothèque</h1>
                    <p class="text-xs" style="color:rgba(255,255,255,0.50);">Livres et ressources PDF gratuits</p>
                </div>
            </div>
            <a href="/espace/communaute"
               class="inline-flex items-center gap-2 text-sm font-bold px-4 py-2.5 rounded-xl transition-all hover:opacity-90"
               style="background:rgba(255,255,255,0.12);border:1.5px solid rgba(255,255,255,0.20);color:#fff;">
                <i class="fas fa-comments"></i>
                <span class="hidden sm:inline">Communauté</span>
            </a>
        </div>
    </div>

    <div style="height:3px;background:#2E6CB8;box-shadow:0 0 8px rgba(74,144,217,0.7);"></div>
</div>

{{-- Barre de recherche & filtre --}}
<form method="GET" class="flex flex-col sm:flex-row gap-3 mb-8">
    <div class="relative flex-1">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-sm" style="color:#7FA5D0;"></i>
        <input
            name="search"
            value="{{ request('search') }}"
            placeholder="Rechercher un livre..."
            class="w-full pl-10 pr-4 py-3 text-sm rounded-xl outline-none transition-all"
            style="background:#F8FAFF;border:2px solid #D4E3F5;color:#0A1535;"
            onfocus="this.style.borderColor='#2E6CB8'" onblur="this.style.borderColor='#D4E3F5'"
        >
    </div>
    <select
        name="categorie"
        onchange="this.form.submit()"
        class="rounded-xl px-4 py-3 text-sm outline-none transition-all"
        style="background:#F8FAFF;border:2px solid #D4E3F5;color:#4A6FA8;font-weight:600;"
    >
        <option value="">Toutes catégories</option>
        @foreach(['entrepreneuriat','informatique','marketing','comptabilite','gestion','droit','sciences','langues','developpement_personnel','commerce','autre'] as $cat)
            <option value="{{ $cat }}" {{ request('categorie') === $cat ? 'selected' : '' }}>
                {{ ucfirst(str_replace('_', ' ', $cat)) }}
            </option>
        @endforeach
    </select>
    <button type="submit"
            class="px-5 py-3 rounded-xl text-sm font-bold text-white transition-all hover:opacity-90"
            style="background:linear-gradient(135deg,#1A3A8F,#2E6CB8);">
        <i class="fas fa-search mr-2"></i>Chercher
    </button>
</form>

{{-- Grille des livres --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($livres as $livre)
        <a href="{{ route('elite.bibliotheque.view', $livre) }}"
           class="group bg-white rounded-2xl overflow-hidden transition-all duration-200 hover:-translate-y-1 hover:shadow-xl"
           style="border:1.5px solid #EBF2FF;box-shadow:0 2px 8px rgba(26,58,143,0.06);">

            {{-- Cover --}}
            <div class="aspect-[3/4] relative flex items-center justify-center overflow-hidden"
                 style="background:linear-gradient(135deg,#040D24 0%,#0D2060 50%,#1A3A8F 100%);">
                @if($livre->cover_image)
                    <img src="{{ asset('storage/' . $livre->cover_image) }}" alt="{{ $livre->titre }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="text-center px-3 z-10">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-3"
                             style="background:rgba(255,255,255,0.10);border:1.5px solid rgba(255,255,255,0.14);">
                            <i class="fas fa-book text-2xl text-white opacity-70"></i>
                        </div>
                        <p class="text-white text-xs font-bold leading-tight opacity-80">{{ Str::limit($livre->titre, 40) }}</p>
                    </div>
                @endif

                {{-- Badge PDF --}}
                <div class="absolute bottom-2 right-2">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                          style="background:rgba(0,0,0,0.55);color:#7FA5D0;backdrop-filter:blur(4px);">
                        <i class="fas fa-file-pdf mr-1" style="color:#F5A623;"></i>PDF
                    </span>
                </div>

                {{-- Overlay hover --}}
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center"
                     style="background:rgba(26,58,143,0.65);">
                    <span class="text-white text-xs font-bold px-4 py-2 rounded-xl"
                          style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);">
                        <i class="fas fa-eye mr-1"></i>Voir
                    </span>
                </div>
            </div>

            {{-- Infos --}}
            <div class="p-3">
                <h3 class="font-bold text-sm leading-snug line-clamp-2 mb-1 transition-colors group-hover:text-blue-700"
                    style="color:#0A1535;">
                    {{ $livre->titre }}
                </h3>
                @if($livre->auteur)
                    <p class="text-xs mb-2" style="color:#7FA5D0;">{{ $livre->auteur }}</p>
                @endif
                <div class="flex items-center gap-3" style="color:#7FA5D0;font-size:10px;">
                    <span><i class="fas fa-eye mr-1" style="color:#4A90D9;"></i>{{ $livre->vues }}</span>
                    <span><i class="fas fa-download mr-1" style="color:#2E6CB8;"></i>{{ $livre->telechargements }}</span>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full text-center py-20">
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5" style="background:#EBF2FF;">
                <i class="fas fa-book text-3xl" style="color:#7FA5D0;"></i>
            </div>
            <p class="font-bold text-gray-500 text-lg">Aucun livre disponible</p>
            <p class="text-gray-400 text-sm mt-1">La bibliothèque sera bientôt enrichie.</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="mt-8">{{ $livres->links() }}</div>

{{-- Points de progression --}}
<div class="flex justify-center gap-1.5 py-10">
    <div class="w-2 h-2 rounded-full" style="background:#1A3A8F;"></div>
    <div class="w-5 h-2 rounded-full" style="background:#2E6CB8;"></div>
    <div class="w-2 h-2 rounded-full" style="background:#1A3A8F;"></div>
</div>

@endsection