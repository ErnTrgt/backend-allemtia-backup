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
            <button type="button" class="header-btn" id="notificationBtn">
                <i class="bi bi-bell"></i>
                @php
                    $unreadNotifications = auth()->user()->unreadNotifications()->count() ?? 0;
                @endphp
                @if($unreadNotifications > 0)
                    <span class="notification-badge"></span>
                @endif
            </button>
            
            <button type="button" class="header-btn" id="messageBtn">
                <i class="bi bi-envelope"></i>
                @php
                    $unreadMessages = 0; // Implement this based on your messaging system
                @endphp
                @if($unreadMessages > 0)
                    <span class="notification-badge"></span>
                @endif
            </button>
            
            <div class="dropdown user-menu">
                <button type="button" class="user-menu-toggle" data-toggle="dropdown">
                    <img src="{{ auth()->user()->profile_photo_url ?? asset('/admin/vendors/images/photo2.jpg') }}" alt="User" class="user-avatar">
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