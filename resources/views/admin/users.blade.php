@extends('layouts.admin-modern')

@section('title', 'Kullanıcılar')
@section('header-title', 'Kullanıcı Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/users.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Kullanıcı Yönetimi</h1>
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
        <span class="breadcrumb-separator">/</span>
        <span>Kullanıcılar</span>
    </div>
</div>

<!-- Page Actions -->
<div class="page-actions">
    <div class="page-actions-left">
        <!-- Search -->
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Kullanıcı ara..." id="userSearch">
        </div>
        
        <!-- Filter by Role -->
        <div class="filter-group">
            <button class="filter-btn {{ !request('role') ? 'active' : '' }}" data-filter="all">
                Tümü
            </button>
            <button class="filter-btn {{ request('role') == 'admin' ? 'active' : '' }}" data-filter="admin">
                Yöneticiler
            </button>
            <button class="filter-btn {{ request('role') == 'seller' ? 'active' : '' }}" data-filter="seller">
                Satıcılar
            </button>
            <button class="filter-btn {{ request('role') == 'buyer' ? 'active' : '' }}" data-filter="buyer">
                Alıcılar
            </button>
        </div>
    </div>
    
    <div class="page-actions-right">
        <!-- Add New User -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus"></i>
            Yeni Kullanıcı
        </button>
    </div>
</div>

<!-- Bulk Actions (Hidden by default) -->
<div class="bulk-actions" id="bulkActions">
    <div class="bulk-select-info">
        <span id="selectedCount">0</span> kullanıcı seçildi
    </div>
    <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
        <i class="bi bi-trash"></i>
        Seçilenleri Sil
    </button>
    <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
        <i class="bi bi-x"></i>
        Seçimi Temizle
    </button>
</div>

<!-- Users Table -->
<div class="users-table-container">
    <table class="users-table">
        <thead>
            <tr>
                <th>
                    <div class="checkbox-wrapper">
                        <input type="checkbox" class="checkbox" id="selectAll">
                    </div>
                </th>
                <th>Kullanıcı</th>
                <th>Rol</th>
                <th>Durum</th>
                <th>Telefon</th>
                <th>Kayıt Tarihi</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
            <tr data-user-id="{{ $user->id }}" data-role="{{ $user->role }}">
                <td>
                    <div class="checkbox-wrapper">
                        <input type="checkbox" class="checkbox user-checkbox" value="{{ $user->id }}">
                    </div>
                </td>
                <td>
                    <div class="user-info">
                        <div class="user-avatar">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <div class="user-details">
                            <div class="user-name">{{ $user->name }}</div>
                            <div class="user-email">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @if($user->role === 'admin')
                        <span class="role-badge role-admin">Yönetici</span>
                    @elseif($user->role === 'seller')
                        <span class="role-badge role-seller">Satıcı</span>
                    @elseif($user->role === 'buyer')
                        <span class="role-badge role-buyer">Alıcı</span>
                    @else
                        <span class="role-badge">{{ ucfirst($user->role) }}</span>
                    @endif
                </td>
                <td>
                    @if($user->status === 'approved')
                        <span class="status-badge status-active">
                            <span class="status-dot"></span>
                            Aktif
                        </span>
                    @elseif($user->status === 'pending')
                        <span class="status-badge status-pending">
                            <span class="status-dot"></span>
                            Beklemede
                        </span>
                    @else
                        <span class="status-badge status-inactive">
                            <span class="status-dot"></span>
                            Pasif
                        </span>
                    @endif
                </td>
                <td>{{ $user->phone ?? '-' }}</td>
                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view" data-tooltip="Görüntüle" onclick="viewUser({{ $user->id }})">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="action-btn edit" data-tooltip="Düzenle" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="action-btn reset" data-tooltip="Şifre Sıfırla" onclick="resetPassword({{ $user->id }})">
                            <i class="bi bi-key"></i>
                        </button>
                        <button class="action-btn delete" data-tooltip="Sil" onclick="deleteUser({{ $user->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-people empty-icon"></i>
                        <h3 class="empty-title">Kullanıcı Bulunamadı</h3>
                        <p class="empty-text">Arama kriterlerinize uygun kullanıcı bulunamadı.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($users->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $users->links('components.admin-pagination') }}
</div>
@endif

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>
                    Yeni Kullanıcı Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Personal Info -->
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Kişisel Bilgiler</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ad Soyad</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefon</label>
                            <input type="tel" name="phone" class="form-control">
                        </div>
                        
                        <!-- Account Info -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted mb-3">Hesap Bilgileri</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-posta</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Şifre</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <!-- Role -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted mb-3">Rol ve Yetki</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kullanıcı Rolü</label>
                            <select name="role" class="form-control" required>
                                <option value="admin">Yönetici</option>
                                <option value="seller">Satıcı</option>
                                <option value="buyer" selected>Alıcı</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Durum</label>
                            <select name="status" class="form-control" required>
                                <option value="approved" selected>Aktif</option>
                                <option value="pending">Beklemede</option>
                                <option value="rejected">Pasif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Kullanıcı Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
@foreach ($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Kullanıcı Düzenle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">x</button>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Personal Info -->
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Kişisel Bilgiler</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ad Soyad</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefon</label>
                            <input type="tel" name="phone" class="form-control" value="{{ $user->phone }}">
                        </div>
                        
                        <!-- Account Info -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted mb-3">Hesap Bilgileri</h6>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">E-posta</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                        
                        <!-- Role -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted mb-3">Rol ve Yetki</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kullanıcı Rolü</label>
                            <select name="role" class="form-control" required>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Yönetici</option>
                                <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>Satıcı</option>
                                <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Alıcı</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Durum</label>
                            <select name="status" class="form-control" required>
                                <option value="approved" {{ $user->status === 'approved' ? 'selected' : '' }}>Aktif</option>
                                <option value="pending" {{ $user->status === 'pending' ? 'selected' : '' }}>Beklemede</option>
                                <option value="rejected" {{ $user->status === 'rejected' ? 'selected' : '' }}>Pasif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
// Search functionality
let searchTimer;
document.getElementById('userSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const rows = document.querySelectorAll('.users-table tbody tr');
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            const name = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
            const email = row.querySelector('.user-email')?.textContent.toLowerCase() || '';
            const phone = row.cells[4]?.textContent.toLowerCase() || '';
            
            if (name.includes(query) || email.includes(query) || phone.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }, 300);
});

// Filter by role
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.dataset.filter;
        
        // Update active state
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Filter rows
        const rows = document.querySelectorAll('.users-table tbody tr');
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            const role = row.dataset.role;
            if (filter === 'all' || role === filter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update URL without reload
        const url = new URL(window.location);
        if (filter === 'all') {
            url.searchParams.delete('role');
        } else {
            url.searchParams.set('role', filter);
        }
        window.history.pushState({}, '', url);
    });
});

// Checkbox functionality
const selectAll = document.getElementById('selectAll');
const userCheckboxes = document.querySelectorAll('.user-checkbox');
const bulkActions = document.getElementById('bulkActions');
const selectedCount = document.getElementById('selectedCount');

selectAll.addEventListener('change', function() {
    userCheckboxes.forEach(cb => {
        cb.checked = this.checked;
    });
    updateBulkActions();
});

userCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
    selectedCount.textContent = checkedCount;
    
    if (checkedCount > 0) {
        bulkActions.classList.add('show');
    } else {
        bulkActions.classList.remove('show');
    }
    
    // Update select all checkbox
    selectAll.checked = checkedCount === userCheckboxes.length && checkedCount > 0;
    selectAll.indeterminate = checkedCount > 0 && checkedCount < userCheckboxes.length;
}

function clearSelection() {
    selectAll.checked = false;
    userCheckboxes.forEach(cb => cb.checked = false);
    updateBulkActions();
}

// User actions
function viewUser(id) {
    AdminPanel.showToast('Kullanıcı detayları sayfasına yönlendiriliyorsunuz...', 'info');
    setTimeout(() => {
        window.location.href = `/admin/users/${id}`;
    }, 1500);
}

function resetPassword(id) {
    if (confirm('Bu kullanıcının şifresini sıfırlamak istediğinizden emin misiniz?')) {
        fetch(`/admin/users/${id}/reset-password`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                AdminPanel.showToast('Şifre sıfırlama bağlantısı gönderildi!', 'success');
            } else {
                AdminPanel.showToast('Bir hata oluştu!', 'error');
            }
        })
        .catch(error => {
            AdminPanel.showToast('Bir hata oluştu!', 'error');
        });
    }
}

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
                AdminPanel.showToast('Kullanıcı başarıyla silindi!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                AdminPanel.showToast('Bir hata oluştu!', 'error');
            }
        })
        .catch(error => {
            AdminPanel.showToast('Bir hata oluştu!', 'error');
        });
    }
}

function bulkDelete() {
    const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedIds.length === 0) return;
    
    if (confirm(`${selectedIds.length} kullanıcıyı silmek istediğinizden emin misiniz?`)) {
        fetch('/admin/users/bulk-delete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                AdminPanel.showToast('Seçili kullanıcılar silindi!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                AdminPanel.showToast('Bir hata oluştu!', 'error');
            }
        })
        .catch(error => {
            AdminPanel.showToast('Bir hata oluştu!', 'error');
        });
    }
}

// Initialize tooltips
document.querySelectorAll('[data-tooltip]').forEach(el => {
    el.title = el.dataset.tooltip;
});
</script>
@endpush