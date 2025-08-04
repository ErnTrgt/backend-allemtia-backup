@extends('layouts.admin-modern')

@section('title', 'Kullanıcılar')
@section('header-title', 'Kullanıcılar')

@section('content')
<div class="users-container">
    <!-- Page Header -->
    <div class="page-header-wrapper">
        <div class="page-header-left">
            <h1 class="page-title">Kullanıcılar</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kullanıcılar</li>
                </ol>
            </nav>
        </div>
        <div class="page-header-right">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-circle me-2"></i>
                Yeni Kullanıcı
            </button>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="user-stat-card glass-card total">
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($users->count()) }}</h3>
                    <p>Toplam Kullanıcı</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="user-stat-card glass-card admin">
                <div class="stat-icon">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($users->where('role', 'admin')->count()) }}</h3>
                    <p>Yönetici</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="user-stat-card glass-card seller">
                <div class="stat-icon">
                    <i class="bi bi-shop"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($users->where('role', 'seller')->count()) }}</h3>
                    <p>Satıcı</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="user-stat-card glass-card buyer">
                <div class="stat-icon">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($users->where('role', 'buyer')->count()) }}</h3>
                    <p>Alıcı</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter and Search Section -->
    <div class="table-card glass-card">
        <div class="table-header">
            <div class="table-filters">
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="all">
                        <i class="bi bi-grid-3x3-gap me-1"></i>
                        Tümü
                    </button>
                    <button class="filter-btn" data-filter="admin">
                        <i class="bi bi-shield me-1"></i>
                        Yöneticiler
                    </button>
                    <button class="filter-btn" data-filter="seller">
                        <i class="bi bi-shop me-1"></i>
                        Satıcılar
                    </button>
                    <button class="filter-btn" data-filter="buyer">
                        <i class="bi bi-bag me-1"></i>
                        Alıcılar
                    </button>
                </div>
                
                <div class="table-actions">
                    <div class="search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" class="table-search" placeholder="Kullanıcı ara..." id="userSearch">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table users-table" id="usersTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Kullanıcı</th>
                        <th>E-posta</th>
                        <th>Telefon</th>
                        <th>Rol</th>
                        <th>Durum</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr data-role="{{ $user->role }}">
                        <td>
                            <input type="checkbox" class="form-check-input user-select" value="{{ $user->id }}">
                        </td>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar {{ $user->role }}">
                                    @if($user->avatar)
                                        <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="user-info">
                                    <h6>{{ $user->name }}</h6>
                                    <span class="text-muted">ID: #{{ $user->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            <span class="role-badge {{ $user->role }}">
                                @if($user->role == 'admin')
                                    <i class="bi bi-shield-fill me-1"></i>
                                @elseif($user->role == 'seller')
                                    <i class="bi bi-shop me-1"></i>
                                @else
                                    <i class="bi bi-bag me-1"></i>
                                @endif
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" 
                                       class="status-toggle" 
                                       data-id="{{ $user->id }}"
                                       {{ $user->status == 'approved' ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </td>
                        <td>{{ $user->created_at->format('d.m.Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action" data-bs-toggle="modal" data-bs-target="#viewUserModal{{ $user->id }}" title="Görüntüle">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn-action" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-action text-danger" onclick="deleteUser({{ $user->id }})" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Yeni Kullanıcı Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Kişisel Bilgiler -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-person-circle"></i>
                            Kişisel Bilgiler
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ad Soyad</label>
                                    <input type="text" class="form-control" name="name" required 
                                           placeholder="Örn: Ahmet Yılmaz">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">E-posta</label>
                                    <input type="email" class="form-control" name="email" required 
                                           placeholder="ornek@email.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İletişim ve Rol -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-telephone"></i>
                            İletişim ve Rol Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Telefon</label>
                                    <input type="text" class="form-control" name="phone" 
                                           placeholder="0555 123 45 67">
                                    <small class="text-muted">İsteğe bağlı</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kullanıcı Rolü</label>
                                    <select class="form-control" name="role" required>
                                        <option value="buyer" selected>Alıcı</option>
                                        <option value="seller">Satıcı</option>
                                        <option value="admin">Yönetici</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Güvenlik -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-shield-lock"></i>
                            Güvenlik Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Şifre</label>
                                    <input type="password" class="form-control" name="password" required 
                                           placeholder="••••••••">
                                    <small class="text-muted">En az 8 karakter</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Şifre Tekrar</label>
                                    <input type="password" class="form-control" name="password_confirmation" required 
                                           placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-message">
                        <i class="bi bi-info-circle-fill"></i>
                        <div class="info-message-content">
                            <div class="info-message-title">Bilgi</div>
                            <div class="info-message-text">
                                Kullanıcı oluşturulduktan sonra e-posta adresine giriş bilgileri otomatik olarak gönderilecektir.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary-red); border-color: var(--primary-red);">
                        <i class="bi bi-check-lg me-1"></i>
                        Kullanıcı Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View User Modals -->
@foreach($users as $user)
<div class="modal fade" id="viewUserModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-fill me-2"></i>
                    Kullanıcı Detayları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <!-- Kullanıcı Bilgileri -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-person-circle"></i>
                        Kişisel Bilgiler
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Ad Soyad</label>
                                <div class="form-control-static">{{ $user->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">E-posta</label>
                                <div class="form-control-static">{{ $user->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İletişim ve Rol -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-telephone"></i>
                        İletişim ve Rol Bilgileri
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Telefon</label>
                                <div class="form-control-static">{{ $user->phone ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Kullanıcı Rolü</label>
                                <div class="form-control-static">
                                    <span class="role-badge {{ $user->role }}">
                                        @if($user->role == 'admin')
                                            <i class="bi bi-shield-fill me-1"></i>
                                        @elseif($user->role == 'seller')
                                            <i class="bi bi-shop me-1"></i>
                                        @else
                                            <i class="bi bi-bag me-1"></i>
                                        @endif
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Durum</label>
                                <div class="form-control-static">
                                    @if($user->status == 'approved')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-warning">Beklemede</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ek Bilgiler -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-calendar3"></i>
                        Kayıt Bilgileri
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Kayıt Tarihi</label>
                                <div class="form-control-static">{{ $user->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Son Güncelleme</label>
                                <div class="form-control-static">{{ $user->updated_at->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->role == 'seller')
                <div class="info-message">
                    <i class="bi bi-info-circle-fill"></i>
                    <div class="info-message-content">
                        <div class="info-message-title">Satıcı Bilgisi</div>
                        <div class="info-message-text">
                            Bu kullanıcı bir satıcıdır. Ürün, sipariş ve mağaza bilgilerine erişebilir.
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Edit User Modals -->
@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Kullanıcı Düzenle: <span class="badge bg-light text-dark">{{ $user->name }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Kişisel Bilgiler -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-person-circle"></i>
                            Kişisel Bilgiler
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ad Soyad</label>
                                    <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">E-posta</label>
                                    <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İletişim ve Rol -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-telephone"></i>
                            İletişim ve Rol Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Telefon</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $user->phone }}" 
                                           placeholder="0555 123 45 67">
                                    <small class="text-muted">İsteğe bağlı</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Kullanıcı Rolü</label>
                                    <select class="form-control" name="role" required>
                                        <option value="buyer" {{ $user->role == 'buyer' ? 'selected' : '' }}>Alıcı</option>
                                        <option value="seller" {{ $user->role == 'seller' ? 'selected' : '' }}>Satıcı</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Yönetici</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Hesap Durumu</label>
                                    <select class="form-control" name="status" required>
                                        <option value="approved" {{ $user->status == 'approved' ? 'selected' : '' }}>Aktif</option>
                                        <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Beklemede</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="info-message">
                        <i class="bi bi-info-circle-fill"></i>
                        <div class="info-message-content">
                            <div class="info-message-title">Güncelleme Bilgisi</div>
                            <div class="info-message-text">
                                Kullanıcı bilgileri güncellendikten sonra kullanıcıya bilgilendirme e-postası gönderilecektir.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary-red); border-color: var(--primary-red);">
                        <i class="bi bi-check-lg me-1"></i>
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<style>
/* Users Page Styles */
.users-container {
    max-width: 1400px;
    margin: 0 auto;
}

.page-header-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-xl);
}

.page-header-left .page-title {
    margin-bottom: var(--spacing-sm);
}

/* User Stat Cards */
.user-stat-card {
    padding: var(--spacing-lg);
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    height: 100%;
    transition: all 0.3s ease;
}

.user-stat-card:hover {
    transform: translateY(-4px);
}

.user-stat-card .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--white);
}

.user-stat-card.total .stat-icon {
    background: linear-gradient(135deg, var(--gray-600), var(--gray-700));
}

.user-stat-card.admin .stat-icon {
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
}

.user-stat-card.seller .stat-icon {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
}

.user-stat-card.buyer .stat-icon {
    background: linear-gradient(135deg, #10B981, #059669);
}

.user-stat-card .stat-content h3 {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-xs);
}

.user-stat-card .stat-content p {
    font-size: 14px;
    color: var(--gray-600);
    margin: 0;
}

/* Table Card */
.table-card {
    margin-bottom: var(--spacing-xl);
}

.table-header {
    padding: var(--spacing-lg);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.table-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: var(--spacing-lg);
}

.filter-group {
    display: flex;
    gap: var(--spacing-sm);
}

.filter-btn {
    padding: var(--spacing-sm) var(--spacing-md);
    background: transparent;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-size: 14px;
    color: var(--gray-700);
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
}

.filter-btn:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
}

.filter-btn.active {
    background: var(--primary-red);
    border-color: var(--primary-red);
    color: var(--white);
}

.table-actions {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.search-wrapper {
    position: relative;
}

.search-wrapper i {
    position: absolute;
    left: var(--spacing-md);
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-500);
}

.table-search {
    padding: var(--spacing-sm) var(--spacing-md) var(--spacing-sm) var(--spacing-2xl);
    background: var(--gray-50);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    width: 300px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.table-search:focus {
    outline: none;
    background: var(--white);
    border-color: var(--primary-red);
    box-shadow: 0 0 0 3px rgba(169, 0, 0, 0.1);
}

/* Users Table */
.users-table {
    width: 100%;
}

.users-table th {
    padding: var(--spacing-md);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.users-table td {
    padding: var(--spacing-md);
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.users-table tbody tr:hover {
    background: rgba(0, 0, 0, 0.01);
}

/* User Cell */
.user-cell {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: var(--white);
    font-size: 16px;
}

.user-avatar.admin {
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
}

.user-avatar.seller {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
}

.user-avatar.buyer {
    background: linear-gradient(135deg, #10B981, #059669);
}

.user-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.user-info h6 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    color: var(--gray-900);
}

.user-info span {
    font-size: 12px;
    color: var(--gray-500);
}

/* Role Badge */
.role-badge {
    display: inline-flex;
    align-items: center;
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 500;
}

.role-badge.admin {
    background: rgba(169, 0, 0, 0.1);
    color: var(--primary-red);
}

.role-badge.seller {
    background: rgba(0, 81, 187, 0.1);
    color: var(--primary-blue);
}

.role-badge.buyer {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

/* Status Toggle */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--gray-300);
    transition: .4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #10B981;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: var(--spacing-xs);
}

.btn-action {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-action:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
    transform: scale(1.05);
}

.btn-action.text-danger:hover {
    background: rgba(239, 68, 68, 0.1);
    border-color: #EF4444;
    color: #EF4444;
}

/* Button Styles */
.btn {
    padding: var(--spacing-sm) var(--spacing-lg);
    border-radius: var(--radius-sm);
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
    color: var(--white);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
}

.btn-secondary:hover {
    background: var(--gray-300);
}

.btn-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
}

/* Modal Styles - Coupons Design System */
.modal-content {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(30px);
    -webkit-backdrop-filter: blur(30px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: var(--radius-xl);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%);
    border-bottom: 1px solid rgba(169, 0, 0, 0.1);
    padding: var(--spacing-xl);
    position: relative;
}

.modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(169, 0, 0, 0.3), transparent);
}

.modal-title {
    font-size: var(--text-xl);
    font-weight: var(--font-semibold);
    color: var(--gray-800);
    display: flex;
    align-items: center;
}

.modal-title i {
    color: var(--primary-red);
}

.modal-title .badge {
    margin-left: var(--spacing-sm);
    font-size: var(--text-sm);
    font-weight: normal;
}

.btn-close {
    background: rgba(0, 0, 0, 0.05);
    border-radius: var(--radius-sm);
    opacity: 0.7;
    transition: all var(--transition-base) var(--ease-in-out);
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    line-height: 1;
    color: var(--gray-600);
}

.btn-close:hover {
    opacity: 1;
    background: rgba(0, 0, 0, 0.1);
    transform: rotate(90deg);
}

.modal-body {
    padding: var(--spacing-xl);
    max-height: 70vh;
    overflow-y: auto;
}

/* Modal Scrollbar */
.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.02);
    border-radius: var(--radius-sm);
}

.modal-body::-webkit-scrollbar-thumb {
    background: rgba(169, 0, 0, 0.2);
    border-radius: var(--radius-sm);
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: rgba(169, 0, 0, 0.3);
}

.modal-footer {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    padding: var(--spacing-lg) var(--spacing-xl);
    gap: var(--spacing-md);
}

/* Form Sections */
.form-section {
    background: rgba(240, 248, 255, 0.3);
    border-radius: var(--radius-md);
    padding: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.form-section:last-of-type {
    margin-bottom: var(--spacing-lg);
}

.form-section-title {
    font-size: var(--text-base);
    font-weight: var(--font-semibold);
    color: var(--gray-700);
    margin-bottom: var(--spacing-md);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.form-section-title i {
    color: var(--primary-red);
}

/* Form Elements */
.form-group {
    margin-bottom: var(--spacing-lg);
}

.form-label {
    display: block;
    font-weight: var(--font-medium);
    color: var(--gray-700);
    margin-bottom: var(--spacing-xs);
    font-size: var(--text-sm);
}

.form-control,
.form-select {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md);
    background: rgba(255, 255, 255, 0.8);
    border: 2px solid rgba(0, 0, 0, 0.08);
    border-radius: var(--radius-sm);
    font-size: var(--text-sm);
    transition: all var(--transition-base) var(--ease-in-out);
    font-family: inherit;
}

.form-control:focus,
.form-select:focus {
    outline: none;
    background: white;
    border-color: var(--primary-red);
    box-shadow: 0 0 0 4px rgba(169, 0, 0, 0.1);
}

.form-control::placeholder {
    color: var(--gray-400);
}

select.form-control {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right var(--spacing-sm) center;
    background-size: 16px 12px;
    padding-right: var(--spacing-2xl);
}

/* Info Message */
.info-message {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-sm);
    padding: var(--spacing-md);
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: var(--radius-md);
    margin-top: var(--spacing-lg);
}

.info-message i {
    color: var(--info);
    font-size: 20px;
    flex-shrink: 0;
}

.info-message-content {
    flex: 1;
}

.info-message-title {
    font-weight: var(--font-semibold);
    color: var(--gray-800);
    margin-bottom: 2px;
}

.info-message-text {
    color: var(--gray-600);
    font-size: var(--text-sm);
}

/* Form Control Static */
.form-control-static {
    padding: var(--spacing-sm) 0;
    font-size: 14px;
    color: var(--gray-900);
    font-weight: 500;
}

/* Modal Buttons */
.modal .btn {
    padding: var(--spacing-sm) var(--spacing-xl);
    border-radius: var(--radius-sm);
    font-weight: var(--font-medium);
    transition: all var(--transition-base) var(--ease-in-out);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: var(--text-sm);
}

.modal .btn-secondary {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%);
    color: var(--gray-700);
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.modal .btn-secondary:hover {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.08) 0%, rgba(0, 0, 0, 0.12) 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.modal .btn-primary {
    background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%);
    color: white;
    box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25);
    position: relative;
    overflow: hidden;
}

.modal .btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.modal .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(169, 0, 0, 0.35);
}

.modal .btn-primary:hover::before {
    left: 100%;
}

/* Modal Animation */
.modal.fade .modal-dialog {
    transform: scale(0.8) translateY(-100px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal.show .modal-dialog {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* Modal Backdrop Enhancement */
.modal-backdrop {
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

/* Responsive Modal */
@media (max-width: 767px) {
    .modal-dialog {
        margin: var(--spacing-md);
    }
    
    .modal-body {
        padding: var(--spacing-lg);
    }
    
    .modal .btn {
        width: 100%;
        justify-content: center;
    }

    .modal-footer {
        flex-direction: column;
        gap: var(--spacing-sm);
    }
    
    .modal-footer button {
        width: 100%;
    }
}

/* Responsive */
@media (max-width: 991px) {
    .page-header-wrapper {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-md);
    }
    
    .table-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-group {
        width: 100%;
        overflow-x: auto;
    }
    
    .table-search {
        width: 100%;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
@endsection

@push('scripts')
<script>
// DataTable Initialization
$(document).ready(function() {
    $('#usersTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Ara...",
            lengthMenu: "_MENU_ kayıt göster",
            paginate: {
                first: "İlk",
                last: "Son",
                next: "Sonraki",
                previous: "Önceki"
            },
            info: "_TOTAL_ kayıttan _START_ - _END_ gösteriliyor",
            infoEmpty: "Kayıt bulunamadı",
            zeroRecords: "Eşleşen kayıt bulunamadı"
        },
        dom: 'rtip'
    });
    
    // Custom search
    $('#userSearch').on('keyup', function() {
        $('#usersTable').DataTable().search(this.value).draw();
    });
});

// Filter Buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        const rows = document.querySelectorAll('#usersTable tbody tr');
        
        rows.forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
            } else {
                if (row.dataset.role === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        // Redraw DataTable
        $('#usersTable').DataTable().draw();
    });
});

// Select All Checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-select');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Status Toggle
document.querySelectorAll('.status-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const userId = this.dataset.id;
        const status = this.checked ? 'approved' : 'pending';
        
        fetch(`/admin/users/${userId}/change-status/${status}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success notification
                showNotification('Kullanıcı durumu güncellendi', 'success');
            }
        });
    });
});

// Delete User
function deleteUser(id) {
    if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
        fetch(`/admin/users/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

// Show Notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="bi bi-check-circle me-2"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Notification Styles
const style = document.createElement('style');
style.textContent = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateX(400px);
        transition: transform 0.3s ease;
        z-index: 9999;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification.success {
        border-left: 4px solid #10B981;
        color: #10B981;
    }
`;
document.head.appendChild(style);
</script>
@endpush