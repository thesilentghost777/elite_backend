<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Elite 2.0 - La plateforme de formation en ligne qui transforme votre avenir professionnel au Cameroun">
    <title>Elite 2.0 | Transformez Votre Avenir Professionnel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 'poppins': ['Poppins', 'sans-serif'] },
                    colors: {
                        'elite': { 'primary': '#1a1a2e', 'secondary': '#16213e', 'accent': '#e94560', 'gold': '#ffd700', 'light': '#0f3460' }
                    },
                    animation: { 'float': 'float 6s ease-in-out infinite', 'gradient': 'gradient 8s ease infinite' },
                    keyframes: {
                        float: { '0%, 100%': { transform: 'translateY(0px)' }, '50%': { transform: 'translateY(-20px)' } },
                        gradient: { '0%, 100%': { backgroundPosition: '0% 50%' }, '50%': { backgroundPosition: '100% 50%' } }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .gradient-text { background: linear-gradient(135deg, #e94560 0%, #ffd700 50%, #e94560 100%); background-size: 200% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: gradient 3s ease infinite; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .hero-bg { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); }
        .floating-shape { position: absolute; border-radius: 50%; filter: blur(60px); opacity: 0.3; }
        .feature-card { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .feature-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 25px 50px -12px rgba(233, 69, 96, 0.25); }
        .btn-shine { position: relative; overflow: hidden; }
        .btn-shine::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); transition: left 0.5s; }
        .btn-shine:hover::before { left: 100%; }
        .pack-card { background: linear-gradient(145deg, #1a1a2e 0%, #16213e 100%); transition: all 0.5s ease; }
        .pack-card:hover { transform: translateY(-15px); box-shadow: 0 30px 60px -15px rgba(233, 69, 96, 0.4); }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1a1a2e; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #e94560, #ffd700); border-radius: 10px; }
        
        /* Optimisations Mobile */
        @media (max-width: 768px) {
            .floating-shape { display: none; }
            .hero-bg { min-height: 100vh; }
        }
        
        /* Menu mobile */
        #mobile-menu { transform: translateX(100%); transition: transform 0.3s ease-in-out; }
        #mobile-menu.active { transform: translateX(0); }
    </style>
</head>
<body class="bg-elite-primary text-white">
    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 z-[9999] bg-elite-primary flex items-center justify-center transition-opacity duration-500">
        <div class="text-center">
            <div class="relative w-24 h-24 mx-auto mb-4">
                <div class="absolute inset-0 border-4 border-transparent border-t-elite-accent rounded-full animate-spin"></div>
                <div class="absolute inset-2 border-4 border-transparent border-r-elite-gold rounded-full animate-spin" style="animation-direction: reverse;"></div>
            </div>
            <h2 class="text-2xl font-bold gradient-text">Elite 2.0</h2>
        </div>
    </div>

    <!-- Navigation -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                <a href="#" class="flex items-center space-x-2 md:space-x-3 group">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-elite-accent to-elite-gold rounded-xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300">
                        <span class="text-xl md:text-2xl font-bold text-white">E</span>
                    </div>
                    <div><span class="text-xl md:text-2xl font-bold">Elite</span><span class="text-elite-gold">2.0</span></div>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#accueil" class="text-gray-300 hover:text-white transition-colors">Accueil</a>
                    <a href="#fonctionnalites" class="text-gray-300 hover:text-white transition-colors">Fonctionnalités</a>
                    <a href="#formations" class="text-gray-300 hover:text-white transition-colors">Formations</a>
                    <a href="#faq" class="text-gray-300 hover:text-white transition-colors">FAQ</a>
                    <a href="https://play.google.com/store/apps/details?id=com.ghost777xsorganization.elite20" class="btn-shine px-6 py-3 bg-gradient-to-r from-elite-accent to-pink-600 rounded-full font-semibold hover:shadow-lg hover:shadow-elite-accent/50 transition-all duration-300">
                        <i class="fas fa-download mr-2"></i>Télécharger
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="lg:hidden p-2 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="fixed top-0 right-0 bottom-0 w-64 bg-elite-secondary z-[60] lg:hidden shadow-2xl">
        <div class="flex items-center justify-between p-4 border-b border-white/10">
            <span class="text-xl font-bold">Menu</span>
            <button id="close-menu" class="p-2 text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="flex flex-col p-4 space-y-4">
            <a href="#accueil" class="mobile-link text-gray-300 hover:text-white py-2 transition-colors">Accueil</a>
            <a href="#fonctionnalites" class="mobile-link text-gray-300 hover:text-white py-2 transition-colors">Fonctionnalités</a>
            <a href="#formations" class="mobile-link text-gray-300 hover:text-white py-2 transition-colors">Formations</a>
            <a href="#faq" class="mobile-link text-gray-300 hover:text-white py-2 transition-colors">FAQ</a>
            <a href="https://play.google.com/store/apps/details?id=com.ghost777xsorganization.elite20" class="btn-shine px-6 py-3 bg-gradient-to-r from-elite-accent to-pink-600 rounded-full font-semibold text-center">
                <i class="fas fa-download mr-2"></i>Télécharger
            </a>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="accueil" class="hero-bg min-h-screen flex items-center relative overflow-hidden pt-16">
        <div class="floating-shape w-96 h-96 bg-elite-accent top-20 -left-48 animate-float"></div>
        <div class="floating-shape w-80 h-80 bg-elite-gold bottom-20 -right-40 animate-float" style="animation-delay: -3s;"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-32 relative z-10">
            <div class="text-center" data-aos="fade-up">
                <div class="inline-flex items-center px-3 py-2 md:px-4 rounded-full glass mb-6 md:mb-8 text-xs md:text-sm">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-gray-300">Nouvelle plateforme 2026</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-7xl font-bold leading-tight mb-4 md:mb-6 px-2">
                    Transformez votre <span class="gradient-text">avenir professionnel</span>
                </h1>
                <p class="text-base md:text-xl text-gray-300 mb-6 md:mb-8 max-w-3xl mx-auto px-4">
                    La première plateforme camerounaise d'orientation et de formation professionnelle intelligente. Découvrez votre métier idéal parmi 50+ profils.
                </p>
                <div class="grid grid-cols-3 gap-3 md:gap-6 mb-8 md:mb-10 max-w-2xl mx-auto px-4">
                    <div class="text-center">
                        <div class="text-2xl md:text-4xl font-bold gradient-text">30+</div>
                        <div class="text-xs md:text-sm text-gray-400">Métiers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl md:text-4xl font-bold gradient-text">23</div>
                        <div class="text-xs md:text-sm text-gray-400">Formations</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl md:text-4xl font-bold gradient-text">10K+</div>
                        <div class="text-xs md:text-sm text-gray-400">Apprenants</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fonctionnalites" class="py-16 md:py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-elite-accent/20 rounded-full text-elite-accent text-sm font-medium mb-4">Fonctionnalités</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-6">Tout pour <span class="gradient-text">réussir</span></h2>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                <div class="feature-card glass rounded-2xl md:rounded-3xl p-6 md:p-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-elite-accent to-pink-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-compass text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-3 md:mb-4">Orientation Intelligente</h3>
                    <p class="text-sm md:text-base text-gray-400">20+ questions pour identifier les 5 métiers les plus adaptés à votre profil.</p>
                </div>
                <div class="feature-card glass rounded-2xl md:rounded-3xl p-6 md:p-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-elite-gold to-yellow-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-route text-xl md:text-2xl text-elite-primary"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-3 md:mb-4">Roadmap Personnalisée</h3>
                    <p class="text-sm md:text-base text-gray-400">Parcours sur mesure selon votre niveau d'études (BEPC à Master).</p>
                </div>
                <div class="feature-card glass rounded-2xl md:rounded-3xl p-6 md:p-8" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-laptop-code text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-3 md:mb-4">Formations Complètes</h3>
                    <p class="text-sm md:text-base text-gray-400">18+ packs avec cours, vidéos, quiz et certification officielle.</p>
                </div>
                <div class="feature-card glass rounded-2xl md:rounded-3xl p-6 md:p-8" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-users text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-3 md:mb-4">Système de Parrainage</h3>
                    <p class="text-sm md:text-base text-gray-400">Gagnez des points et obtenez des formations gratuites!</p>
                </div>
                <div class="feature-card glass rounded-2xl md:rounded-3xl p-6 md:p-8" data-aos="fade-up" data-aos-delay="500">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-wallet text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-3 md:mb-4">Paiement Flexible</h3>
                    <p class="text-sm md:text-base text-gray-400">Mobile Money, code caisse, transfert P2P sans frais.</p>
                </div>
                <div class="feature-card glass rounded-2xl md:rounded-3xl p-6 md:p-8" data-aos="fade-up" data-aos-delay="600">
                    <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mb-4 md:mb-6">
                        <i class="fas fa-award text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-3 md:mb-4">Diplômes Reconnus</h3>
                    <p class="text-sm md:text-base text-gray-400">AQP, CQP, DQP, BTS reconnus par l'État camerounais.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Formations Section -->
    <section id="formations" class="py-16 md:py-24 bg-elite-secondary/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 md:mb-16" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-green-500/20 rounded-full text-green-400 text-sm font-medium mb-4">Formations</span>
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">18 Packs <span class="gradient-text">certifiants</span></h2>
                <h4 class="text-xl md:text-2xl font-bold mt-4">Les plus populaires</h4>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                <div class="pack-card rounded-2xl md:rounded-3xl p-5 md:p-6 border border-white/10" data-aos="fade-up">
                    <div class="flex items-center justify-between mb-4 md:mb-6">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-bullhorn text-xl md:text-2xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-elite-gold/20 rounded-full text-elite-gold text-xs md:text-sm">Populaire</span>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-2">Marketing Digital</h3>
                    <p class="text-sm md:text-base text-gray-400 mb-4">Maîtrisez les stratégies de marketing en ligne</p>
                    <div class="flex flex-wrap gap-2 mb-4 md:mb-6">
                        <span class="px-2 md:px-3 py-1 bg-white/5 rounded-full text-xs">6-12 Mois</span>
                        <span class="px-2 md:px-3 py-1 bg-white/5 rounded-full text-xs">CQP/DQP</span>
                        <span class="px-2 md:px-3 py-1 bg-white/5 rounded-full text-xs">Niveau BAC</span>
                    </div>
                </div>
                <div class="pack-card rounded-2xl md:rounded-3xl p-5 md:p-6 border border-white/10" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center justify-between mb-4 md:mb-6">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-code text-xl md:text-2xl"></i>
                        </div>
                        <span class="px-3 py-1 bg-green-500/20 rounded-full text-green-400 text-xs md:text-sm">Nouveau</span>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-2">Développement Web</h3>
                    <p class="text-sm md:text-base text-gray-400 mb-4">Créez des sites web et applications</p>
                    <div class="flex flex-wrap gap-2 mb-4 md:mb-6">
                        <span class="px-2 md:px-3 py-1 bg-white/5 rounded-full text-xs">6-12 Mois</span>
                        <span class="px-2 md:px-3 py-1 bg-white/5 rounded-full text-xs">CQP/DQP</span>
                        <span class="px-2 md:px-3 py-1 bg-white/5 rounded-full text-xs">Niveau BAC</span>
                    </div>
                </div>
                <div class="pack-card rounded-2xl md:rounded-3xl p-5 md:p-6 border border-white/10" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center justify-between mb-4 md:mb-6">
                        <div class="w-14 h-14 md:w-16 md:h-16 bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-palette text-xl md:text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-xl md:text-2xl font-bold mb-2">Infographie</h3>
                    <p class="text-sm md:text-base text-gray-400 mb-4">Devenez expert en création graphique</p>
                    <div class="flex flex-wrap gap-2 mb-4 md:mb-6">
                        <span class="px-2 md:px-3 py-1 bg-white/5 rounded-full text-xs">3-12 Mois</span>
                        <span class="px-2 md:px-3 py-1 bg-white/5 rounded-full text-xs">AQP/CQP/DQP</span>
                        <span class="px-2 md:px-3 py-1 bg-white/5 rounded-full text-xs">Niveau BEPC</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-16 md:py-24">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12 md:mb-16" data-aos="fade-up">
                <span class="inline-block px-4 py-2 bg-cyan-500/20 rounded-full text-cyan-400 text-sm font-medium mb-4">FAQ</span>
                <h2 class="text-3xl md:text-4xl font-bold">Questions <span class="gradient-text">fréquentes</span></h2>
            </div>
            <div class="space-y-4" data-aos="fade-up">
                <div class="glass rounded-xl md:rounded-2xl p-5 md:p-6">
                    <h3 class="font-semibold text-base md:text-lg mb-2">Comment fonctionne le questionnaire d'orientation?</h3>
                    <p class="text-sm md:text-base text-gray-400">20+ questions analysent votre profil et calculent le % de correspondance avec 50+ métiers. Vous recevez les 5 métiers les plus adaptés.</p>
                </div>
                <div class="glass rounded-xl md:rounded-2xl p-5 md:p-6">
                    <h3 class="font-semibold text-base md:text-lg mb-2">Comment fonctionne le système de points?</h3>
                    <p class="text-sm md:text-base text-gray-400">500 FCFA = 1 point. Rechargez via Mobile Money ou code caisse. Chaque pack coûte environ 50 points. Le parrainage vous rapporte des points bonus!</p>
                </div>
                <div class="glass rounded-xl md:rounded-2xl p-5 md:p-6">
                    <h3 class="font-semibold text-base md:text-lg mb-2">Les diplômes sont-ils reconnus?</h3>
                    <p class="text-sm md:text-base text-gray-400">Oui! Diplômes AQP, CQP, DQP, BTS délivrés par le CFPAM et reconnus par le MINEFOP du Cameroun.</p>
                </div>
            </div>
            <div class="mt-8 md:mt-12 text-center">
                <a href="https://wa.me/237696087354" target="_blank" class="inline-flex items-center px-5 md:px-6 py-3 bg-green-600 hover:bg-green-700 rounded-full font-medium transition-colors text-sm md:text-base">
                    <i class="fab fa-whatsapp text-lg md:text-xl mr-2 md:mr-3"></i>Contactez-nous sur WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 md:py-24 bg-elite-secondary/30">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4 md:mb-6">Prêt à transformer votre <span class="gradient-text">avenir</span>?</h2>
            <p class="text-base md:text-xl text-gray-300 mb-8 md:mb-10 max-w-2xl mx-auto px-4">Rejoignez des milliers de Camerounais qui ont choisi Elite 2.0!</p>
            <a href="https://play.google.com/store/apps/details?id=com.ghost777xsorganization.elite20" class="btn-shine px-8 md:px-10 py-4 md:py-5 bg-gradient-to-r from-elite-accent to-pink-600 rounded-full font-bold text-lg md:text-xl inline-flex items-center">
                <i class="fas fa-rocket mr-2 md:mr-3"></i>Télécharger l'application
            </a>
            <div class="mt-12 md:mt-16 grid sm:grid-cols-3 gap-6 md:gap-8 max-w-3xl mx-auto">
                <a href="https://wa.me/237696087354" class="glass rounded-xl md:rounded-2xl p-5 md:p-6 hover:bg-green-500/10 transition-colors">
                    <i class="fab fa-whatsapp text-2xl md:text-3xl text-green-500 mb-3 md:mb-4"></i>
                    <p class="font-semibold text-sm md:text-base">+237 696 087 354</p>
                </a>
                <a href="mailto:techforgesolution237@gmail.com" class="glass rounded-xl md:rounded-2xl p-5 md:p-6 hover:bg-elite-accent/10 transition-colors">
                    <i class="fas fa-envelope text-2xl md:text-3xl text-elite-accent mb-3 md:mb-4"></i>
                    <p class="font-semibold text-xs md:text-sm break-words">tsf237@gmail.com</p>
                </a>
                <a href="https://techforgesolution237.site" target="_blank" class="glass rounded-xl md:rounded-2xl p-5 md:p-6 hover:bg-elite-gold/10 transition-colors">
                    <i class="fas fa-globe text-2xl md:text-3xl text-elite-gold mb-3 md:mb-4"></i>
                    <p class="font-semibold text-xs md:text-sm break-words">techforgesolution237.site</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 md:py-12 bg-elite-primary border-t border-white/5 text-center">
        <div class="flex items-center justify-center space-x-3 mb-4 md:mb-6">
            <div class="w-10 h-10 bg-gradient-to-br from-elite-accent to-elite-gold rounded-xl flex items-center justify-center">
                <span class="text-xl font-bold">E</span>
            </div>
            <span class="text-xl font-bold">Elite<span class="text-elite-gold">2.0</span></span>
        </div>
        <p class="text-gray-400 text-xs md:text-sm px-4">© 2026 Elite 2.0. Powered By <a href="https://techforgesolution237.site" class="text-elite-gold hover:underline">TFS237</a></p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Preloader
        window.addEventListener('load', () => { 
            document.getElementById('preloader').style.opacity = '0'; 
            setTimeout(() => document.getElementById('preloader').style.display = 'none', 500); 
        });
        
        // AOS Init
        AOS.init({ duration: 800, once: true, offset: 100 });
        
        // Navbar background on scroll
        window.addEventListener('scroll', () => { 
            const navbar = document.getElementById('navbar');
            if (window.pageYOffset > 100) {
                navbar.classList.add('bg-elite-primary/95', 'backdrop-blur-lg', 'shadow-lg');
            } else {
                navbar.classList.remove('bg-elite-primary/95', 'backdrop-blur-lg', 'shadow-lg');
            }
        });
        
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const closeMenuBtn = document.getElementById('close-menu');
        const mobileLinks = document.querySelectorAll('.mobile-link');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.add('active');
        });
        
        closeMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
        });
        
        // Close menu when clicking on a link
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                mobileMenu.classList.remove('active');
            }
        });
    </script>
</body>
</html>