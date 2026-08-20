<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Elite 2.0')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        elite: {
                            blue: { 50:'#eff6ff',100:'#dbeafe',200:'#bfdbfe',300:'#93c5fd',400:'#60a5fa',500:'#3b82f6',600:'#2563eb',700:'#1d4ed8',800:'#1e3a5f',900:'#0f2440' },
                            green: { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b' },
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Figtree', system-ui, sans-serif; }
        .gradient-elite { background: linear-gradient(135deg, #1e3a5f 0%, #064e3b 100%); }
        .glass { background: rgba(255,255,255,0.08); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.12); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(16,185,129,0.15); }
        .badge-pulse { animation: pulse-badge 2s infinite; }
        @keyframes pulse-badge { 0%,100%{opacity:1} 50%{opacity:0.7} }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    

    {{-- Alerts --}}
    @if(session('success'))
    <div class="max-w-7xl mx-auto px-4 mt-4">
        <div class="bg-elite-green-50 border border-elite-green-200 text-elite-green-800 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-check-circle text-elite-green-500"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    {{-- Content --}}
    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

  

    @stack('scripts')
</body>
</html>
