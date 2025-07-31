@extends('layouts.admin-modern')

@section('title', 'Ürünler')

@section('content')
<div class="products-container">
    <!-- Page Header Component -->
    <x-admin.page-header 
        title="Ürünler"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Ürünler']
        ]">
        <x-slot name="actions">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-circle me-2"></i>
                Yeni Ürün
            </button>
        </x-slot>
    </x-admin.page-header>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card total">
                <div class="stat-icon">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['total']) }}</h3>
                    <p>Toplam Ürün</p>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card active">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['active']) }}</h3>
                    <p>Aktif Ürün</p>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card pending">
                <div class="stat-icon">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['pending']) }}</h3>
                    <p>Onay Bekleyen</p>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card out-of-stock">
                <div class="stat-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['out_of_stock']) }}</h3>
                    <p>Stokta Yok</p>
                </div>
            </x-admin.glass-card>
        </div>
    </div>
    
    <!-- Products Table/Grid -->
    <x-admin.glass-card class="table-card" :padding="false">
        <!-- Table Header Component -->
        <x-admin.table-header search-placeholder="Ürün ara..." search-id="productSearch">
            <x-slot name="filters">
                <button class="filter-btn active" data-filter="all">
                    <i class="bi bi-grid-3x3-gap me-1"></i>
                    Tümü
                </button>
                <button class="filter-btn" data-filter="active">
                    <i class="bi bi-check-circle me-1"></i>
                    Aktif
                </button>
                <button class="filter-btn" data-filter="pending">
                    <i class="bi bi-clock me-1"></i>
                    Beklemede
                </button>
                <button class="filter-btn" data-filter="out-of-stock">
                    <i class="bi bi-x-circle me-1"></i>
                    Stokta Yok
                </button>
            </x-slot>
            
            <x-slot name="actions">
                <div class="view-toggle">
                    <button class="view-btn active" data-view="grid" title="Grid Görünüm">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </button>
                    <button class="view-btn" data-view="list" title="Liste Görünüm">
                        <i class="bi bi-list-ul"></i>
                    </button>
                </div>
            </x-slot>
        </x-admin.table-header>
        
        <!-- Grid View -->
        <div id="gridView" class="products-grid p-4">
            <div class="row g-4">
                @foreach($products as $product)
                <div class="col-md-6 col-lg-4 col-xl-3 product-item" data-status="{{ $product->status }}">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{ $product->image ?? '/images/default-product.jpg' }}" alt="{{ $product->name }}">
                            <div class="product-badges">
                                @if($product->is_featured)
                                <span class="badge-featured">Öne Çıkan</span>
                                @endif
                                @if($product->discount_percentage > 0)
                                <span class="badge-discount">-{{ $product->discount_percentage }}%</span>
                                @endif
                            </div>
                            <div class="product-overlay">
                                <button class="btn-overlay" onclick="viewProduct({{ $product->id }})" title="Görüntüle">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn-overlay" onclick="editProduct({{ $product->id }})" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-overlay" onclick="deleteProduct({{ $product->id }})" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="product-info">
                            <div class="product-category">{{ $product->category->name ?? 'Kategori Yok' }}</div>
                            <h5 class="product-name">{{ $product->name }}</h5>
                            <div class="product-price">
                                @if($product->discount_price)
                                <span class="price-old">₺{{ number_format($product->price, 2) }}</span>
                                <span class="price-current">₺{{ number_format($product->discount_price, 2) }}</span>
                                @else
                                <span class="price-current">₺{{ number_format($product->price, 2) }}</span>
                                @endif
                            </div>
                            <div class="product-meta">
                                <div class="meta-item">
                                    <i class="bi bi-box-seam"></i>
                                    <span>Stok: {{ $product->stock }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="bi bi-shop"></i>
                                    <span>{{ $product->store->name ?? 'Mağaza Yok' }}</span>
                                </div>
                            </div>
                            <div class="product-status">
                                <span class="status-badge {{ $product->status }}">
                                    @if($product->status == 'active')
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    @elseif($product->status == 'pending')
                                        <i class="bi bi-clock me-1"></i>Onay Bekliyor
                                    @else
                                        <i class="bi bi-x-circle me-1"></i>Pasif
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- List View -->
        <div id="listView" class="table-responsive" style="display: none;">
            <table class="table products-table" id="productsTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Ürün</th>
                        <th>Kategori</th>
                        <th>Fiyat</th>
                        <th>Stok</th>
                        <th>Mağaza</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr data-status="{{ $product->status }}">
                        <td>
                            <input type="checkbox" class="form-check-input product-select" value="{{ $product->id }}">
                        </td>
                        <td>
                            <div class="product-cell">
                                <img src="{{ $product->image ?? '/images/default-product.jpg' }}" 
                                     alt="{{ $product->name }}" 
                                     class="product-thumb">
                                <div class="product-details">
                                    <h6>{{ $product->name }}</h6>
                                    <span class="text-muted">SKU: {{ $product->sku }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>
                            @if($product->discount_price)
                                <div class="price-group">
                                    <span class="price-old">₺{{ number_format($product->price, 2) }}</span>
                                    <span class="price-current">₺{{ number_format($product->discount_price, 2) }}</span>
                                </div>
                            @else
                                <span class="price-current">₺{{ number_format($product->price, 2) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="stock-badge {{ $product->stock > 10 ? 'in-stock' : ($product->stock > 0 ? 'low-stock' : 'out-of-stock') }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td>{{ $product->store->name ?? '-' }}</td>
                        <td>
                            <span class="status-badge {{ $product->status }}">
                                @if($product->status == 'active')
                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                @elseif($product->status == 'pending')
                                    <i class="bi bi-clock me-1"></i>Beklemede
                                @else
                                    <i class="bi bi-x-circle me-1"></i>Pasif
                                @endif
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action" onclick="viewProduct({{ $product->id }})" title="Görüntüle">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn-action" onclick="editProduct({{ $product->id }})" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-action text-danger" onclick="deleteProduct({{ $product->id }})" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-admin.glass-card>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Ürün Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Ürün Adı</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Kategori Seçin</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mağaza</label>
                            <select class="form-select" name="store_id" required>
                                <option value="">Mağaza Seçin</option>
                                @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fiyat</label>
                            <input type="number" class="form-control" name="price" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">İndirimli Fiyat</label>
                            <input type="number" class="form-control" name="discount_price" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stok</label>
                            <input type="number" class="form-control" name="stock" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Açıklama</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ürün Görseli</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Ürün Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Products Page Styles */
.products-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Stat Cards */
.stat-card {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    padding: var(--spacing-lg);
    height: 100%;
}

.stat-card .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--white);
}

.stat-card.total .stat-icon {
    background: linear-gradient(135deg, var(--gray-600), var(--gray-700));
}

.stat-card.active .stat-icon {
    background: linear-gradient(135deg, #10B981, #059669);
}

.stat-card.pending .stat-icon {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.stat-card.out-of-stock .stat-icon {
    background: linear-gradient(135deg, #EF4444, #DC2626);
}

.stat-card .stat-content h3 {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-xs);
}

.stat-card .stat-content p {
    font-size: 14px;
    color: var(--gray-600);
    margin: 0;
}

/* Filter Buttons */
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

/* View Toggle */
.view-toggle {
    display: flex;
    background: var(--gray-100);
    border-radius: var(--radius-sm);
    padding: 2px;
}

.view-btn {
    padding: var(--spacing-xs) var(--spacing-sm);
    background: transparent;
    border: none;
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: calc(var(--radius-sm) - 2px);
}

.view-btn:hover {
    color: var(--gray-800);
}

.view-btn.active {
    background: var(--white);
    color: var(--primary-red);
    box-shadow: var(--shadow-sm);
}

/* Products Grid */
.products-grid {
    min-height: 400px;
}

.product-card {
    background: var(--white);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid var(--gray-200);
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.product-image {
    position: relative;
    padding-top: 100%;
    overflow: hidden;
    background: var(--gray-100);
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-badges {
    position: absolute;
    top: var(--spacing-sm);
    left: var(--spacing-sm);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.badge-featured,
.badge-discount {
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-featured {
    background: var(--primary-red);
    color: var(--white);
}

.badge-discount {
    background: var(--secondary-red);
    color: var(--white);
}

.product-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    padding: var(--spacing-md);
    display: flex;
    justify-content: center;
    gap: var(--spacing-sm);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.btn-overlay {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    color: var(--gray-700);
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-overlay:hover {
    background: var(--white);
    transform: scale(1.1);
    color: var(--primary-red);
}

.product-info {
    padding: var(--spacing-md);
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-category {
    font-size: 12px;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: var(--spacing-xs);
}

.product-name {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: var(--spacing-sm);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-price {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.price-old {
    font-size: 14px;
    color: var(--gray-500);
    text-decoration: line-through;
}

.price-current {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary-red);
}

.product-meta {
    display: flex;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-md);
    font-size: 13px;
    color: var(--gray-600);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.product-status {
    margin-top: auto;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 500;
}

.status-badge.active {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

.status-badge.pending {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
}

.status-badge.inactive {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
}

/* Products Table */
.products-table {
    width: 100%;
}

.products-table th {
    padding: var(--spacing-md);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.products-table td {
    padding: var(--spacing-md);
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.product-cell {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.product-thumb {
    width: 50px;
    height: 50px;
    border-radius: var(--radius-sm);
    object-fit: cover;
    background: var(--gray-100);
}

.product-details h6 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    color: var(--gray-900);
}

.price-group {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.stock-badge {
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 500;
}

.stock-badge.in-stock {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

.stock-badge.low-stock {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
}

.stock-badge.out-of-stock {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
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

/* Responsive */
@media (max-width: 1200px) {
    .product-card {
        font-size: 14px;
    }
    
    .product-name {
        font-size: 15px;
    }
    
    .price-current {
        font-size: 16px;
    }
}

@media (max-width: 768px) {
    .filter-group {
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: var(--spacing-sm);
    }
    
    .product-meta {
        font-size: 12px;
    }
}
</style>
@endsection

@push('scripts')
<script>
// View Toggle
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const view = this.dataset.view;
        
        if (view === 'grid') {
            document.getElementById('gridView').style.display = 'block';
            document.getElementById('listView').style.display = 'none';
        } else {
            document.getElementById('gridView').style.display = 'none';
            document.getElementById('listView').style.display = 'block';
            
            // Initialize DataTable if not already initialized
            if (!$.fn.DataTable.isDataTable('#productsTable')) {
                $('#productsTable').DataTable({
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
            }
        }
    });
});

// Filter Buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        const items = document.querySelectorAll('.product-item, #productsTable tbody tr');
        
        items.forEach(item => {
            if (filter === 'all') {
                item.style.display = '';
            } else {
                const status = item.dataset.status;
                if (status === filter || (filter === 'out-of-stock' && item.querySelector('.stock-badge.out-of-stock'))) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            }
        });
        
        // Redraw DataTable if in list view
        if (document.getElementById('listView').style.display !== 'none') {
            $('#productsTable').DataTable().draw();
        }
    });
});

// Search functionality
document.getElementById('productSearch').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const items = document.querySelectorAll('.product-item');
    
    items.forEach(item => {
        const productName = item.querySelector('.product-name').textContent.toLowerCase();
        const category = item.querySelector('.product-category').textContent.toLowerCase();
        const store = item.querySelector('.meta-item:last-child span').textContent.toLowerCase();
        
        if (productName.includes(searchTerm) || category.includes(searchTerm) || store.includes(searchTerm)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
    
    // Also search in DataTable if in list view
    if (document.getElementById('listView').style.display !== 'none') {
        $('#productsTable').DataTable().search(searchTerm).draw();
    }
});

// Select All Checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-select');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Product Actions
function viewProduct(id) {
    window.location.href = `/admin/products/${id}`;
}

function editProduct(id) {
    window.location.href = `/admin/products/${id}/edit`;
}

function deleteProduct(id) {
    if (confirm('Bu ürünü silmek istediğinizden emin misiniz?')) {
        fetch(`/admin/products/${id}`, {
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
    window.location.href = '/admin/products/export';
}

// Print Table
function printTable() {
    window.print();
}
</script>
@endpush