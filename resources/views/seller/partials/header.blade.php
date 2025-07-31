<!-- Modern Glass Morphism Header -->
<div class="header-content">
    <!-- Left Section -->
    <div class="header-left">
        <!-- Sidebar Toggle -->
        <button class="sidebar-toggle btn-glass" type="button">
            <i class="bi bi-list fs-5"></i>
        </button>
        
        <!-- Search Bar -->
        <div class="header-search d-none d-lg-block">
            <i class="bi bi-search header-search-icon"></i>
            <input type="text" 
                   class="form-control" 
                   placeholder="Ürün, sipariş veya müşteri ara..." 
                   id="globalSearch">
        </div>
    </div>
    
    <!-- Right Section -->
    <div class="header-right">
        <!-- Quick Actions -->
        <div class="header-actions d-flex align-items-center gap-2">
            <!-- Add Product Button -->
            <a href="{{ route('seller.products') }}" 
               class="header-action d-none d-md-flex" 
               data-bs-toggle="tooltip" 
               data-bs-placement="bottom" 
               title="Yeni Ürün Ekle">
                <i class="bi bi-plus-lg"></i>
            </a>
            
            <!-- Messages -->
            <div class="dropdown">
                <button class="header-action position-relative" 
                        type="button" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    <i class="bi bi-chat-dots"></i>
                    <span class="notification-badge">3</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end glass-dropdown message-dropdown">
                    <div class="dropdown-header">
                        <h6 class="mb-0">Mesajlar</h6>
                        <a href="#" class="text-primary small">Tümünü Gör</a>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-body">
                        <!-- Message Item -->
                        <a href="#" class="dropdown-item message-item">
                            <div class="d-flex align-items-start">
                                <img src="{{ asset('admin/vendors/images/photo1.jpg') }}" 
                                     alt="User" 
                                     class="avatar-sm rounded-circle me-3">
                                <div class="flex-1">
                                    <h6 class="mb-1 fs-sm">Ahmet Yılmaz</h6>
                                    <p class="text-muted small mb-1">Ürün hakkında bilgi almak istiyorum...</p>
                                    <small class="text-muted">5 dakika önce</small>
                                </div>
                            </div>
                        </a>
                        <!-- Message Item -->
                        <a href="#" class="dropdown-item message-item">
                            <div class="d-flex align-items-start">
                                <img src="{{ asset('admin/vendors/images/photo2.jpg') }}" 
                                     alt="User" 
                                     class="avatar-sm rounded-circle me-3">
                                <div class="flex-1">
                                    <h6 class="mb-1 fs-sm">Ayşe Demir</h6>
                                    <p class="text-muted small mb-1">Siparişim ne zaman kargoya verilecek?</p>
                                    <small class="text-muted">1 saat önce</small>
                                </div>
                            </div>
                        </a>
                        <!-- Message Item -->
                        <a href="#" class="dropdown-item message-item">
                            <div class="d-flex align-items-start">
                                <img src="{{ asset('admin/vendors/images/photo3.jpg') }}" 
                                     alt="User" 
                                     class="avatar-sm rounded-circle me-3">
                                <div class="flex-1">
                                    <h6 class="mb-1 fs-sm">Mehmet Kaya</h6>
                                    <p class="text-muted small mb-1">Toplu sipariş için indirim var mı?</p>
                                    <small class="text-muted">2 saat önce</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Notifications -->
            <div class="dropdown">
                <button class="header-action notification-btn position-relative" 
                        type="button" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge pulse">5</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end glass-dropdown notification-dropdown">
                    <div class="dropdown-header">
                        <h6 class="mb-0">Bildirimler</h6>
                        <a href="#" class="text-primary small">Tümünü Okundu İşaretle</a>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-body">
                        <!-- Notification Item -->
                        <a href="#" class="dropdown-item notification-item unread">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon bg-success-soft text-success">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <div class="flex-1 ms-3">
                                    <h6 class="mb-1 fs-sm">Yeni Sipariş</h6>
                                    <p class="text-muted small mb-1">3 adet ürün için yeni sipariş aldınız.</p>
                                    <small class="text-muted">2 dakika önce</small>
                                </div>
                            </div>
                        </a>
                        <!-- Notification Item -->
                        <a href="#" class="dropdown-item notification-item unread">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon bg-warning-soft text-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <div class="flex-1 ms-3">
                                    <h6 class="mb-1 fs-sm">Stok Uyarısı</h6>
                                    <p class="text-muted small mb-1">5 ürününüzde kritik stok seviyesi!</p>
                                    <small class="text-muted">30 dakika önce</small>
                                </div>
                            </div>
                        </a>
                        <!-- Notification Item -->
                        <a href="#" class="dropdown-item notification-item">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon bg-info-soft text-info">
                                    <i class="bi bi-star"></i>
                                </div>
                                <div class="flex-1 ms-3">
                                    <h6 class="mb-1 fs-sm">Yeni Değerlendirme</h6>
                                    <p class="text-muted small mb-1">Ürününüz 5 yıldız aldı!</p>
                                    <small class="text-muted">1 saat önce</small>
                                </div>
                            </div>
                        </a>
                        <!-- Notification Item -->
                        <a href="#" class="dropdown-item notification-item">
                            <div class="d-flex align-items-start">
                                <div class="notification-icon bg-danger-soft text-danger">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                                <div class="flex-1 ms-3">
                                    <h6 class="mb-1 fs-sm">Sipariş İptali</h6>
                                    <p class="text-muted small mb-1">1 siparişiniz iptal edildi.</p>
                                    <small class="text-muted">3 saat önce</small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="dropdown-footer text-center">
                        <a href="#" class="text-primary small">Tüm Bildirimleri Gör</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- User Menu -->
        <div class="dropdown">
            <button class="user-menu" 
                    type="button" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('admin/vendors/images/photo2.jpg') }}" 
                     alt="{{ auth()->user()->name }}" 
                     class="user-avatar">
                <div class="user-info d-none d-md-block">
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <span class="user-role">Satıcı</span>
                </div>
                <i class="bi bi-chevron-down ms-2"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end glass-dropdown user-dropdown">
                <div class="dropdown-header text-center py-3">
                    <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('admin/vendors/images/photo2.jpg') }}" 
                         alt="{{ auth()->user()->name }}" 
                         class="avatar-lg rounded-circle mb-2">
                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                    <small class="text-muted">{{ auth()->user()->email }}</small>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('seller.profile') }}">
                    <i class="bi bi-person me-2"></i> Profilim
                </a>
                <a class="dropdown-item" href="{{ route('seller.password.change') }}">
                    <i class="bi bi-key me-2"></i> Şifre Değiştir
                </a>
                <a class="dropdown-item" href="#">
                    <i class="bi bi-gear me-2"></i> Ayarlar
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="#"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right me-2"></i> Çıkış Yap
                </a>
                <form id="logout-form" action="{{ route('seller.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom Header Styles -->
<style>
/* Avatar Sizes */
.avatar-sm {
    width: 32px;
    height: 32px;
    object-fit: cover;
}

.avatar-lg {
    width: 64px;
    height: 64px;
    object-fit: cover;
}

/* Glass Dropdown Styles */
.glass-dropdown {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    min-width: 320px;
    margin-top: 10px;
    padding: 0;
    animation: fadeInDown 0.3s ease;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-header {
    padding: 16px 20px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dropdown-body {
    max-height: 400px;
    overflow-y: auto;
    padding: 8px;
}

.dropdown-footer {
    padding: 12px 20px;
    border-top: 1px solid rgba(0, 0, 0, 0.08);
}

/* Message & Notification Items */
.message-item,
.notification-item {
    padding: 12px 16px;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 4px;
}

.message-item:hover,
.notification-item:hover {
    background: rgba(0, 81, 187, 0.08);
    transform: translateX(4px);
}

.notification-item.unread {
    background: rgba(0, 81, 187, 0.05);
    border-left: 3px solid var(--primary-blue);
}

.notification-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    flex-shrink: 0;
}

/* Background color utilities */
.bg-success-soft { background: rgba(16, 185, 129, 0.1); }
.bg-warning-soft { background: rgba(245, 158, 11, 0.1); }
.bg-info-soft { background: rgba(59, 130, 246, 0.1); }
.bg-danger-soft { background: rgba(239, 68, 68, 0.1); }

/* Pulse Animation for Notifications */
@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.7;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.notification-badge.pulse {
    animation: pulse 2s infinite;
}

/* Scrollbar Styling */
.dropdown-body::-webkit-scrollbar {
    width: 6px;
}

.dropdown-body::-webkit-scrollbar-track {
    background: transparent;
}

.dropdown-body::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 3px;
}

.dropdown-body::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.2);
}

/* User Dropdown Specific */
.user-dropdown {
    min-width: 280px;
}

.user-dropdown .dropdown-item {
    padding: 10px 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.user-dropdown .dropdown-item:hover {
    background: rgba(0, 81, 187, 0.08);
    padding-left: 24px;
}

.user-dropdown .dropdown-item i {
    font-size: 16px;
}

/* Font size utilities */
.fs-sm {
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .glass-dropdown {
        min-width: 280px;
    }
    
    .notification-dropdown,
    .message-dropdown {
        position: fixed;
        left: 10px;
        right: 10px;
        width: auto;
        max-width: 400px;
        margin: 0 auto;
    }
}
</style>