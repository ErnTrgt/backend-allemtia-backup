<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - ALLEMTIA</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- ApexCharts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-red: #A90000;
            --secondary-red: #C1121F;
            --light-blue: #F0F8FF;
            --primary-blue: #0051BB;
            --secondary-blue: #3FA1DD;
            --white: #FFFFFF;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
            
            --sidebar-width: 300px;
            --sidebar-collapsed-width: 120px;
            --header-height: 70px;
            --sidebar-header-height: 180px;
            
            --spacing-xs: 4px;
            --spacing-sm: 8px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;
            --spacing-2xl: 48px;
            --spacing-3xl: 64px;
            
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.15);
            --shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.2);
            
            --transition-base: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            overflow-x: hidden;
        }
        
        /* Layout Structure */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            transition: var(--transition-base);
            z-index: 1000;
            overflow: hidden;
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-header {
            height: var(--sidebar-header-height);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--gray-900);
        }
        
        .sidebar-logo img {
            height: 160px;
            width: auto;
            transition: var(--transition-base);
        }
        
        .sidebar.collapsed .sidebar-logo img {
            height: 100px;
        }
        
        .sidebar-menu {
            padding: var(--spacing-lg) 0;
            height: calc(100vh - var(--sidebar-header-height));
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 3px;
        }
        
        .nav-item {
            margin-bottom: var(--spacing-xs);
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-md) var(--spacing-lg);
            color: var(--gray-700);
            text-decoration: none;
            border-radius: var(--radius-md);
            margin: 0 var(--spacing-md);
            transition: var(--transition-fast);
            position: relative;
            overflow: hidden;
        }
        
        .nav-link:hover {
            color: var(--primary-red);
            background: rgba(169, 0, 0, 0.05);
            transform: translateX(4px);
        }
        
        .nav-link.active {
            color: var(--white);
            background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
            box-shadow: var(--shadow-md);
        }
        
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--white);
        }
        
        .nav-icon {
            font-size: 20px;
            min-width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .nav-text {
            font-size: 15px;
            font-weight: 500;
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }
        
        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
        }
        
        .sidebar.collapsed .nav-link {
            justify-content: center;
        }
        
        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            height: var(--header-height);
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 var(--spacing-xl);
            transition: var(--transition-base);
            z-index: 999;
        }
        
        .sidebar.collapsed ~ .header {
            left: var(--sidebar-collapsed-width);
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
        }
        
        .sidebar-toggle {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition-fast);
        }
        
        .sidebar-toggle:hover {
            background: var(--gray-50);
            transform: scale(1.05);
        }
        
        .search-box {
            position: relative;
            width: 400px;
        }
        
        .search-input {
            width: 100%;
            padding: var(--spacing-sm) var(--spacing-md) var(--spacing-sm) var(--spacing-2xl);
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            font-size: 14px;
            transition: var(--transition-fast);
        }
        
        .search-input:focus {
            outline: none;
            background: var(--white);
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(169, 0, 0, 0.1);
        }
        
        .search-icon {
            position: absolute;
            left: var(--spacing-md);
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
        }
        
        .header-btn {
            position: relative;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition-fast);
            color: var(--gray-700);
        }
        
        .header-btn:hover {
            background: var(--gray-50);
            transform: scale(1.05);
            color: var(--primary-red);
        }
        
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 20px;
            height: 20px;
            background: var(--primary-red);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-sm) var(--spacing-md);
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition-fast);
        }
        
        .user-menu:hover {
            background: var(--gray-50);
            transform: scale(1.02);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-900);
        }
        
        .user-role {
            font-size: 12px;
            color: var(--gray-500);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            min-height: calc(100vh - var(--header-height));
            padding: var(--spacing-xl);
            transition: var(--transition-base);
        }
        
        .sidebar.collapsed ~ .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        /* Page Title */
        .page-header {
            margin-bottom: var(--spacing-xl);
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: var(--spacing-sm);
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-item {
            font-size: 14px;
            color: var(--gray-500);
        }
        
        .breadcrumb-item.active {
            color: var(--gray-700);
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .search-box {
                width: 300px;
            }
        }
        
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.mobile-active {
                transform: translateX(0);
            }
            
            .header {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .search-box {
                display: none;
            }
            
            .user-info {
                display: none;
            }
        }
        
        /* Mobile Overlay */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        
        .mobile-overlay.active {
            display: block;
        }
        
        /* Dropdown Menu */
        .dropdown-menu {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-xl);
            padding: var(--spacing-sm);
            min-width: 200px;
        }
        
        .dropdown-item {
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-sm);
            font-size: 14px;
            color: var(--gray-700);
            transition: var(--transition-fast);
        }
        
        .dropdown-item:hover {
            background: var(--gray-50);
            color: var(--primary-red);
        }
        
        .dropdown-divider {
            margin: var(--spacing-sm) 0;
            border-color: var(--gray-200);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
                    <img src="{{ asset('admin/src/images/emtialogo.png') }}" alt="ALLEMTIA">
                </a>
            </div>
            
            <nav class="sidebar-menu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid nav-icon"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                            <i class="bi bi-people nav-icon"></i>
                            <span class="nav-text">Kullanıcılar</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.products') }}" class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                            <i class="bi bi-box-seam nav-icon"></i>
                            <span class="nav-text">Ürünler</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                            <i class="bi bi-cart-check nav-icon"></i>
                            <span class="nav-text">Siparişler</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                            <i class="bi bi-tags nav-icon"></i>
                            <span class="nav-text">Kategoriler</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.stores') }}" class="nav-link {{ request()->routeIs('admin.stores*') ? 'active' : '' }}">
                            <i class="bi bi-shop nav-icon"></i>
                            <span class="nav-text">Mağazalar</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}">
                            <i class="bi bi-ticket-perforated nav-icon"></i>
                            <span class="nav-text">Kuponlar</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up nav-icon"></i>
                            <span class="nav-text">Raporlar</span>
                        </a>
                    </li>
                    
                    <li class="nav-item mt-auto">
                        <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}">
                            <i class="bi bi-gear nav-icon"></i>
                            <span class="nav-text">Ayarlar</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Mobile Overlay -->
        <div class="mobile-overlay" id="mobileOverlay"></div>
        
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Ara...">
                </div>
            </div>
            
            <div class="header-right">
                <button class="header-btn">
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                
                <button class="header-btn">
                    <i class="bi bi-envelope"></i>
                </button>
                
                <div class="dropdown">
                    <div class="user-menu" data-bs-toggle="dropdown">
                        <div class="user-info">
                            <div class="user-name">{{ Auth::user()->name }}</div>
                            <div class="user-role">Yönetici</div>
                        </div>
                        <div class="user-avatar">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                    
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.profile') }}">
                                <i class="bi bi-person me-2"></i> Profil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i> Ayarlar
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Çıkış Yap
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileOverlay = document.getElementById('mobileOverlay');
        
        sidebarToggle.addEventListener('click', () => {
            if (window.innerWidth > 991) {
                sidebar.classList.toggle('collapsed');
                // Save state to localStorage
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            } else {
                sidebar.classList.toggle('mobile-active');
                mobileOverlay.classList.toggle('active');
            }
        });
        
        // Mobile overlay click
        mobileOverlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-active');
            mobileOverlay.classList.remove('active');
        });
        
        // Restore sidebar state
        if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth > 991) {
            sidebar.classList.add('collapsed');
        }
        
        // Window resize handler
        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                sidebar.classList.remove('mobile-active');
                mobileOverlay.classList.remove('active');
            }
        });
        
        // Tooltip initialization
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
    
    @stack('scripts')
</body>
</html>