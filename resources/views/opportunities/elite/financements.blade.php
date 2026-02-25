@extends('opportunities.layouts.elite-base')
@section('title', 'Financements — Elite 2.0')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-black text-gray-900"><i class="fas fa-coins mr-2 text-elite-green-500"></i>Financements</h1>
    <p class="text-gray-500 text-sm mt-1">Bourses, subventions et opportunités de financement</p>
</div>

<div class="space-y-4">
    @forelse($financements as $f)
    <div class="bg-white rounded-2xl shadow-sm border p-5 card-hover">
        <div class="flex items-center gap-2 mb-3">
            @php
                $typeBadge = match($f->type) {
                    'bourse' => ['bg-purple-100 text-purple-700', 'fa-graduation-cap'],
                    'subvention' => ['bg-blue-100 text-blue-700', 'fa-hand-holding-usd'],
                    'pret' => ['bg-orange-100 text-orange-700', 'fa-piggy-bank'],
                    'investissement' => ['bg-green-100 text-green-700', 'fa-chart-line'],
                    default => ['bg-gray-100 text-gray-700', 'fa-coins'],
                };
            @endphp
            <span class="{{ $typeBadge[0] }} text-xs font-bold px-2.5 py-1 rounded-lg">
                <i class="fas {{ $typeBadge[1] }} mr-1"></i>{{ ucfirst($f->type) }}
            </span>
            @if($f->date_limite && \Carbon\Carbon::parse($f->date_limite)->isFuture())
                <span class="text-xs text-gray-400"><i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($f->date_limite)->diffForHumans() }}</span>
            @endif
        </div>

        <h3 class="font-bold text-gray-900 text-lg">{{ $f->titre }}</h3>
        <p class="text-sm text-elite-blue-600 font-semibold mt-1"><i class="fas fa-landmark mr-1"></i>{{ $f->organisme }}</p>
        <p class="text-sm text-gray-600 mt-3">{{ $f->description }}</p>

        @if($f->montant_min || $f->montant_max)
        <div class="mt-3">
            <span class="bg-elite-green-50 text-elite-green-700 text-xs font-bold px-3 py-1.5 rounded-lg">
                <i class="fas fa-money-bill-wave mr-1"></i>
                @if($f->montant_min && $f->montant_max)
                    {{ number_format($f->montant_min, 0, ',', '.') }} - {{ number_format($f->montant_max, 0, ',', '.') }} FCFA
                @elseif($f->montant_min)
                    À partir de {{ number_format($f->montant_min, 0, ',', '.') }} FCFA
                @else
                    Jusqu'à {{ number_format($f->montant_max, 0, ',', '.') }} FCFA
                @endif
            </span>
        </div>
        @endif

        @if($f->conditions_eligibilite)
        <div class="mt-3 bg-amber-50 rounded-xl p-3">
            <p class="text-xs text-amber-700"><i class="fas fa-exclamation-triangle mr-1"></i><strong>Éligibilité :</strong> {{ $f->conditions_eligibilite }}</p>
        </div>
        @endif

        <div class="mt-4">
            @php
                $msg = urlencode("Bonjour, je suis intéressé(e) par l'offre de financement \"{$f->titre}\" proposée par {$f->organisme} sur Elite 2.0. Pouvez-vous m'orienter ? Merci !");
            @endphp
            <a href="https://wa.me/237659292001?text={{ $msg }}" target="_blank"
               class="inline-flex items-center gap-2 bg-green-500 text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-green-600 transition shadow-sm">
                <i class="fab fa-whatsapp text-lg"></i>Souscrire via WhatsApp
            </a>
        </div>
    </div>
    @empty
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-coins text-2xl text-gray-300"></i>
        </div>
        <p class="text-gray-400 font-medium">Aucune offre de financement disponible</p>
    </div>
    @endforelse
</div>
@endsection
