@extends('opportunities.layouts.elite-base')
@section('title', $livre->titre . ' — Bibliothèque Elite 2.0')

@section('content')
<a href="{{ route('elite.bibliotheque') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="fas fa-arrow-left"></i>Retour
</a>

<div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
    <div class="sm:flex">
        {{-- Cover --}}
        <div class="sm:w-1/3 aspect-[3/4] bg-gradient-to-br from-elite-blue-800 to-elite-green-700 flex items-center justify-center">
            @if($livre->cover_image)
                <img src="{{ asset('storage/' . $livre->cover_image) }}" alt="{{ $livre->titre }}" class="w-full h-full object-cover">
            @else
                <div class="text-center px-6">
                    <i class="fas fa-book text-6xl text-white/30 mb-3"></i>
                    <p class="text-white/80 font-bold">{{ $livre->titre }}</p>
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div class="p-6 sm:w-2/3">
            <span class="bg-elite-blue-100 text-elite-blue-700 text-xs font-bold px-2.5 py-1 rounded-lg">
                {{ ucfirst(str_replace('_', ' ', $livre->categorie)) }}
            </span>
            <h1 class="text-2xl font-black text-gray-900 mt-3">{{ $livre->titre }}</h1>
            @if($livre->auteur)
                <p class="text-sm text-elite-blue-600 font-semibold mt-1"><i class="fas fa-pen-fancy mr-1"></i>{{ $livre->auteur }}</p>
            @endif

            @if($livre->description)
                <p class="text-sm text-gray-600 mt-4 leading-relaxed">{{ $livre->description }}</p>
            @endif

            <div class="flex flex-wrap gap-4 mt-6 text-xs text-gray-400">
                @if($livre->nombre_pages)
                    <span><i class="fas fa-file-alt mr-1"></i>{{ $livre->nombre_pages }} pages</span>
                @endif
                <span><i class="fas fa-eye mr-1"></i>{{ $livre->vues }} vues</span>
                <span><i class="fas fa-download mr-1"></i>{{ $livre->telechargements }} téléchargements</span>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('elite.bibliotheque.download', $livre) }}" class="inline-flex items-center gap-2 bg-elite-blue-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-elite-blue-700 transition text-sm shadow-sm">
                    <i class="fas fa-download"></i>Télécharger le PDF
                </a>
                <a href="{{ asset('storage/' . $livre->fichier_pdf) }}" target="_blank" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 font-bold px-6 py-3 rounded-xl hover:bg-gray-200 transition text-sm">
                    <i class="fas fa-external-link-alt"></i>Ouvrir dans le navigateur
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
