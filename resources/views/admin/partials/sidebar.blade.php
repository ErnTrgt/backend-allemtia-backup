<!-- Admin Sidebar Component -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <img src="{{ asset('admin/src/images/emtialogo.png') }}" alt="ALLEMTIA" class="sidebar-logo">
    </div>
    
    <nav class="sidebar-nav">
        <!-- Main Navigation -->
        <div class="nav-section">
            <div class="nav-section-title">
                <span>ANA MENÜ</span>
            </div>
            <ul class="nav-items">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 nav-icon"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <i class="bi bi-people nav-icon"></i>
                        <span class="nav-text">Kullanıcılar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.products') }}" class="nav-link {{ request()->routeIs('admin.products') ? 'active' : '' }}">
                        <i class="bi bi-box-seam nav-icon"></i>
                        <span class="nav-text">Ürünler</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                        <i class="bi bi-cart3 nav-icon"></i>
                        <span class="nav-text">Siparişler</span>
                        @if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                            <span class="nav-badge">{{ $pendingOrdersCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Store Management -->
        <div class="nav-section">
            <div class="nav-section-title">
                <span>MAĞAZA YÖNETİMİ</span>
            </div>
            <ul class="nav-items">
                <li class="nav-item">
                    <a href="{{ route('admin.stores') }}" class="nav-link {{ request()->routeIs('admin.stores') ? 'active' : '' }}">
                        <i class="bi bi-shop nav-icon"></i>
                        <span class="nav-text">Mağazalar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                        <i class="bi bi-tags nav-icon"></i>
                        <span class="nav-text">Kategoriler</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <i class="bi bi-ticket-perforated nav-icon"></i>
                        <span class="nav-text">Kuponlar</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Content & Reports -->
        <div class="nav-section">
            <div class="nav-section-title">
                <span>İÇERİK YÖNETİMİ</span>
            </div>
            <ul class="nav-items">
                <li class="nav-item">
                    <a href="{{ route('admin.blogs.index') }}" class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                        <i class="bi bi-newspaper nav-icon"></i>
                        <span class="nav-text">Blog</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.slider.index') }}" class="nav-link {{ request()->routeIs('admin.slider.*') ? 'active' : '' }}">
                        <i class="bi bi-images nav-icon"></i>
                        <span class="nav-text">Slider</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.about.index') }}" class="nav-link {{ request()->routeIs('admin.about.*') ? 'active' : '' }}">
                        <i class="bi bi-info-square nav-icon"></i>
                        <span class="nav-text">Hakkımızda</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.faq.index') }}" class="nav-link {{ request()->routeIs('admin.faq.*') ? 'active' : '' }}">
                        <i class="bi bi-question-circle nav-icon"></i>
                        <span class="nav-text">S.S.S</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Settings & Reports -->
        <div class="nav-section">
            <div class="nav-section-title">
                <span>AYARLAR & RAPORLAR</span>
            </div>
            <ul class="nav-items">
                <li class="nav-item">
                    <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                        <i class="bi bi-graph-up nav-icon"></i>
                        <span class="nav-text">Raporlar</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <i class="bi bi-person nav-icon"></i>
                        <span class="nav-text">Profil</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-gear nav-icon"></i>
                        <span class="nav-text">Ayarlar</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>