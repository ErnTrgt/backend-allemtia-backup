<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Seller Panel') - Allemtia</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Existing CSS (for backward compatibility) -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/styles/core.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/styles/icon-font.min.css') }}">
    
    <!-- Modern Design System -->
    <link rel="stylesheet" href="{{ asset('css/seller-design-system.css') }}">
    
    <!-- Modern Layout CSS -->
    <style>
        /* CSS Variables - Seller Panel Theme */
        :root {
            --color-dark: #0B090A;
            --color-primary: #2B2D42;
            --color-gray: #8D99AE;
            --color-light: #EDF2F4;
            --color-accent: #EF233C;
            --color-accent-dark: #D90429;
            --color-white: #FFFFFF;
            
            /* Layout Variables */
            --header-height: 70px;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            
            /* Transitions */
            --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Z-Index */
            --z-dropdown: 100;
            --z-header: 200;
            --z-sidebar: 150;
            --z-mobile-menu: 300;
            --z-modal: 400;
        }

        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-light);
            color: var(--color-primary);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Layout Structure */
        .app-container {
            min-height: 100vh;
            display: flex;
        }

        /* Header */
        .app-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: var(--color-white);
            border-bottom: 1px solid rgba(141, 153, 174, 0.1);
            z-index: var(--z-header);
            transition: all var(--transition-base);
        }

        .header-container {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .sidebar-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: transparent;
            border: none;
            color: var(--color-primary);
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .sidebar-toggle:hover {
            background: var(--color-light);
            color: var(--color-accent);
        }

        .brand-logo-mobile {
            display: none;
            height: 40px;
        }

        .brand-logo-mobile img {
            height: 100%;
            width: auto;
        }

        .search-bar {
            position: relative;
            width: 400px;
        }

        .search-input {
            width: 100%;
            height: 42px;
            padding: 0 1rem 0 3rem;
            border: 2px solid transparent;
            background: var(--color-light);
            border-radius: 10px;
            font-size: 0.875rem;
            color: var(--color-primary);
            transition: all var(--transition-fast);
        }

        .search-input:focus {
            outline: none;
            background: var(--color-white);
            border-color: var(--color-primary);
        }

        .search-input::placeholder {
            color: var(--color-gray);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--color-gray);
            pointer-events: none;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: transparent;
            border: none;
            color: var(--color-primary);
            cursor: pointer;
            position: relative;
            transition: all var(--transition-fast);
        }

        .header-btn:hover {
            background: var(--color-light);
            color: var(--color-accent);
        }

        .notification-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: var(--color-accent);
            border-radius: 50%;
            border: 2px solid var(--color-white);
        }

        .user-menu {
            position: relative;
        }

        .user-menu-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            background: transparent;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .user-menu-toggle:hover {
            background: var(--color-light);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--color-light);
        }

        .user-info {
            text-align: left;
        }

        .user-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--color-primary);
            line-height: 1.2;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--color-gray);
        }

        /* Sidebar */
        .app-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--color-white);
            border-right: 1px solid rgba(141, 153, 174, 0.1);
            z-index: var(--z-sidebar);
            transition: all var(--transition-base);
            overflow: hidden;
        }

        .app-sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid rgba(141, 153, 174, 0.1);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--color-primary);
        }

        .brand-logo img {
            height: 40px;
            width: auto;
        }

        .brand-text {
            font-size: 1.5rem;
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -1px;
            transition: opacity var(--transition-base);
        }

        .collapsed .brand-text {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar-content {
            height: calc(100% - var(--header-height));
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem 0;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar-content::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-content::-webkit-scrollbar-thumb {
            background: var(--color-gray);
            border-radius: 3px;
            opacity: 0.3;
        }

        .sidebar-content:hover::-webkit-scrollbar-thumb {
            opacity: 0.5;
        }

        .nav-menu {
            list-style: none;
            padding: 0 1rem;
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 1rem;
            color: var(--color-primary);
            text-decoration: none;
            border-radius: 12px;
            transition: all var(--transition-fast);
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover {
            background: var(--color-light);
            color: var(--color-accent);
            transform: translateX(4px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%);
            color: var(--color-white);
            box-shadow: 0 4px 12px rgba(239, 35, 60, 0.3);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            transform: translateX(-100%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            to {
                transform: translateX(200%);
            }
        }

        .nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            min-width: 24px;
        }

        .nav-text {
            font-size: 0.9375rem;
            font-weight: 500;
            white-space: nowrap;
            transition: opacity var(--transition-base);
        }

        .collapsed .nav-text {
            opacity: 0;
            visibility: hidden;
        }

        .nav-badge {
            margin-left: auto;
            padding: 0.125rem 0.5rem;
            background: var(--color-accent);
            color: var(--color-white);
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 999px;
            transition: opacity var(--transition-base);
        }

        .collapsed .nav-badge {
            opacity: 0;
            visibility: hidden;
        }

        /* Dropdown Menu */
        .nav-item.has-dropdown .nav-link::after {
            content: '\F282';
            font-family: 'bootstrap-icons';
            margin-left: auto;
            transition: transform var(--transition-fast);
        }

        .nav-item.has-dropdown.open .nav-link::after {
            transform: rotate(90deg);
        }

        .dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height var(--transition-base);
        }

        .nav-item.has-dropdown.open .dropdown-menu {
            max-height: 300px;
        }

        .dropdown-menu .nav-link {
            padding-left: 3rem;
            font-size: 0.875rem;
        }

        /* Main Content */
        .app-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            min-height: calc(100vh - var(--header-height));
            transition: margin-left var(--transition-base);
            background: var(--color-light);
        }

        .sidebar-collapsed .app-main {
            margin-left: var(--sidebar-collapsed-width);
        }

        .main-content {
            padding: 2rem;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--color-gray);
        }

        .breadcrumb a {
            color: var(--color-gray);
            text-decoration: none;
            transition: color var(--transition-fast);
        }

        .breadcrumb a:hover {
            color: var(--color-accent);
        }

        .breadcrumb-separator {
            color: var(--color-gray);
            opacity: 0.5;
        }

        /* Mobile Overlay */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(11, 9, 10, 0.5);
            z-index: var(--z-mobile-menu);
            opacity: 0;
            transition: opacity var(--transition-base);
        }

        .mobile-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .search-bar {
                width: 300px;
            }
        }

        @media (max-width: 768px) {
            .app-sidebar {
                transform: translateX(-100%);
            }

            .app-sidebar.mobile-open {
                transform: translateX(0);
            }

            .app-main {
                margin-left: 0;
            }

            .sidebar-collapsed .app-main {
                margin-left: 0;
            }

            .search-bar {
                display: none;
            }

            .user-info {
                display: none;
            }

            .brand-logo-mobile {
                display: block;
            }

            .main-content {
                padding: 1rem;
            }
        }

        /* Dropdown Styles */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            min-width: 200px;
            background: var(--color-white);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(11, 9, 10, 0.1);
            border: 1px solid rgba(141, 153, 174, 0.1);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all var(--transition-fast);
            z-index: var(--z-dropdown);
        }

        .dropdown.show .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--color-primary);
            text-decoration: none;
            transition: all var(--transition-fast);
            font-size: 0.875rem;
        }

        .dropdown-item:hover {
            background: var(--color-light);
            color: var(--color-accent);
        }

        .dropdown-item i {
            font-size: 1.125rem;
            opacity: 0.7;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--color-light);
            margin: 0.5rem 0;
        }

        /* Loading States */
        .skeleton {
            background: linear-gradient(90deg, var(--color-light) 25%, rgba(141, 153, 174, 0.1) 50%, var(--color-light) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        /* Tooltips */
        .tooltip {
            position: absolute;
            background: var(--color-primary);
            color: var(--color-white);
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-fast);
            pointer-events: none;
            z-index: var(--z-modal);
        }

        .tooltip::before {
            content: '';
            position: absolute;
            border: 5px solid transparent;
        }

        .tooltip.top {
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(10px);
            margin-bottom: 0.5rem;
        }

        .tooltip.top::before {
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-top-color: var(--color-primary);
        }

        .collapsed .nav-link:hover .tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="app-sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('seller.dashboard') }}" class="brand-logo">
                    <img src="{{ asset('admin/src/images/emtialogo.png') }}" alt="Allemtia">
                    <span class="brand-text">Allemtia</span>
                </a>
            </div>
            
            <div class="sidebar-content">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('seller.dashboard') }}" class="nav-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid nav-icon"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('seller.products') }}" class="nav-link {{ request()->routeIs('seller.products*') ? 'active' : '' }}">
                            <i class="bi bi-box-seam nav-icon"></i>
                            <span class="nav-text">Ürünler</span>
                            <span class="nav-badge">24</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('seller.orders') }}" class="nav-link {{ request()->routeIs('seller.orders*') ? 'active' : '' }}">
                            <i class="bi bi-bag-check nav-icon"></i>
                            <span class="nav-text">Siparişler</span>
                            <span class="nav-badge">3</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('seller.coupons.index') }}" class="nav-link {{ request()->routeIs('seller.coupons*') ? 'active' : '' }}">
                            <i class="bi bi-ticket-perforated nav-icon"></i>
                            <span class="nav-text">Kuponlar</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('seller.cart-items') }}" class="nav-link {{ request()->routeIs('seller.cart-items') ? 'active' : '' }}">
                            <i class="bi bi-cart3 nav-icon"></i>
                            <span class="nav-text">Sepet Öğeleri</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('seller.wishlist-items') }}" class="nav-link {{ request()->routeIs('seller.wishlist-items') ? 'active' : '' }}">
                            <i class="bi bi-heart nav-icon"></i>
                            <span class="nav-text">Favori Öğeleri</span>
                        </a>
                    </li>
                    
                    <li class="nav-item has-dropdown {{ request()->routeIs('seller.category-requests') || request()->routeIs('seller.subcategory-requests') ? 'open' : '' }}">
                        <a href="javascript:;" class="nav-link">
                            <i class="bi bi-tags nav-icon"></i>
                            <span class="nav-text">Kategori Talepleri</span>
                        </a>
                        <ul class="dropdown-menu" style="{{ request()->routeIs('seller.category-requests') || request()->routeIs('seller.subcategory-requests') ? 'max-height: 300px;' : '' }}">
                            <li class="nav-item">
                                <a href="{{ route('seller.category-requests') }}" class="nav-link {{ request()->routeIs('seller.category-requests') ? 'active' : '' }}">
                                    <span class="nav-text">Kategori Talepleri</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('seller.subcategory-requests') }}" class="nav-link {{ request()->routeIs('seller.subcategory-requests') ? 'active' : '' }}">
                                    <span class="nav-text">Alt Kategori Talepleri</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item has-dropdown {{ request()->routeIs('seller.profile') || request()->routeIs('seller.password.change') ? 'open' : '' }}">
                        <a href="javascript:;" class="nav-link">
                            <i class="bi bi-gear nav-icon"></i>
                            <span class="nav-text">Hesap Ayarları</span>
                        </a>
                        <ul class="dropdown-menu" style="{{ request()->routeIs('seller.profile') || request()->routeIs('seller.password.change') ? 'max-height: 300px;' : '' }}">
                            <li class="nav-item">
                                <a href="{{ route('seller.profile') }}" class="nav-link {{ request()->routeIs('seller.profile') ? 'active' : '' }}">
                                    <span class="nav-text">Profil</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('seller.password.change') }}" class="nav-link {{ request()->routeIs('seller.password.change') ? 'active' : '' }}">
                                    <span class="nav-text">Şifre Değiştir</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Header -->
        <header class="app-header">
            <div class="header-container">
                <div class="header-left">
                    <button type="button" class="sidebar-toggle" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <div class="brand-logo-mobile">
                        <img src="{{ asset('admin/src/images/emtialogo.png') }}" alt="Allemtia">
                    </div>
                    
                    <div class="search-bar">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" placeholder="Ara...">
                    </div>
                </div>
                
                <div class="header-right">
                    <button type="button" class="header-btn">
                        <i class="bi bi-bell"></i>
                        <span class="notification-badge"></span>
                    </button>
                    
                    <button type="button" class="header-btn">
                        <i class="bi bi-envelope"></i>
                    </button>
                    
                    <div class="dropdown user-menu">
                        <button type="button" class="user-menu-toggle" data-toggle="dropdown">
                            <img src="{{ asset('/admin/vendors/images/photo2.jpg') }}" alt="User" class="user-avatar">
                            <div class="user-info">
                                <div class="user-name">{{ auth()->user()->name ?? 'Satıcı' }}</div>
                                <div class="user-role">Satıcı</div>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        
                        <div class="dropdown-menu">
                            <a href="{{ route('seller.profile') }}" class="dropdown-item">
                                <i class="bi bi-person"></i>
                                Profil
                            </a>
                            <a href="{{ route('seller.password.change') }}" class="dropdown-item">
                                <i class="bi bi-shield-lock"></i>
                                Şifre Değiştir
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>
                                Çıkış Yap
                            </a>
                            <form id="logout-form" action="{{ route('seller.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="app-main">
            <div class="main-content">
                @if(View::hasSection('page-header'))
                    <div class="page-header">
                        @yield('page-header')
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Scripts -->
    <script src="{{ asset('admin/vendors/scripts/core.js') }}"></script>
    <script src="{{ asset('admin/vendors/scripts/script.min.js') }}"></script>
    
    <!-- Modern Layout Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Toggle
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const appMain = document.querySelector('.app-main');
            
            // Check if sidebar state is saved
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed && window.innerWidth > 768) {
                sidebar.classList.add('collapsed');
                appMain.classList.add('sidebar-collapsed');
            }
            
            sidebarToggle.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('mobile-open');
                    mobileOverlay.classList.toggle('active');
                } else {
                    sidebar.classList.toggle('collapsed');
                    appMain.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                }
            });
            
            mobileOverlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-open');
                mobileOverlay.classList.remove('active');
            });
            
            // Dropdown Menus
            const dropdowns = document.querySelectorAll('.dropdown');
            
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('[data-toggle="dropdown"]');
                
                toggle?.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    // Close other dropdowns
                    dropdowns.forEach(d => {
                        if (d !== dropdown) {
                            d.classList.remove('show');
                        }
                    });
                    
                    dropdown.classList.toggle('show');
                });
            });
            
            // Close dropdowns on outside click
            document.addEventListener('click', function() {
                dropdowns.forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            });
            
            // Sidebar Dropdown Menus
            const navDropdowns = document.querySelectorAll('.nav-item.has-dropdown');
            
            navDropdowns.forEach(dropdown => {
                const link = dropdown.querySelector('.nav-link');
                
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdown.classList.toggle('open');
                });
            });
            
            // Add tooltips for collapsed sidebar
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                const text = link.querySelector('.nav-text')?.textContent;
                if (text) {
                    const tooltip = document.createElement('div');
                    tooltip.className = 'tooltip top';
                    tooltip.textContent = text;
                    link.appendChild(tooltip);
                }
            });
            
            // Search functionality
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        // Implement search functionality
                        console.log('Search:', this.value);
                    }
                });
            }
            
            // Active menu item scroll into view
            const activeMenuItem = document.querySelector('.nav-link.active');
            if (activeMenuItem) {
                activeMenuItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>