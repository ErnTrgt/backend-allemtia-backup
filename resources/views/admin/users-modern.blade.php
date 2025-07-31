@extends('layouts.admin-modern')

@section('title', 'Kullanıcılar')

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
                    
                    <div class="dropdown">
                        <button class="btn-icon" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Excel İndir</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i>Yazdır</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Ayarlar</a></li>
                        </ul>
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
                                <button class="btn-action" onclick="viewUser({{ $user->id }})" title="Görüntüle">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn-action" onclick="editUser({{ $user->id }})" title="Düzenle">
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
                <h5 class="modal-title">Yeni Kullanıcı Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-posta</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefon</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="role" required>
                                <option value="buyer">Alıcı</option>
                                <option value="seller">Satıcı</option>
                                <option value="admin">Yönetici</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Şifre</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Şifre Tekrar</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Kullanıcı Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

/* Modal Styles */
.modal-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
}

.modal-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding: var(--spacing-lg);
}

.modal-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--gray-900);
}

.modal-body {
    padding: var(--spacing-lg);
}

.modal-footer {
    border-top: 1px solid rgba(0, 0, 0, 0.05);
    padding: var(--spacing-lg);
}

.form-label {
    font-size: 14px;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: var(--spacing-sm);
}

.form-control,
.form-select {
    padding: var(--spacing-sm) var(--spacing-md);
    background: var(--white);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-size: 14px;
    transition: all 0.2s ease;
}

.form-control:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary-red);
    box-shadow: 0 0 0 3px rgba(169, 0, 0, 0.1);
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

// View User
function viewUser(id) {
    // Implement view user logic
    window.location.href = `/admin/users/${id}`;
}

// Edit User
function editUser(id) {
    // Implement edit user logic
    window.location.href = `/admin/users/${id}/edit`;
}

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