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
                    @php
                        $productCount = auth()->user()->products()->count() ?? 0;
                    @endphp
                    @if($productCount > 0)
                        <span class="nav-badge">{{ $productCount }}</span>
                    @endif
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('seller.orders') }}" class="nav-link {{ request()->routeIs('seller.orders*') ? 'active' : '' }}">
                    <i class="bi bi-bag-check nav-icon"></i>
                    <span class="nav-text">Siparişler</span>
                    @php
                        $orderCount = auth()->user()->orders()->where('status', 'pending')->count() ?? 0;
                    @endphp
                    @if($orderCount > 0)
                        <span class="nav-badge">{{ $orderCount }}</span>
                    @endif
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