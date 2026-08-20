@extends('opportunities.layouts.elite-base')
@section('title', 'Communauté — Elite 2.0')

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

    <div class="relative z-10 px-6 py-8 text-center">
        <p class="text-xs font-semibold tracking-widest uppercase mb-4" style="color:rgba(255,255,255,0.45);">Elite 2.0</p>

        {{-- Avatar communauté --}}
        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-4"
             style="background:rgba(255,255,255,0.10);border:1.5px solid rgba(255,255,255,0.18);">
            <i class="fas fa-users text-3xl text-white"></i>
        </div>

        <h1 class="text-2xl font-black text-white tracking-tight mb-2">Communauté Elite 2.0</h1>
        <p class="text-sm max-w-md mx-auto" style="color:rgba(255,255,255,0.50);">
            Rejoignez des groupes WhatsApp selon vos centres d'intérêt et échangez avec des milliers de membres
        </p>

        {{-- Stats --}}
        <div class="flex justify-center gap-4 mt-6">
            <div class="text-center px-5 py-3 rounded-xl" style="background:rgba(255,255,255,0.09);border:1px solid rgba(255,255,255,0.14);">
                <p class="text-lg font-black text-white">10</p>
                <p class="text-xs" style="color:rgba(255,255,255,0.50);">Groupes</p>
            </div>
            <div class="text-center px-5 py-3 rounded-xl" style="background:rgba(255,255,255,0.09);border:1px solid rgba(255,255,255,0.14);">
                <p class="text-lg font-black text-white">2 327</p>
                <p class="text-xs" style="color:rgba(255,255,255,0.50);">Membres</p>
            </div>
            <div class="text-center px-5 py-3 rounded-xl" style="background:rgba(245,166,35,0.15);border:1px solid rgba(245,166,35,0.30);">
                <p class="text-lg font-black" style="color:#F5A623;">WhatsApp</p>
                <p class="text-xs" style="color:rgba(255,255,255,0.50);">Gratuit</p>
            </div>
        </div>
    </div>

    <div style="height:3px;background:#2E6CB8;box-shadow:0 0 8px rgba(74,144,217,0.7);"></div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- GRILLE DES GROUPES                          --}}
{{-- ═══════════════════════════════════════════ --}}
@php
    $defaultGroups = [
        ['nom' => 'Entrepreneuriat',                    'description' => 'Business, startup, création d\'entreprise, stratégie commerciale',          'icone' => '🚀', 'membres_count' => 342],
        ['nom' => 'Informatique & Tech',                'description' => 'Programmation, réseaux, IA, cybersécurité, dev web & mobile',               'icone' => '💻', 'membres_count' => 289],
        ['nom' => 'Sport & Bien-être',                  'description' => 'Football, basketball, fitness, nutrition, santé mentale',                   'icone' => '⚽', 'membres_count' => 215],
        ['nom' => 'Marketing Digital',                  'description' => 'SEO, réseaux sociaux, publicité en ligne, content marketing',               'icone' => '📱', 'membres_count' => 198],
        ['nom' => 'Finance & Investissement',           'description' => 'Épargne, bourse, crypto, immobilier, gestion financière',                   'icone' => '💰', 'membres_count' => 276],
        ['nom' => 'Arts & Culture',                     'description' => 'Musique, cinéma, littérature, photographie, design',                        'icone' => '🎨', 'membres_count' => 134],
        ['nom' => 'Sciences & Innovation',              'description' => 'Recherche, environnement, énergie, biotechnologie',                         'icone' => '🔬', 'membres_count' => 87],
        ['nom' => 'Droit & Administration',             'description' => 'Juridique, fonction publique, concours administratifs',                     'icone' => '⚖️', 'membres_count' => 156],
        ['nom' => 'Leadership & Développement',         'description' => 'Confiance en soi, prise de parole, gestion du temps, motivation',           'icone' => '🌟', 'membres_count' => 321],
        ['nom' => 'Agriculture & Agrobusiness',         'description' => 'Élevage, culture, transformation, agroalimentaire',                        'icone' => '🌾', 'membres_count' => 109],
    ];

    $bandeaux = [
        ['#1A3A8F', '#2E6CB8'],
        ['#0D2060', '#1A3A8F'],
        ['#2E6CB8', '#4A90D9'],
        ['#534AB7', '#7F77DD'],
        ['#1A3A8F', '#4A90D9'],
        ['#0F6E56', '#1D9E75'],
        ['#185FA5', '#378ADD'],
        ['#3C3489', '#534AB7'],
        ['#065F46', '#1D9E75'],
        ['#0A1535', '#1A3A8F'],
    ];

    $displayGroups = $groups->count() > 0
        ? $groups
        : collect($defaultGroups)->map(fn($g) => (object) $g);
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($displayGroups as $group)
    @php
        $idx          = $loop->index % count($bandeaux);
        $fromColor    = $bandeaux[$idx][0];
        $toColor      = $bandeaux[$idx][1];
        $whatsappMsg  = urlencode("Bonjour, je veux rejoindre le groupe Elite 2.0 de discussion sur {$group->nom}. Merci !");
        $whatsappUrl  = "https://wa.me/237659292001?text={$whatsappMsg}";
    @endphp

    <div class="bg-white rounded-2xl overflow-hidden transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl"
         style="border:1.5px solid #EBF2FF;box-shadow:0 2px 8px rgba(26,58,143,0.06);">

        {{-- Bandeau gradient --}}
        <div class="p-4 relative overflow-hidden"
             style="background:linear-gradient(135deg, {{ $fromColor }}, {{ $toColor }});">

            {{-- Mini filigrane Afrique --}}
            <svg class="absolute right-3 top-2 opacity-10 w-14 h-14 pointer-events-none" viewBox="0 0 80 90" fill="white">
                <ellipse cx="40" cy="35" rx="22" ry="26"/>
                <ellipse cx="40" cy="67" rx="9" ry="14"/>
            </svg>

            <div class="flex items-center gap-3 relative z-10">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0"
                     style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.20);">
                    {{ $group->icone ?? '💬' }}
                </div>
                <div>
                    <h3 class="font-black text-white text-sm leading-snug">{{ $group->nom }}</h3>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.60);">
                        <i class="fas fa-users mr-1"></i>{{ number_format($group->membres_count ?? 0) }} membres
                    </p>
                </div>
            </div>
        </div>

        {{-- Corps --}}
        <div class="p-4">
            <p class="text-xs leading-relaxed mb-4" style="color:#4A6FA8;">{{ $group->description }}</p>

            <a href="{{ $whatsappUrl }}" target="_blank"
               class="w-full flex items-center justify-center gap-2 font-bold py-2.5 rounded-xl text-sm transition-all hover:opacity-90 shadow-sm text-white"
               style="background:#25D366;">
                <i class="fab fa-whatsapp text-base"></i>Rejoindre le groupe
            </a>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- PROPOSER UN NOUVEAU GROUPE                  --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="mt-8 rounded-2xl overflow-hidden" style="border:1.5px solid #D4E3F5;">

    {{-- Mini header bleu --}}
    <div class="px-6 py-4" style="background:linear-gradient(135deg,#040D24,#1A3A8F);">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(255,255,255,0.10);border:1px solid rgba(255,255,255,0.18);">
                <i class="fas fa-lightbulb" style="color:#F5A623;"></i>
            </div>
            <div>
                <p class="font-black text-white text-sm">Proposer un nouveau groupe</p>
                <p class="text-xs" style="color:rgba(255,255,255,0.50);">Créez un groupe sur le thème de votre choix</p>
            </div>
        </div>
    </div>
    <div style="height:2px;background:#2E6CB8;"></div>

    {{-- Corps --}}
    <div class="px-6 py-5 bg-white flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-center sm:text-left" style="color:#4A6FA8;">
            Contactez-nous sur WhatsApp pour lancer un groupe de discussion communautaire.
        </p>
        <a href="https://wa.me/237659292001?text={{ urlencode('Bonjour, je souhaite proposer un nouveau groupe de discussion sur Elite 2.0. Le thème serait : ') }}"
           target="_blank"
           class="inline-flex items-center gap-2 text-white font-bold px-6 py-3 rounded-xl transition-all hover:opacity-90 shadow-sm whitespace-nowrap"
           style="background:#25D366;">
            <i class="fab fa-whatsapp text-base"></i>Proposer un groupe
        </a>
    </div>
</div>

{{-- Points de progression --}}
<div class="flex justify-center gap-1.5 py-10">
    <div class="w-2 h-2 rounded-full" style="background:#1A3A8F;"></div>
    <div class="w-5 h-2 rounded-full" style="background:#2E6CB8;"></div>
    <div class="w-2 h-2 rounded-full" style="background:#1A3A8F;"></div>
</div>

@endsection