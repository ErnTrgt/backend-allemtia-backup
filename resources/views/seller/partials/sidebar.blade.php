<!-- Modern Glass Morphism Sidebar -->
<div class="sidebar-header">
    <a href="{{ route('seller.dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('admin/src/images/emtialogo.png') }}" alt="ALLEMTIA" class="sidebar-logo">
    </a>
</div>

<nav class="sidebar-menu">
    <ul class="nav-list">
        <!-- Dashboard -->
        <li class="nav-item {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}">
            <a href="{{ route('seller.dashboard') }}" class="nav-link">
                <i class="bi bi-speedometer2 nav-icon"></i>
                <span class="nav-text">Gösterge Paneli</span>
            </a>
        </li>
        
        <!-- Products -->
        <li class="nav-item {{ request()->routeIs('seller.products*') ? 'active' : '' }}">
            <a href="{{ route('seller.products') }}" class="nav-link">
                <i class="bi bi-box-seam nav-icon"></i>
                <span class="nav-text">Ürünler</span>
                <span class="nav-badge">{{ $productCount ?? '' }}</span>
            </a>
        </li>
        
        <!-- Orders -->
        <li class="nav-item {{ request()->routeIs('seller.orders*') ? 'active' : '' }}">
            <a href="{{ route('seller.orders') }}" class="nav-link">
                <i class="bi bi-bag-check nav-icon"></i>
                <span class="nav-text">Siparişler</span>
                @if(isset($newOrderCount) && $newOrderCount > 0)
                    <span class="nav-badge bg-danger">{{ $newOrderCount }}</span>
                @endif
            </a>
        </li>
        
        <!-- Analytics Section -->
        <li class="nav-section">
            <span class="nav-section-title">ANALİTİK</span>
        </li>
        
        <!-- Cart Items -->
        <li class="nav-item {{ request()->routeIs('seller.cart-items') ? 'active' : '' }}">
            <a href="{{ route('seller.cart-items') }}" class="nav-link">
                <i class="bi bi-cart3 nav-icon"></i>
                <span class="nav-text">Sepet Ürünleri</span>
            </a>
        </li>
        
        <!-- Wishlist -->
        <li class="nav-item {{ request()->routeIs('seller.wishlist-items') ? 'active' : '' }}">
            <a href="{{ route('seller.wishlist-items') }}" class="nav-link">
                <i class="bi bi-heart nav-icon"></i>
                <span class="nav-text">Favori Listesi</span>
            </a>
        </li>
        
        <!-- Marketing Section -->
        <li class="nav-section">
            <span class="nav-section-title">PAZARLAMA</span>
        </li>
        
        <!-- Coupons -->
        <li class="nav-item {{ request()->routeIs('seller.coupons.*') ? 'active' : '' }}">
            <a href="{{ route('seller.coupons.index') }}" class="nav-link">
                <i class="bi bi-ticket-perforated nav-icon"></i>
                <span class="nav-text">Kuponlar</span>
            </a>
        </li>
        
        <!-- Category Requests -->
        <li class="nav-item dropdown {{ request()->routeIs('seller.category.requests') || request()->routeIs('seller.subcategory.requests') ? 'active show' : '' }}">
            <a href="javascript:;" class="nav-link dropdown-toggle">
                <i class="bi bi-diagram-3 nav-icon"></i>
                <span class="nav-text">Kategori Yönetimi</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <ul class="nav-dropdown {{ request()->routeIs('seller.category.requests') || request()->routeIs('seller.subcategory.requests') ? 'show' : '' }}">
                <li class="nav-item {{ request()->routeIs('seller.category.requests') ? 'active' : '' }}">
                    <a href="{{ route('seller.category.requests') }}" class="nav-link">
                        <span class="nav-text">Kategori Talepleri</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('seller.subcategory.requests') ? 'active' : '' }}">
                    <a href="{{ route('seller.subcategory.requests') }}" class="nav-link">
                        <span class="nav-text">Alt Kategori Talepleri</span>
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Settings Section -->
        <li class="nav-section">
            <span class="nav-section-title">AYARLAR</span>
        </li>
        
        <!-- Account Settings -->
        <li class="nav-item dropdown {{ request()->routeIs('seller.profile') || request()->routeIs('seller.password.change') ? 'active show' : '' }}">
            <a href="javascript:;" class="nav-link dropdown-toggle">
                <i class="bi bi-person-gear nav-icon"></i>
                <span class="nav-text">Hesap Ayarları</span>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </a>
            <ul class="nav-dropdown {{ request()->routeIs('seller.profile') || request()->routeIs('seller.password.change') ? 'show' : '' }}">
                <li class="nav-item {{ request()->routeIs('seller.profile') ? 'active' : '' }}">
                    <a href="{{ route('seller.profile') }}" class="nav-link">
                        <i class="bi bi-person nav-icon"></i>
                        <span class="nav-text">Profil</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('seller.password.change') ? 'active' : '' }}">
                    <a href="{{ route('seller.password.change') }}" class="nav-link">
                        <i class="bi bi-key nav-icon"></i>
                        <span class="nav-text">Şifre Değiştir</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<!-- Sidebar Footer -->
<div class="sidebar-footer">
    <div class="storage-info glass-card p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="small text-muted">Depolama Alanı</span>
            <span class="small fw-bold">2.5GB / 5GB</span>
        </div>
        <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-gradient-primary" role="progressbar" style="width: 50%"></div>
        </div>
    </div>
    
    <div class="help-center text-center">
        <a href="#" class="btn btn-sm btn-glass w-100">
            <i class="bi bi-question-circle me-2"></i>Yardım Merkezi
        </a>
    </div>
</div>

<!-- Enhanced Sidebar Styles -->
<style>
/* Section Titles */
.nav-section {
    padding: 20px 24px 10px;
    pointer-events: none;
}

.nav-section-title {
    font-size: 11px;
    font-weight: 700;
    color: rgba(0, 0, 0, 0.4);
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Navigation Badges */
.nav-badge {
    position: absolute;
    right: 24px;
    top: 50%;
    transform: translateY(-50%);
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: var(--primary-blue);
    color: white;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition-base);
}

.nav-badge.bg-danger {
    background: var(--danger);
    animation: pulse 2s infinite;
}

/* Dropdown Icon */
.dropdown-icon {
    position: absolute;
    right: 24px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    transition: transform 0.3s ease;
}

.nav-item.show .dropdown-icon {
    transform: translateY(-50%) rotate(180deg);
}

/* Enhanced Active State */
.nav-item.active > .nav-link {
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
    color: white;
    box-shadow: 0 4px 20px rgba(169, 0, 0, 0.3);
}

.nav-item.active > .nav-link::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 60%;
    background: white;
    border-radius: 3px;
    opacity: 0.8;
}

/* Sidebar Footer */
.sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px;
    background: rgba(255, 255, 255, 0.95);
    border-top: 1px solid rgba(0, 0, 0, 0.08);
}

body.sidebar-collapsed .sidebar-footer {
    padding: 20px 10px;
}

.storage-info {
    background: rgba(0, 81, 187, 0.05);
    border: 1px solid rgba(0, 81, 187, 0.1);
}

.progress {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 3px;
    overflow: hidden;
}

.bg-gradient-primary {
    background: linear-gradient(90deg, var(--primary-blue), var(--secondary-blue));
}

/* Help Center Button */
.help-center .btn-glass {
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(0, 0, 0, 0.1);
    color: var(--gray-700);
    font-size: 13px;
    padding: 8px 16px;
    transition: var(--transition-base);
}

.help-center .btn-glass:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Collapsed State Adjustments */
body.sidebar-collapsed .nav-section-title,
body.sidebar-collapsed .storage-info,
body.sidebar-collapsed .help-center span {
    display: none;
}

body.sidebar-collapsed .nav-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    transform: none;
}

body.sidebar-collapsed .help-center .btn-glass {
    padding: 8px;
}

body.sidebar-collapsed .help-center .btn-glass i {
    margin: 0;
}

/* Hover Effects Enhancement */
.nav-link {
    position: relative;
    overflow: hidden;
}

.nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 255, 255, 0.2), 
        transparent);
    transition: left 0.6s ease;
}

.nav-link:hover::before {
    left: 100%;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .nav-section {
        padding: 15px 20px 8px;
    }
    
    .sidebar-footer {
        position: static;
        padding: 15px;
    }
}

/* Smooth Scroll Behavior */
.sidebar-menu {
    scroll-behavior: smooth;
}

/* Custom Scrollbar Enhancement */
.sidebar-menu::-webkit-scrollbar {
    width: 4px;
}

.sidebar-menu::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.02);
}

.sidebar-menu::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 2px;
    transition: background 0.3s;
}

.sidebar-menu::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.2);
}
</style>