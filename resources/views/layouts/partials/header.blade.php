<header class="top-header">
    <div class="header-left">
        <button class="menu-toggle" id="menuToggle">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <nav class="breadcrumb">
            @yield('breadcrumb')
        </nav>
    </div>

    <div class="header-right">
        <div class="user-menu">
            <div class="user-avatar">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
            <div>
                <div style="font-weight: 600; font-size: 0.875rem;">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div style="font-size: 0.75rem; color: var(--gray-600);">Administrateur</div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Déconnexion
            </button>
        </form>
    </div>
</header>