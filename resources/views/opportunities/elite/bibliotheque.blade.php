@extends('opportunities.layouts.elite-base')
@section('title', 'Bibliothèque — Elite 2.0')
@section('content')

<a href="/espace/communaute" class="inline-flex items-center px-4 py-2 bg-sky-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 transition-colors">
    <i class="fas fa-comments mr-2"></i>Discuter avec la communauté
</a>

<br><br>

<div class="mb-6">
    <h1 class="text-2xl font-black text-slate-900">
        <i class="fas fa-book-open mr-2 text-cyan-600"></i>Bibliothèque
    </h1>
    <p class="text-slate-400 text-sm mt-1">Livres et ressources PDF gratuits</p>
</div>

{{-- Search & Filter --}}
<form method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
    <div class="relative flex-1">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
        <input
            name="search"
            value="{{ request('search') }}"
            placeholder="Rechercher un livre..."
            class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl text-sm bg-white text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 outline-none transition"
        >
    </div>
    <select
        name="categorie"
        onchange="this.form.submit()"
        class="border border-slate-200 rounded-xl px-4 py-3 text-sm bg-white text-slate-600 focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 outline-none transition"
    >
        <option value="">Toutes catégories</option>
        @foreach(['entrepreneuriat','informatique','marketing','comptabilite','gestion','droit','sciences','langues','developpement_personnel','commerce','autre'] as $cat)
            <option value="{{ $cat }}" {{ request('categorie') === $cat ? 'selected' : '' }}>
                {{ ucfirst(str_replace('_', ' ', $cat)) }}
            </option>
        @endforeach
    </select>
</form>

{{-- Grid --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($livres as $livre)
        <a href="{{ route('elite.bibliotheque.view', $livre) }}"
           class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 block group">

            <div class="aspect-[3/4] bg-gradient-to-br from-slate-700 via-cyan-800 to-sky-900 flex items-center justify-center relative">
                @if($livre->cover_image)
                    <img src="{{ asset('storage/' . $livre->cover_image) }}" alt="{{ $livre->titre }}" class="w-full h-full object-cover">
                @else
                    <div class="text-center px-3">
                        <i class="fas fa-book text-4xl text-white/30 mb-2"></i>
                        <p class="text-white/80 text-xs font-bold leading-tight">{{ Str::limit($livre->titre, 40) }}</p>
                    </div>
                @endif

                <div class="absolute bottom-2 right-2">
                    <span class="bg-black/50 text-cyan-200 text-[10px] px-2 py-0.5 rounded-full backdrop-blur-sm">
                        <i class="fas fa-file-pdf mr-1"></i>PDF
                    </span>
                </div>
            </div>

            <div class="p-3">
                <h3 class="font-bold text-sm text-slate-800 line-clamp-2 group-hover:text-cyan-700 transition-colors">
                    {{ $livre->titre }}
                </h3>
                @if($livre->auteur)
                    <p class="text-xs text-slate-400 mt-1">{{ $livre->auteur }}</p>
                @endif
                <div class="flex items-center gap-3 mt-2 text-[10px] text-slate-400">
                    <span><i class="fas fa-eye mr-1 text-sky-400"></i>{{ $livre->vues }}</span>
                    <span><i class="fas fa-download mr-1 text-cyan-400"></i>{{ $livre->telechargements }}</span>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full text-center py-16">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-book text-2xl text-slate-300"></i>
            </div>
            <p class="text-slate-400 font-medium">Aucun livre disponible</p>
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $livres->links() }}</div>

@endsection