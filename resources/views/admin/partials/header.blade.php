<!-- Admin Header Component -->
<header class="admin-header">
    <div class="header-content">
        <div class="header-left">
            <button class="menu-toggle" type="button">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="header-title">@yield('header-title', 'Dashboard')</h1>
        </div>
        
        <div class="header-right">
            <!-- Actions -->
            <div class="header-actions">
                <!-- Notifications -->
                <button class="header-action-btn" data-dropdown-toggle="notificationDropdown">
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge"></span>
                </button>
                
                <!-- Messages -->
                <button class="header-action-btn" data-dropdown-toggle="messageDropdown">
                    <i class="bi bi-envelope"></i>
                </button>
                
                <!-- User Menu -->
                <div class="user-menu" data-dropdown-toggle="userDropdown">
                    <img src="{{ asset('admin/src/images/user-avatar.png') }}" alt="User" class="user-avatar">
                    <span class="user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <i class="bi bi-chevron-down"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dropdown Menus -->
    <div class="dropdown-menu" id="notificationDropdown">
        <div class="dropdown-header">Bildirimler</div>
        <div class="dropdown-content">
            <p class="text-center text-muted p-3">Yeni bildirim yok</p>
        </div>
    </div>
    
    <div class="dropdown-menu" id="messageDropdown">
        <div class="dropdown-header">Mesajlar</div>
        <div class="dropdown-content">
            <p class="text-center text-muted p-3">Yeni mesaj yok</p>
        </div>
    </div>
    
    <div class="dropdown-menu" id="userDropdown">
        <a href="{{ route('admin.profile') }}" class="dropdown-item">
            <i class="bi bi-person"></i> Profil
        </a>
        <div class="dropdown-divider"></div>
        <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
                <i class="bi bi-box-arrow-right"></i> Çıkış Yap
            </button>
        </form>
    </div>
</header>