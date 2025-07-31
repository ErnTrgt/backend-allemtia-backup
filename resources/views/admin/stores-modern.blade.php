@extends('layouts.admin-modern')

@section('title', 'Mağazalar')

@section('content')
<div class="stores-container">
    <!-- Page Header -->
    <div class="page-header-wrapper">
        <div class="page-header-left">
            <h1 class="page-title">Mağazalar</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Mağazalar</li>
                </ol>
            </nav>
        </div>
        <div class="page-header-right">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStoreModal">
                <i class="bi bi-plus-circle me-2"></i>
                Yeni Mağaza
            </button>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="store-stat-card glass-card total">
                <div class="stat-icon">
                    <i class="bi bi-shop"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stores->count()) }}</h3>
                    <p>Toplam Mağaza</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="store-stat-card glass-card active">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stores->where('status', 'approved')->count()) }}</h3>
                    <p>Aktif Mağaza</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="store-stat-card glass-card products">
                <div class="stat-icon">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stores->sum('products_count')) }}</h3>
                    <p>Toplam Ürün</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="store-stat-card glass-card revenue">
                <div class="stat-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="stat-content">
                    <h3>₺{{ number_format($stores->sum('total_revenue'), 0) }}</h3>
                    <p>Toplam Gelir</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stores Table -->
    <div class="table-card glass-card">
        <div class="table-header">
            <div class="table-filters">
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="all">
                        <i class="bi bi-grid-3x3-gap me-1"></i>
                        Tümü
                    </button>
                    <button class="filter-btn" data-filter="approved">
                        <i class="bi bi-check-circle me-1"></i>
                        Aktif
                    </button>
                    <button class="filter-btn" data-filter="pending">
                        <i class="bi bi-clock me-1"></i>
                        Beklemede
                    </button>
                    <button class="filter-btn" data-filter="rejected">
                        <i class="bi bi-x-circle me-1"></i>
                        Pasif
                    </button>
                </div>
                
                <div class="table-actions">
                    <div class="search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" class="table-search" placeholder="Mağaza ara..." id="storeSearch">
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn-icon" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" onclick="exportToExcel()">
                                <i class="bi bi-download me-2"></i>Excel İndir
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="printTable()">
                                <i class="bi bi-printer me-2"></i>Yazdır
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="bi bi-gear me-2"></i>Ayarlar
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stores Table -->
        <div class="table-responsive">
            <table class="table stores-table" id="storesTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Mağaza</th>
                        <th>Ürün Sayısı</th>
                        <th>Satış</th>
                        <th>Gelir</th>
                        <th>Komisyon</th>
                        <th>Durum</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stores as $store)
                    <tr data-status="{{ $store->status ?? 'approved' }}">
                        <td>
                            <input type="checkbox" class="form-check-input store-select" value="{{ $store->id }}">
                        </td>
                        <td>
                            <div class="store-cell">
                                <div class="store-avatar {{ $store->status ?? 'approved' }}">
                                    @if($store->avatar ?? false)
                                        <img src="{{ asset('storage/' . $store->avatar) }}" alt="{{ $store->name }}">
                                    @else
                                        {{ strtoupper(substr($store->name ?? 'M', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="store-info">
                                    <h6>{{ $store->name ?? $store->store_name ?? 'İsimsiz Mağaza' }}</h6>
                                    <span class="text-muted">{{ $store->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="product-count">
                                <span class="count-number">{{ $store->products_count ?? 0 }}</span>
                                <span class="count-label">ürün</span>
                            </div>
                        </td>
                        <td>
                            <div class="sales-count">
                                <span class="count-number">{{ $store->total_sales ?? 0 }}</span>
                                <span class="count-label">satış</span>
                            </div>
                        </td>
                        <td>
                            <div class="revenue-amount">
                                <strong>₺{{ number_format($store->total_revenue ?? 0, 2) }}</strong>
                            </div>
                        </td>
                        <td>
                            <div class="commission-rate">
                                <span class="rate-badge">%{{ $store->commission_rate ?? 10 }}</span>
                            </div>
                        </td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" 
                                       class="status-toggle" 
                                       data-id="{{ $store->id }}"
                                       {{ ($store->status ?? 'approved') == 'approved' ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </td>
                        <td>{{ $store->created_at->format('d.m.Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.stores.show', $store->id) }}" 
                                   class="btn-action" 
                                   title="Görüntüle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn-action" 
                                        onclick="editStore({{ $store->id }})" 
                                        title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-action text-danger" 
                                        onclick="deleteStore({{ $store->id }})" 
                                        title="Sil">
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

<!-- Add Store Modal -->
<div class="modal fade" id="addStoreModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Mağaza Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.stores.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Mağaza Adı</label>
                            <input type="text" class="form-control" name="store_name" required>
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
                            <label class="form-label">Komisyon Oranı (%)</label>
                            <input type="number" class="form-control" name="commission_rate" value="10" min="0" max="100">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Açıklama</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Mağaza Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Stores Page Styles */
.stores-container {
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

/* Store Stat Cards */
.store-stat-card {
    padding: var(--spacing-lg);
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    height: 100%;
    transition: all 0.3s ease;
}

.store-stat-card:hover {
    transform: translateY(-4px);
}

.store-stat-card .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--white);
}

.store-stat-card.total .stat-icon {
    background: linear-gradient(135deg, var(--gray-600), var(--gray-700));
}

.store-stat-card.active .stat-icon {
    background: linear-gradient(135deg, #10B981, #059669);
}

.store-stat-card.products .stat-icon {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
}

.store-stat-card.revenue .stat-icon {
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
}

.store-stat-card .stat-content h3 {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-xs);
}

.store-stat-card .stat-content p {
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

/* Stores Table */
.stores-table {
    width: 100%;
}

.stores-table th {
    padding: var(--spacing-md);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.stores-table td {
    padding: var(--spacing-md);
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.stores-table tbody tr:hover {
    background: rgba(0, 0, 0, 0.01);
}

/* Store Cell */
.store-cell {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.store-avatar {
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

.store-avatar.approved {
    background: linear-gradient(135deg, #10B981, #059669);
}

.store-avatar.pending {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.store-avatar.rejected {
    background: linear-gradient(135deg, var(--gray-600), var(--gray-700));
}

.store-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.store-info h6 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    color: var(--gray-900);
}

.store-info span {
    font-size: 12px;
    color: var(--gray-500);
}

/* Count Styles */
.product-count,
.sales-count {
    display: flex;
    align-items: baseline;
    gap: var(--spacing-xs);
}

.count-number {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

.count-label {
    font-size: 12px;
    color: var(--gray-500);
}

/* Revenue Amount */
.revenue-amount strong {
    font-size: 16px;
    color: var(--primary-red);
}

/* Commission Rate */
.rate-badge {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-sm);
    background: rgba(0, 81, 187, 0.1);
    color: var(--primary-blue);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
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
    text-decoration: none;
}

.btn-action:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
    transform: scale(1.05);
    color: var(--gray-700);
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
    $('#storesTable').DataTable({
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
    $('#storeSearch').on('keyup', function() {
        $('#storesTable').DataTable().search(this.value).draw();
    });
});

// Filter Buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        const rows = document.querySelectorAll('#storesTable tbody tr');
        
        rows.forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
            } else {
                if (row.dataset.status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        // Redraw DataTable
        $('#storesTable').DataTable().draw();
    });
});

// Select All Checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.store-select');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Status Toggle
document.querySelectorAll('.status-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const storeId = this.dataset.id;
        const status = this.checked ? 'approved' : 'rejected';
        
        fetch(`/admin/stores/${storeId}/toggle-status`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (response.ok) {
                showNotification('Mağaza durumu güncellendi', 'success');
            }
        });
    });
});

// Edit Store
function editStore(id) {
    window.location.href = `/admin/stores/${id}/edit`;
}

// Delete Store
function deleteStore(id) {
    if (confirm('Bu mağazayı silmek istediğinizden emin misiniz?')) {
        fetch(`/admin/stores/${id}`, {
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

// Export to Excel
function exportToExcel() {
    window.location.href = '/admin/stores/export';
}

// Print Table
function printTable() {
    window.print();
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