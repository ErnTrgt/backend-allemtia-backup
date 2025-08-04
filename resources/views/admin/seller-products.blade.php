@extends('layouts.admin-modern')

@section('title', 'Mağaza Ürünleri')
@section('header-title', 'Mağaza Ürünleri')

@push('styles')
<style>
/* Seller Products Page Styles */
.seller-info-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
}

/* Page Actions */
.page-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}

.page-actions-left {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 16px;
}

.page-actions-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

/* Search Wrapper */
.search-wrapper {
    position: relative;
    width: 100%;
    max-width: 400px;
}

.search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-500);
    font-size: 16px;
}

.search-input {
    width: 100%;
    padding: 10px 16px 10px 42px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 2px solid rgba(0, 0, 0, 0.08);
    border-radius: 12px;
    font-size: 14px;
    color: var(--gray-700);
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-red);
    box-shadow: 0 0 0 3px rgba(169, 0, 0, 0.1);
}

.search-input::placeholder {
    color: var(--gray-400);
}

/* Secondary Button */
.btn-secondary {
    padding: 10px 20px;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%);
    color: var(--gray-700);
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.08) 0%, rgba(0, 0, 0, 0.1) 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    color: var(--gray-800);
}

.seller-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.seller-logo {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.seller-logo-placeholder {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, rgba(169, 0, 0, 0.1) 0%, rgba(193, 18, 31, 0.1) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: var(--primary-red);
}

.seller-details h3 {
    font-size: 24px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 8px;
}

.seller-meta {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: var(--gray-600);
}

.meta-item i {
    color: var(--primary-red);
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.stat-label {
    font-size: 13px;
    color: var(--gray-600);
}

/* Products Table */
.products-table-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-header {
    padding: 24px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.table-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-800);
    margin: 0;
}

.products-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.products-table th {
    padding: 16px;
    font-weight: 600;
    color: var(--gray-700);
    text-align: left;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    background: rgba(0, 0, 0, 0.02);
    font-size: 14px;
}

.products-table td {
    padding: 16px;
    color: var(--gray-700);
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
}

.products-table tbody tr {
    transition: all 0.2s ease;
}

.products-table tbody tr:hover {
    background: rgba(0, 0, 0, 0.02);
}

.product-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-image {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    background: var(--gray-100);
}

.product-name {
    font-weight: 500;
    color: var(--gray-900);
    margin-bottom: 2px;
}

.product-category {
    font-size: 12px;
    color: var(--gray-500);
}

.stock-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 20px;
}

.stock-badge.in-stock {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.stock-badge.low-stock {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.stock-badge.out-of-stock {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 20px;
}

.status-badge.active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-badge.inactive {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.action-btn:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.action-btn.view:hover {
    color: var(--info);
    border-color: var(--info);
}

.action-btn.edit:hover {
    color: var(--primary-blue);
    border-color: var(--primary-blue);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 16px;
    display: block;
}

.empty-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
}

.empty-text {
    color: var(--gray-500);
    font-size: 14px;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .page-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .page-actions-left {
        width: 100%;
    }
    
    .search-wrapper {
        max-width: none;
    }
    
    .page-actions-right {
        width: 100%;
        justify-content: flex-end;
    }
}
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Mağaza Ürünleri</h1>
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
        <span class="breadcrumb-separator">/</span>
        <a href="{{ route('admin.stores') }}">Mağazalar</a>
        <span class="breadcrumb-separator">/</span>
        <span>{{ $seller->store_name ?? $seller->name }}</span>
    </div>
</div>

<!-- Seller Info Card -->
<div class="seller-info-card">
    <div class="seller-header">
        @if(isset($seller->logo) && $seller->logo)
            <img src="{{ asset('storage/' . $seller->logo) }}" alt="{{ $seller->store_name ?? $seller->name }}" class="seller-logo">
        @else
            <div class="seller-logo-placeholder">
                <i class="bi bi-shop"></i>
            </div>
        @endif
        
        <div class="seller-details">
            <h3>{{ $seller->store_name ?? $seller->name }}</h3>
            <div class="seller-meta">
                <div class="meta-item">
                    <i class="bi bi-person"></i>
                    <span>{{ $seller->name }}</span>
                </div>
                <div class="meta-item">
                    <i class="bi bi-envelope"></i>
                    <span>{{ $seller->email }}</span>
                </div>
                @if($seller->phone)
                <div class="meta-item">
                    <i class="bi bi-telephone"></i>
                    <span>{{ $seller->phone }}</span>
                </div>
                @endif
                <div class="meta-item">
                    <i class="bi bi-calendar"></i>
                    <span>Katıldı: {{ $seller->created_at->format('d.m.Y') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $seller->products->count() }}</div>
            <div class="stat-label">Toplam Ürün</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $seller->products->where('status', 1)->count() }}</div>
            <div class="stat-label">Aktif Ürün</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $seller->products->sum('stock') }}</div>
            <div class="stat-label">Toplam Stok</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">₺{{ number_format($seller->products->avg('price'), 2, ',', '.') }}</div>
            <div class="stat-label">Ortalama Fiyat</div>
        </div>
    </div>
</div>

<!-- Page Actions -->
<div class="page-actions">
    <div class="page-actions-left">
        <!-- Search -->
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Ürün ara..." id="productSearch">
        </div>
    </div>
    
    <div class="page-actions-right">
        <a href="{{ route('admin.stores') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Mağazalara Dön
        </a>
    </div>
</div>

<!-- Products Table -->
<div class="products-table-card">
    <div class="table-header">
        <h3 class="table-title">Ürün Listesi</h3>
    </div>
    
    <div class="table-wrapper">
        <table class="products-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th>Fiyat</th>
                    <th>Stok</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($seller->products as $product)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="product-info">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                                @else
                                    <img src="{{ asset('images/default-product.svg') }}" alt="{{ $product->name }}" class="product-image">
                                @endif
                                <div>
                                    <div class="product-name">{{ $product->name }}</div>
                                    <div class="product-category">{{ $product->category->name ?? 'Kategori Yok' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>
                            <strong>₺{{ number_format($product->price, 2, ',', '.') }}</strong>
                            @if($product->discount_price)
                                <br><small class="text-muted"><del>₺{{ number_format($product->discount_price, 2, ',', '.') }}</del></small>
                            @endif
                        </td>
                        <td>
                            @if($product->stock > 10)
                                <span class="stock-badge in-stock">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ $product->stock }} adet
                                </span>
                            @elseif($product->stock > 0)
                                <span class="stock-badge low-stock">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    {{ $product->stock }} adet
                                </span>
                            @else
                                <span class="stock-badge out-of-stock">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Stok Yok
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($product->status == 1)
                                <span class="status-badge active">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="status-badge inactive">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Pasif
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn view" title="Görüntüle" onclick="viewProduct({{ $product->id }})">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="{{ route('admin.products') }}?product_id={{ $product->id }}" class="action-btn edit" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-box-seam empty-icon"></i>
                                <h3 class="empty-title">Ürün Bulunamadı</h3>
                                <p class="empty-text">Bu mağazaya henüz ürün eklenmemiş.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search functionality
let searchTimer;
document.getElementById('productSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const rows = document.querySelectorAll('.products-table tbody tr');
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            const productName = row.querySelector('.product-name')?.textContent.toLowerCase() || '';
            const category = row.querySelector('.product-category')?.textContent.toLowerCase() || '';
            
            if (productName.includes(query) || category.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }, 300);
});

// View product
function viewProduct(productId) {
    // Redirect to products page with the product modal open
    window.location.href = `{{ route('admin.products') }}?view_product=${productId}`;
}
</script>
@endpush
