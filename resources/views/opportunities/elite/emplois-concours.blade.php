@extends('opportunities.layouts.elite-base')
@section('title', 'Emplois & Concours — Elite 2.0')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-black text-gray-900"><i class="fas fa-rocket mr-2 text-elite-blue-600"></i>Opportunités</h1>
    <p class="text-gray-500 text-sm mt-1">Emplois et concours disponibles pour vous</p>
</div>

{{-- Toggle --}}
<div class="flex gap-2 mb-6">
    <button onclick="toggleSection('emplois')" id="btn-emplois" class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-elite-blue-800 text-white transition">
        <i class="fas fa-briefcase mr-1"></i>Emplois <span class="bg-white/20 px-2 py-0.5 rounded-full text-xs ml-1">{{ count($emplois) }}</span>
    </button>
    <button onclick="toggleSection('concours')" id="btn-concours" class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-gray-200 text-gray-600 hover:bg-gray-300 transition">
        <i class="fas fa-trophy mr-1"></i>Concours <span class="bg-gray-300 px-2 py-0.5 rounded-full text-xs ml-1">{{ count($concours) }}</span>
    </button>
</div>

{{-- EMPLOIS --}}
<div id="section-emplois" class="space-y-4">
    @forelse($emplois as $job)
    <div class="bg-white rounded-2xl shadow-sm border p-5 card-hover">
        <div class="flex items-start justify-between gap-3">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-elite-blue-100 text-elite-blue-700 text-xs font-bold px-2.5 py-1 rounded-lg">{{ $job->type_contrat }}</span>
                    @if($job->date_limite && \Carbon\Carbon::parse($job->date_limite)->isFuture())
                        <span class="text-xs text-gray-400"><i class="fas fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($job->date_limite)->diffForHumans() }}</span>
                    @endif
                </div>
                <h3 class="font-bold text-gray-900 text-lg">{{ $job->titre }}</h3>
                <p class="text-sm text-elite-blue-600 font-semibold mt-1"><i class="fas fa-building mr-1"></i>{{ $job->entreprise }}</p>
                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $job->ville }}</p>
                <p class="text-sm text-gray-600 mt-3 line-clamp-3">{{ $job->description }}</p>

                @if($job->salaire_min || $job->salaire_max)
                <div class="mt-3 flex items-center gap-2">
                    <span class="bg-elite-green-50 text-elite-green-700 text-xs font-bold px-3 py-1 rounded-lg">
                        <i class="fas fa-money-bill-wave mr-1"></i>
                        @if($job->salaire_min && $job->salaire_max)
                            {{ number_format($job->salaire_min, 0, ',', '.') }} - {{ number_format($job->salaire_max, 0, ',', '.') }} FCFA
                        @elseif($job->salaire_min)
                            À partir de {{ number_format($job->salaire_min, 0, ',', '.') }} FCFA
                        @else
                            Jusqu'à {{ number_format($job->salaire_max, 0, ',', '.') }} FCFA
                        @endif
                    </span>
                </div>
                @endif

                <div class="mt-4 flex flex-wrap gap-2">
                    @if($job->contact_email)
                    <a href="mailto:{{ $job->contact_email }}" class="inline-flex items-center gap-1.5 bg-elite-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-xl hover:bg-elite-blue-700 transition">
                        <i class="fas fa-envelope"></i>Postuler par email
                    </a>
                    @endif
                    @if($job->contact_telephone)
                    <a href="tel:{{ $job->contact_telephone }}" class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-semibold px-4 py-2 rounded-xl hover:bg-gray-200 transition">
                        <i class="fas fa-phone"></i>{{ $job->contact_telephone }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-briefcase text-2xl text-gray-300"></i>
        </div>
        <p class="text-gray-400 font-medium">Aucune offre d'emploi pour le moment</p>
    </div>
    @endforelse
</div>

{{-- CONCOURS --}}
<div id="section-concours" class="space-y-4 hidden">
    @forelse($concours as $c)
    <div class="bg-white rounded-2xl shadow-sm border p-5 card-hover">
        <div class="flex items-center gap-2 mb-2">
            <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-2.5 py-1 rounded-lg"><i class="fas fa-trophy mr-1"></i>Concours</span>
            @if($c->date_limite_inscription && \Carbon\Carbon::parse($c->date_limite_inscription)->isFuture())
                <span class="text-xs text-orange-500 font-semibold badge-pulse"><i class="fas fa-fire mr-1"></i>Inscriptions ouvertes</span>
            @endif
        </div>
        <h3 class="font-bold text-gray-900 text-lg">{{ $c->titre }}</h3>
        <p class="text-sm text-gray-600 font-medium mt-1"><i class="fas fa-university mr-1 text-gray-400"></i>{{ $c->organisateur }}</p>
        <p class="text-sm text-gray-600 mt-3">{{ $c->description }}</p>

        <div class="mt-4 grid grid-cols-3 gap-2">
            <div class="bg-gray-50 rounded-xl p-2.5 text-center">
                <p class="text-[10px] text-gray-400 uppercase font-semibold">Début</p>
                <p class="text-xs font-bold text-gray-700">{{ \Carbon\Carbon::parse($c->date_debut)->format('d/m/Y') }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-2.5 text-center">
                <p class="text-[10px] text-gray-400 uppercase font-semibold">Fin</p>
                <p class="text-xs font-bold text-gray-700">{{ \Carbon\Carbon::parse($c->date_fin)->format('d/m/Y') }}</p>
            </div>
            <div class="bg-red-50 rounded-xl p-2.5 text-center">
                <p class="text-[10px] text-red-400 uppercase font-semibold">Limite</p>
                <p class="text-xs font-bold text-red-600">{{ \Carbon\Carbon::parse($c->date_limite_inscription)->format('d/m/Y') }}</p>
            </div>
        </div>

        @if($c->conditions)
        <div class="mt-3 bg-blue-50 rounded-xl p-3">
            <p class="text-xs text-blue-700"><i class="fas fa-info-circle mr-1"></i><strong>Conditions :</strong> {{ $c->conditions }}</p>
        </div>
        @endif

        @if($c->lien_inscription)
        <a href="{{ $c->lien_inscription }}" target="_blank" class="mt-4 inline-flex items-center gap-1.5 bg-elite-green-600 text-white text-xs font-semibold px-4 py-2 rounded-xl hover:bg-elite-green-700 transition">
            <i class="fas fa-external-link-alt"></i>S'inscrire
        </a>
        @endif
    </div>
    @empty
    <div class="text-center py-16">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-trophy text-2xl text-gray-300"></i>
        </div>
        <p class="text-gray-400 font-medium">Aucun concours disponible</p>
    </div>
    @endforelse
</div>

@push('scripts')
<script>
function toggleSection(s) {
    document.getElementById('section-emplois').classList.toggle('hidden', s !== 'emplois');
    document.getElementById('section-concours').classList.toggle('hidden', s !== 'concours');
    const be = document.getElementById('btn-emplois'), bc = document.getElementById('btn-concours');
    if (s === 'emplois') {
        be.className = 'flex-1 py-2.5 rounded-xl text-sm font-bold bg-elite-blue-800 text-white transition';
        bc.className = 'flex-1 py-2.5 rounded-xl text-sm font-bold bg-gray-200 text-gray-600 hover:bg-gray-300 transition';
    } else {
        bc.className = 'flex-1 py-2.5 rounded-xl text-sm font-bold bg-elite-blue-800 text-white transition';
        be.className = 'flex-1 py-2.5 rounded-xl text-sm font-bold bg-gray-200 text-gray-600 hover:bg-gray-300 transition';
    }
}
</script>
@endpush
@endsection
