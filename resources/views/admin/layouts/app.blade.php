<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') - Elite Formation</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #2563eb;
            --secondary-blue: #1e40af;
            --light-blue: #dbeafe;
            --success-green: #10b981;
            --dark-green: #059669;
            --light-green: #d1fae5;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: var(--white);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-menu {
            padding: 1.5rem 0;
        }

        .menu-section {
            margin-bottom: 2rem;
        }

        .menu-section-title {
            padding: 0 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .menu-item {
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
            position: relative;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white);
        }

        .menu-item.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: var(--white);
            font-weight: 600;
        }

        .menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: var(--success-green);
        }

        .menu-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: margin-left 0.3s ease;
            min-width: 0;
        }

        .top-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-200);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
            flex: 1;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            color: var(--gray-700);
            flex-shrink: 0;
        }

        .menu-toggle:hover {
            background-color: var(--gray-100);
            border-radius: 8px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-600);
            font-size: 0.875rem;
            flex-wrap: wrap;
        }

        .breadcrumb a {
            color: var(--primary-blue);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb-separator {
            color: var(--gray-400);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-shrink: 0;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .user-menu:hover {
            background-color: var(--gray-100);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-blue), var(--success-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--gray-900);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        .content-wrapper {
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.875rem;
            font-weight: bold;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .page-description {
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        /* Cards */
        .card {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
        }

        /* Buttons */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--primary-blue);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--secondary-blue);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
        }

        .btn-success {
            background-color: var(--success-green);
            color: var(--white);
        }

        .btn-success:hover {
            background-color: var(--dark-green);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
        }

        .btn-secondary {
            background-color: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-secondary:hover {
            background-color: var(--gray-300);
        }

        .btn-danger {
            background-color: var(--danger);
            color: var(--white);
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(239, 68, 68, 0.3);
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .alert-success {
            background-color: var(--light-green);
            color: var(--dark-green);
            border: 1px solid var(--success-green);
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid var(--danger);
        }

        .alert-info {
            background-color: var(--light-blue);
            color: var(--secondary-blue);
            border: 1px solid var(--primary-blue);
        }

        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid var(--warning);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .user-info {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: flex;
            }

            .content-wrapper {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .top-header {
                padding: 1rem;
            }

            .header-right {
                gap: 0.5rem;
            }

            .btn {
                padding: 0.5rem 0.75rem;
                font-size: 0.813rem;
            }

            .btn .menu-icon {
                width: 16px;
                height: 16px;
            }
        }

        /* Overlay pour mobile */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .overlay.active {
            display: block;
        }

        /* Scrollbar personnalisée */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- SweetAlert 2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        @include('admin.layouts.partials.sidebar')

        <div class="main-content">
            @include('admin.layouts.partials.header')

            <div class="content-wrapper">
                @if(session('success'))
                    <div class="alert alert-success">
                        <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">
                        <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning">
                        <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info">
                        <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <!-- SweetAlert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toggle sidebar sur mobile
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('overlay');

        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        // Fermer le menu sur mobile quand on clique sur un lien
        const menuItems = document.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.3s, transform 0.3s';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // CSRF Token pour les requêtes AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Configuration globale pour fetch
        window.fetchWithCSRF = function(url, options = {}) {
            return fetch(url, {
                ...options,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...options.headers
                }
            });
        };
    </script>

    @stack('scripts')
</body>
</html>