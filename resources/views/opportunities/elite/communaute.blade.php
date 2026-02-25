@extends('opportunities.layouts.elite-base')
@section('title', 'Communauté — Elite 2.0')

@section('content')
<div class="mb-6 text-center">
    <div class="w-16 h-16 bg-gradient-to-br from-elite-blue-600 to-elite-green-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
        <i class="fas fa-users text-2xl text-white"></i>
    </div>
    <h1 class="text-2xl font-black text-gray-900">Communauté Elite 2.0</h1>
    <p class="text-gray-500 text-sm mt-1 max-w-md mx-auto">Rejoignez des groupes de discussion WhatsApp selon vos centres d'intérêt et échangez avec des milliers de membres</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @php
        $defaultGroups = [
            ['nom' => 'Entrepreneuriat', 'description' => 'Business, startup, création d\'entreprise, stratégie commerciale', 'icone' => '🚀', 'membres_count' => 342],
            ['nom' => 'Informatique & Tech', 'description' => 'Programmation, réseaux, IA, cybersécurité, dev web & mobile', 'icone' => '💻', 'membres_count' => 289],
            ['nom' => 'Sport & Bien-être', 'description' => 'Football, basketball, fitness, nutrition, santé mentale', 'icone' => '⚽', 'membres_count' => 215],
            ['nom' => 'Marketing Digital', 'description' => 'SEO, réseaux sociaux, publicité en ligne, content marketing', 'icone' => '📱', 'membres_count' => 198],
            ['nom' => 'Finance & Investissement', 'description' => 'Épargne, bourse, crypto, immobilier, gestion financière', 'icone' => '💰', 'membres_count' => 276],
            ['nom' => 'Arts & Culture', 'description' => 'Musique, cinéma, littérature, photographie, design', 'icone' => '🎨', 'membres_count' => 134],
            ['nom' => 'Sciences & Innovation', 'description' => 'Recherche, environnement, énergie, biotechnologie', 'icone' => '🔬', 'membres_count' => 87],
            ['nom' => 'Droit & Administration', 'description' => 'Juridique, fonction publique, concours administratifs', 'icone' => '⚖️', 'membres_count' => 156],
            ['nom' => 'Leadership & Développement Personnel', 'description' => 'Confiance en soi, prise de parole, gestion du temps, motivation', 'icone' => '🌟', 'membres_count' => 321],
            ['nom' => 'Agriculture & Agrobusiness', 'description' => 'Élevage, culture, transformation, agroalimentaire', 'icone' => '🌾', 'membres_count' => 109],
        ];
        $displayGroups = $groups->count() > 0 ? $groups : collect($defaultGroups)->map(fn($g) => (object) $g);
    @endphp

    @foreach($displayGroups as $group)
    @php
        $whatsappMsg = urlencode("Bonjour, je veux rejoindre le groupe Elite 2.0 de discussion sur {$group->nom}. Merci !");
        $whatsappUrl = "https://wa.me/237659292001?text={$whatsappMsg}";
        $gradients = [
            'from-blue-500 to-cyan-400',
            'from-emerald-500 to-teal-400',
            'from-violet-500 to-purple-400',
            'from-orange-500 to-amber-400',
            'from-rose-500 to-pink-400',
            'from-indigo-500 to-blue-400',
        ];
        $gradient = $gradients[$loop->index % count($gradients)];
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden card-hover">
        <div class="bg-gradient-to-r {{ $gradient }} p-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl backdrop-blur-sm">
                    {{ $group->icone ?? '💬' }}
                </div>
                <div>
                    <h3 class="font-bold text-white text-sm">{{ $group->nom }}</h3>
                    <p class="text-white/70 text-xs"><i class="fas fa-users mr-1"></i>{{ $group->membres_count ?? 0 }} membres</p>
                </div>
            </div>
        </div>
        <div class="p-4">
            <p class="text-xs text-gray-600 leading-relaxed mb-4">{{ $group->description }}</p>
            <a href="{{ $whatsappUrl }}" target="_blank"
               class="w-full flex items-center justify-center gap-2 bg-green-500 text-white font-bold py-2.5 rounded-xl hover:bg-green-600 transition text-sm shadow-sm">
                <i class="fab fa-whatsapp text-lg"></i>Rejoindre le groupe
            </a>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-8 text-center">
    <div class="bg-gradient-to-r from-elite-blue-50 to-elite-green-50 rounded-2xl p-6 border border-elite-blue-100">
        <h3 class="font-bold text-gray-900"><i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Vous souhaitez proposer un nouveau groupe ?</h3>
        <p class="text-sm text-gray-500 mt-1 mb-4">Contactez-nous pour créer un groupe de discussion sur le thème de votre choix</p>
        <a href="https://wa.me/237659292001?text={{ urlencode('Bonjour, je souhaite proposer un nouveau groupe de discussion sur Elite 2.0. Le thème serait : ') }}" target="_blank"
           class="inline-flex items-center gap-2 bg-elite-blue-600 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-elite-blue-700 transition text-sm">
            <i class="fab fa-whatsapp"></i>Proposer un groupe
        </a>
    </div>
</div>
@endsection
