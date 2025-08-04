@extends('layouts.admin-modern')

@section('title', 'Ürünler')
@section('header-title', 'Ürün Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/products.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Ürün Yönetimi</h1>
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
        <span class="breadcrumb-separator">/</span>
        <span>Ürünler</span>
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
        
        <!-- Filter -->
        <div class="filter-dropdown">
            <button class="filter-btn" data-dropdown-toggle="filterDropdown">
                <i class="bi bi-filter"></i>
                <span>Filtrele</span>
                <i class="bi bi-chevron-down"></i>
            </button>
            <div class="dropdown-menu" id="filterDropdown">
                <a href="{{ route('admin.products') }}" class="dropdown-item {{ !request('seller_id') ? 'active' : '' }}">
                    <i class="bi bi-shop"></i> Tüm Satıcılar
                </a>
                @foreach ($sellers as $seller)
                    <a href="{{ route('admin.products', ['seller_id' => $seller->id]) }}" 
                       class="dropdown-item {{ request('seller_id') == $seller->id ? 'active' : '' }}">
                        <i class="bi bi-person"></i> {{ $seller->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="page-actions-right">
        <!-- View Toggle -->
        <div class="view-toggle">
            <button class="view-toggle-btn active" data-view="grid">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button class="view-toggle-btn" data-view="list">
                <i class="bi bi-list-ul"></i>
            </button>
        </div>
        
        <!-- Add New Product -->
        <button class="btn btn-primary" onclick="AdminPanel.showToast('Yeni ürün ekleme özelliği yakında!', 'info')">
            <i class="bi bi-plus-circle"></i>
            Yeni Ürün
        </button>
    </div>
</div>

<!-- Bulk Actions (Hidden by default) -->
<div class="bulk-actions" id="bulkActions">
    <div class="bulk-select-info">
        <span id="selectedCount">0</span> ürün seçildi
    </div>
    <button class="btn btn-sm btn-outline-danger" onclick="bulkDelete()">
        <i class="bi bi-trash"></i>
        Seçilenleri Sil
    </button>
    <button class="btn btn-sm btn-outline-success" onclick="bulkActivate()">
        <i class="bi bi-check-circle"></i>
        Seçilenleri Aktifleştir
    </button>
    <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
        <i class="bi bi-x"></i>
        Seçimi Temizle
    </button>
</div>

<!-- Products Grid/List -->
<div class="products-container grid-view" id="productsContainer">
    @forelse($products as $product)
    <div class="product-card" data-product-id="{{ $product->id }}">
        <!-- Checkbox for bulk selection -->
        <div class="checkbox-wrapper position-absolute" style="top: 10px; left: 10px; z-index: 10;">
            <input type="checkbox" class="checkbox product-checkbox" value="{{ $product->id }}">
        </div>
        
        <!-- Grid View Content -->
        <div class="product-image-wrapper">
            @if ($product->images->isNotEmpty())
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                     alt="{{ $product->name }}" class="product-image">
            @else
                <img src="{{ asset('admin/src/images/product-placeholder.png') }}" 
                     alt="{{ $product->name }}" class="product-image">
            @endif
            
            <!-- Badges -->
            <div class="product-badges">
                @if($product->created_at->diffInDays() < 7)
                    <span class="badge badge-new">Yeni</span>
                @endif
                @if($product->discount > 0)
                    <span class="badge badge-sale">%{{ $product->discount }} İndirim</span>
                @endif
                @if($product->stock == 0)
                    <span class="badge badge-out-of-stock">Stokta Yok</span>
                @endif
            </div>
            
            <!-- Quick Actions -->
            <div class="product-actions-overlay">
                <button class="action-btn view" data-tooltip="Görüntüle" onclick="viewProduct({{ $product->id }})">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="action-btn edit" data-tooltip="Düzenle" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="action-btn delete" data-tooltip="Sil" onclick="deleteProduct({{ $product->id }})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        
        <div class="product-content">
            <h3 class="product-name">{{ $product->name }}</h3>
            
            <div class="product-price">
                @if($product->discount > 0)
                    <span class="current-price">₺{{ number_format($product->price * (1 - $product->discount/100), 2, ',', '.') }}</span>
                    <span class="original-price">₺{{ number_format($product->price, 2, ',', '.') }}</span>
                @else
                    <span class="current-price">₺{{ number_format($product->price, 2, ',', '.') }}</span>
                @endif
            </div>
            
            <div class="product-meta">
                <span class="stock-indicator {{ $product->stock > 10 ? 'stock-in' : ($product->stock > 0 ? 'stock-low' : 'stock-out') }}">
                    <span class="stock-dot"></span>
                    {{ $product->stock > 0 ? $product->stock . ' adet' : 'Stokta yok' }}
                </span>
                @if($product->status)
                    <span class="badge badge-success">Aktif</span>
                @else
                    <span class="badge badge-danger">Pasif</span>
                @endif
            </div>
            
            <!-- List View Extra Info -->
            <div class="list-view-info" style="display: none;">
                <p class="product-category">
                    <i class="bi bi-tag"></i> 
                    {{ $product->category->name ?? 'Kategori belirtilmemiş' }}
                </p>
                <div class="product-seller">
                    <i class="bi bi-shop"></i>
                    <a href="{{ route('admin.seller.products', $product->user->id) }}">
                        {{ $product->user->name }}
                    </a>
                </div>
                <div class="rating">
                    <div class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= ($product->rating ?? 0) ? '-fill filled' : ' empty' }}"></i>
                        @endfor
                    </div>
                    <span class="rating-count">({{ $product->review_count ?? 0 }})</span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="bi bi-box-seam empty-icon"></i>
        <h3 class="empty-title">Ürün Bulunamadı</h3>
        <p class="empty-text">Arama kriterlerinize uygun ürün bulunamadı.</p>
        <button class="btn btn-primary" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise"></i>
            Sayfayı Yenile
        </button>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($products->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $products->links('components.admin-pagination') }}
</div>
@endif

<!-- Edit Product Modals -->
@foreach ($products as $product)
<div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Ürün Düzenle: {{ $product->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Basic Info -->
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Temel Bilgiler</h6>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Ürün Adı</label>
                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stok Miktarı</label>
                            <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                        </div>
                        
                        <!-- Price Info -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted mb-3">Fiyat Bilgileri</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fiyat (₺)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ $product->price }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">İndirim (%)</label>
                            <input type="number" name="discount" class="form-control" value="{{ $product->discount ?? 0 }}" min="0" max="100">
                        </div>
                        
                        <!-- Description -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted mb-3">Açıklama</h6>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Ürün Açıklaması</label>
                            <textarea name="description" class="form-control" rows="4">{{ $product->description }}</textarea>
                        </div>
                        
                        <!-- Status -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted mb-3">Durum</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ürün Durumu</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ $product->status ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !$product->status ? 'selected' : '' }}>Pasif</option>
                            </select>
                        </div>
                        
                        <!-- Images -->
                        <div class="col-12 mt-4">
                            <h6 class="text-muted mb-3">Ürün Görselleri</h6>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Yeni Görseller Ekle</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted">Birden fazla görsel seçebilirsiniz</small>
                        </div>
                        
                        @if ($product->images->isNotEmpty())
                        <div class="col-12 mt-3">
                            <label class="form-label">Mevcut Görseller</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($product->images as $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                         alt="Ürün Görseli" class="rounded" 
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                @endforeach
                            </div>
                        </div>
                        @endif
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
// View Toggle
document.querySelectorAll('.view-toggle-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const view = this.dataset.view;
        const container = document.getElementById('productsContainer');
        
        // Update button states
        document.querySelectorAll('.view-toggle-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Update container class
        container.classList.remove('grid-view', 'list-view');
        container.classList.add(view + '-view');
        
        // Toggle list view info
        document.querySelectorAll('.list-view-info').forEach(info => {
            info.style.display = view === 'list' ? 'block' : 'none';
        });
    });
});

// Search functionality
let searchTimer;
document.getElementById('productSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const products = document.querySelectorAll('.product-card');
        products.forEach(product => {
            const name = product.querySelector('.product-name').textContent.toLowerCase();
            const seller = product.querySelector('.product-seller a')?.textContent.toLowerCase() || '';
            
            if (name.includes(query) || seller.includes(query)) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });
    }, 300);
});

// Checkbox functionality
const productCheckboxes = document.querySelectorAll('.product-checkbox');
const bulkActions = document.getElementById('bulkActions');
const selectedCount = document.getElementById('selectedCount');

productCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
    selectedCount.textContent = checkedCount;
    
    if (checkedCount > 0) {
        bulkActions.classList.add('show');
    } else {
        bulkActions.classList.remove('show');
    }
}

function clearSelection() {
    productCheckboxes.forEach(cb => cb.checked = false);
    updateBulkActions();
}

// Product Actions
function viewProduct(id) {
    window.location.href = `/admin/product/${id}`;
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
                AdminPanel.showToast('Ürün başarıyla silindi!', 'success');
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
    const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedIds.length === 0) return;
    
    if (confirm(`${selectedIds.length} ürünü silmek istediğinizden emin misiniz?`)) {
        fetch('/admin/products/bulk-delete', {
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
                AdminPanel.showToast('Seçili ürünler silindi!', 'success');
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

function bulkActivate() {
    const selectedIds = Array.from(document.querySelectorAll('.product-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedIds.length === 0) return;
    
    fetch('/admin/products/bulk-activate', {
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
            AdminPanel.showToast('Seçili ürünler aktifleştirildi!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            AdminPanel.showToast('Bir hata oluştu!', 'error');
        }
    })
    .catch(error => {
        AdminPanel.showToast('Bir hata oluştu!', 'error');
    });
}

// Initialize tooltips
document.querySelectorAll('[data-tooltip]').forEach(el => {
    el.title = el.dataset.tooltip;
});
</script>
@endpush