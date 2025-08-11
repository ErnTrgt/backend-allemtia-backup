@extends('layouts.admin-modern')

@section('title', 'Ürünler')
@section('header-title', 'Ürünler')

@section('content')
<script>
// Temporary placeholder functions - will be overridden when ProductManager loads
window.viewProduct = function(productId) {
    console.log('View product clicked for ID:', productId);
    
    // Bootstrap modal'ı doğrudan aç
    const modalElement = document.getElementById('viewProductModal' + productId);
    
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('View modal opened for product:', productId);
    } else {
        console.error('View modal not found for product ID:', productId);
        alert('Ürün detay modalı bulunamadı. Lütfen sayfayı yenileyip tekrar deneyin.');
    }
};

window.editProduct = function(productId) {
    console.log('Edit product clicked for ID:', productId);
    
    // Bootstrap modal'ı doğrudan aç
    const modalElement = document.getElementById('editProductModal' + productId);
    
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('Modal opened for product:', productId);
    } else {
        console.error('Modal not found for product ID:', productId);
        alert('Ürün düzenleme modalı bulunamadı. Lütfen sayfayı yenileyip tekrar deneyin.');
    }
};

window.deleteProduct = function(productId) {
    console.log('Waiting for ProductManager to load...');
    setTimeout(() => {
        if (window.productManager) {
            window.productManager.deleteProduct(productId);
        } else {
            alert('Yükleniyor, lütfen tekrar deneyin...');
        }
    }, 500);
};
</script>

<div class="products-container">
    <!-- Page Header with Add Button -->
    <div class="page-header-wrapper mb-4">
        <div class="page-header-left">
            <h1 class="page-title">Ürünler</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Ürünler</li>
                </ol>
            </nav>
        </div>
        <div class="page-header-right">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-circle me-2"></i>
                Yeni Ürün Ekle
            </button>
        </div>
    </div>
    
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
                    <div class="product-card" data-product-id="{{ $product->id }}">
                        <div class="product-image">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/default-product.svg' }}" alt="{{ $product->name }}">
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
                                <span class="status-badge {{ $product->status == 1 || $product->status == 'active' ? 'active' : 'inactive' }}">
                                    @if($product->status == 1 || $product->status == 'active')
                                        <i class="bi bi-check-circle me-1"></i>Aktif
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
                    <tr data-product-id="{{ $product->id }}" data-status="{{ $product->status }}">
                        <td>
                            <input type="checkbox" class="form-check-input product-select" value="{{ $product->id }}">
                        </td>
                        <td>
                            <div class="product-cell">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/default-product.svg' }}" 
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
                            <span class="status-badge {{ $product->status == 1 || $product->status == 'active' ? 'active' : 'inactive' }}">
                                @if($product->status == 1 || $product->status == 'active')
                                    <i class="bi bi-check-circle me-1"></i>Aktif
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

<!-- View Product Modals -->
@foreach($products as $product)
<div class="modal fade" id="viewProductModal{{ $product->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-box-seam-fill me-2"></i>
                    Ürün Detayları
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <!-- Ürün Görselleri -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-images"></i>
                        Ürün Görselleri
                    </h6>
                    <div class="product-images-container">
                        @if($product->image || ($product->images && count($product->images) > 0))
                            @if($product->image)
                                <div class="main-image">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid">
                                </div>
                            @endif
                            
                            @if($product->images && count($product->images) > 0)
                                <div class="image-gallery">
                                    @foreach($product->images as $image)
                                        <div class="gallery-item">
                                            <img src="{{ asset('storage/' . ($image->image_path ?? $image)) }}" alt="{{ $product->name }}" class="img-fluid">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="no-image-placeholder">
                                <i class="bi bi-image"></i>
                                <p>Bu ürün için görsel bulunmuyor</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ürün Bilgileri -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-box-seam"></i>
                        Temel Bilgiler
                    </h6>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">Ürün Adı</label>
                                <div class="form-control-static">{{ $product->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">SKU</label>
                                <div class="form-control-static">{{ $product->sku ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Kategori</label>
                                <div class="form-control-static">{{ $product->category->name ?? 'Kategori Yok' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Mağaza</label>
                                <div class="form-control-static">{{ $product->store->name ?? 'Mağaza Yok' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fiyat ve Stok -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-currency-dollar"></i>
                        Fiyat ve Stok Bilgileri
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Normal Fiyat</label>
                                <div class="form-control-static">₺{{ number_format($product->price, 2, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">İndirimli Fiyat</label>
                                <div class="form-control-static">
                                    @if($product->discount_price)
                                        ₺{{ number_format($product->discount_price, 2, ',', '.') }}
                                        <span class="badge bg-danger ms-2">-{{ $product->discount_percentage }}%</span>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Stok Durumu</label>
                                <div class="form-control-static">
                                    <span class="stock-badge {{ $product->stock > 10 ? 'in-stock' : ($product->stock > 0 ? 'low-stock' : 'out-of-stock') }}">
                                        {{ $product->stock }} adet
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ek Bilgiler -->
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="bi bi-info-circle"></i>
                        Ek Bilgiler
                    </h6>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Açıklama</label>
                                <div class="form-control-static">{{ $product->description ?? 'Açıklama bulunmuyor' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Durum</label>
                                <div class="form-control-static">
                                    <span class="status-badge {{ $product->status == 1 || $product->status == 'active' ? 'active' : 'inactive' }}">
                                        @if($product->status == 1 || $product->status == 'active')
                                            <i class="bi bi-check-circle me-1"></i>Aktif
                                        @else
                                            <i class="bi bi-x-circle me-1"></i>Pasif
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Eklenme Tarihi</label>
                                <div class="form-control-static">{{ $product->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($product->is_featured)
                <div class="info-message">
                    <i class="bi bi-star-fill"></i>
                    <div class="info-message-content">
                        <div class="info-message-title">Öne Çıkan Ürün</div>
                        <div class="info-message-text">
                            Bu ürün öne çıkan ürünler arasında gösterilmektedir.
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <a href="{{ route('admin.product.details', $product->id) }}" class="btn btn-primary" style="background: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-red) 100%); border: none;">
                    <i class="bi bi-box-arrow-up-right me-2"></i>
                    Ürün Detayına Git
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Edit Product Modals -->
@foreach($products as $product)
<div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Ürün Düzenle: <span class="badge bg-light text-dark">{{ $product->name }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <form id="editProductForm{{ $product->id }}" action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data" onsubmit="handleProductUpdate(event, {{ $product->id }})">
                @csrf
                @method('PUT')
                <input type="hidden" name="deleted_images" id="deletedImages{{ $product->id }}" value="">
                <div class="modal-body">
                    <!-- Temel Bilgiler -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-box-seam"></i>
                            Temel Bilgiler
                        </h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Ürün Adı</label>
                                    <input type="text" class="form-control" name="name" value="{{ $product->name }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Kategori</label>
                                    <select class="form-control" name="category_id" required>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fiyat ve Stok Bilgileri -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-currency-dollar"></i>
                            Fiyat ve Stok Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Fiyat</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="price" 
                                               value="{{ $product->price }}" step="0.01" required>
                                        <span class="input-group-text">₺</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Stok Adedi</label>
                                    <input type="number" class="form-control" name="stock" 
                                           value="{{ $product->stock }}" required min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detay Bilgileri -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-card-text"></i>
                            Detay Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Ürün Açıklaması</label>
                                    <textarea class="form-control" name="description" rows="3">{{ $product->description }}</textarea>
                                    <small class="text-muted">İsteğe bağlı</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Mevcut Görseller</label>
                                    <div class="current-images-container">
                                        @if($product->image)
                                            <div class="current-image-item" data-image-type="main">
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="Ana görsel">
                                                <div class="image-overlay">
                                                    <span class="image-badge">Ana Görsel</span>
                                                    <button type="button" class="btn-remove-image" onclick="removeImage(this, '{{ $product->id }}', 'main')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($product->images && count($product->images) > 0)
                                            @foreach($product->images as $index => $image)
                                                <div class="current-image-item" data-image-id="{{ $image->id ?? $index }}">
                                                    <img src="{{ asset('storage/' . ($image->image_path ?? $image)) }}" alt="Ek görsel">
                                                    <div class="image-overlay">
                                                        <button type="button" class="btn-remove-image" onclick="removeImage(this, '{{ $product->id }}', '{{ $image->id ?? $index }}')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        
                                        @if(!$product->image && (!$product->images || count($product->images) == 0))
                                            <div class="no-images-text">
                                                <i class="bi bi-image"></i>
                                                <p>Henüz görsel eklenmemiş</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Yeni Ana Görsel</label>
                                    <input type="file" class="form-control" name="image" accept="image/*" id="mainImageInput{{ $product->id }}">
                                    <small class="text-muted">Ana ürün görseli (isteğe bağlı)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ek Görseller</label>
                                    <input type="file" class="form-control" name="images[]" accept="image/*" multiple id="additionalImagesInput{{ $product->id }}">
                                    <small class="text-muted">Birden fazla görsel seçebilirsiniz</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Durum</label>
                                    <select class="form-control" name="status" required>
                                        <option value="1" {{ $product->status == 1 || $product->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ $product->status == 0 || $product->status == 'inactive' ? 'selected' : '' }}>Pasif</option>
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
                                Ürün bilgileri güncellendikten sonra değişiklikler anında yayına alınacaktır.
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Yeni Ürün Ekle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <form id="addProductForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Temel Bilgiler -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-box-seam"></i>
                            Temel Bilgiler
                        </h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control" name="category_id" required>
                                        <option value="">Kategori Seçin</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Mağaza <span class="text-danger">*</span></label>
                                    <select class="form-control" name="store_id" required>
                                        <option value="">Mağaza Seçin</option>
                                        @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fiyat ve Stok Bilgileri -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-currency-dollar"></i>
                            Fiyat ve Stok Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Fiyat <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="price" step="0.01" required>
                                        <span class="input-group-text">₺</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Stok Adedi <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="stock" required min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detay Bilgileri -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-card-text"></i>
                            Detay Bilgileri
                        </h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Ürün Açıklaması</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                    <small class="text-muted">İsteğe bağlı</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ana Görsel</label>
                                    <input type="file" class="form-control" name="image" accept="image/*">
                                    <small class="text-muted">Ana ürün görseli (isteğe bağlı)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Ek Görseller</label>
                                    <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
                                    <small class="text-muted">Birden fazla görsel seçebilirsiniz</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
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

/* Input Group */
.input-group {
    display: flex;
    align-items: stretch;
    width: 100%;
}

.input-group-text {
    padding: var(--spacing-sm) var(--spacing-md);
    background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%);
    border: 2px solid rgba(0, 0, 0, 0.08);
    border-left: none;
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    color: var(--gray-600);
    font-size: var(--text-sm);
}

.input-group .form-control {
    border-radius: var(--radius-sm) 0 0 var(--radius-sm);
    border-right: none;
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

/* Product Images Container */
.product-images-container {
    margin-top: var(--spacing-md);
}

.main-image {
    border-radius: var(--radius-md);
    overflow: hidden;
    background: var(--gray-100);
    margin-bottom: var(--spacing-md);
    position: relative;
    max-height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.main-image img {
    width: 100%;
    height: auto;
    max-height: 400px;
    object-fit: contain;
}

.image-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: var(--spacing-sm);
}

.gallery-item {
    position: relative;
    padding-top: 100%;
    border-radius: var(--radius-sm);
    overflow: hidden;
    background: var(--gray-100);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    cursor: pointer;
}

.gallery-item:hover {
    border-color: var(--primary-red);
    transform: scale(1.05);
}

.gallery-item img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* No Image Placeholder */
.no-image-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: var(--spacing-3xl) var(--spacing-xl);
    background: var(--gray-50);
    border-radius: var(--radius-md);
    border: 2px dashed var(--gray-300);
    color: var(--gray-400);
    text-align: center;
}

.no-image-placeholder i {
    font-size: 48px;
    margin-bottom: var(--spacing-md);
}

.no-image-placeholder p {
    margin: 0;
    font-size: var(--text-sm);
}

/* Current Images Container */
.current-images-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: var(--spacing-md);
    margin-top: var(--spacing-sm);
}

.current-image-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: var(--radius-md);
    overflow: hidden;
    background: var(--gray-100);
    border: 2px solid var(--gray-200);
    transition: all 0.3s ease;
}

.current-image-item:hover {
    border-color: var(--primary-red);
    transform: scale(1.02);
}

.current-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.current-image-item .image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, transparent 40%, transparent 60%, rgba(0,0,0,0.7) 100%);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-end;
    padding: var(--spacing-sm);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.current-image-item:hover .image-overlay {
    opacity: 1;
}

.image-badge {
    background: var(--primary-red);
    color: white;
    padding: 4px 8px;
    border-radius: var(--radius-sm);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.btn-remove-image {
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-remove-image:hover {
    background: #DC2626;
    transform: scale(1.1);
}

.no-images-text {
    grid-column: 1 / -1;
    text-align: center;
    padding: var(--spacing-xl);
    color: var(--gray-400);
}

.no-images-text i {
    font-size: 36px;
    margin-bottom: var(--spacing-sm);
    display: block;
}

.no-images-text p {
    margin: 0;
    font-size: var(--text-sm);
}

/* Image Preview for File Input */
.image-preview-container {
    display: none;
    margin-top: var(--spacing-md);
    padding: var(--spacing-md);
    background: var(--gray-50);
    border-radius: var(--radius-md);
    border: 1px solid var(--gray-200);
}

.image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: var(--spacing-sm);
}

.preview-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: var(--radius-sm);
    overflow: hidden;
    background: var(--gray-200);
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Responsive Image Gallery */
@media (max-width: 768px) {
    .image-gallery {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    }
    
    .main-image {
        max-height: 300px;
    }
    
    .current-images-container {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }
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

/* Product Detail Button */
.modal-footer .btn-primary {
    padding: 10px 20px;
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(169, 0, 0, 0.2);
}

.modal-footer .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(169, 0, 0, 0.3);
}

.modal-footer .btn-primary i {
    font-size: 14px;
    transition: transform 0.3s ease;
}

.modal-footer .btn-primary:hover i {
    transform: translate(2px, -2px);
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

// Image Gallery Click Handler
document.addEventListener('DOMContentLoaded', function() {
    // Handle gallery item clicks
    document.querySelectorAll('.gallery-item').forEach(item => {
        item.addEventListener('click', function() {
            const clickedImg = this.querySelector('img');
            const mainImageContainer = this.closest('.product-images-container').querySelector('.main-image img');
            
            if (mainImageContainer && clickedImg) {
                // Swap images with animation
                mainImageContainer.style.opacity = '0';
                setTimeout(() => {
                    mainImageContainer.src = clickedImg.src;
                    mainImageContainer.style.opacity = '1';
                }, 300);
            }
        });
    });
    
    // Add transition to main images
    document.querySelectorAll('.main-image img').forEach(img => {
        img.style.transition = 'opacity 0.3s ease';
    });
    
    // Preview new images on file select
    document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                // Create preview container if needed
                let previewContainer = this.parentElement.querySelector('.image-preview-container');
                if (!previewContainer) {
                    previewContainer = document.createElement('div');
                    previewContainer.className = 'image-preview-container';
                    previewContainer.innerHTML = '<p class="text-muted mb-2">Yüklenecek görseller:</p><div class="image-preview-grid"></div>';
                    this.parentElement.appendChild(previewContainer);
                }
                
                const previewGrid = previewContainer.querySelector('.image-preview-grid');
                previewGrid.innerHTML = '';
                previewContainer.style.display = 'block';
                
                Array.from(files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewItem = document.createElement('div');
                            previewItem.className = 'preview-item';
                            previewItem.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                            previewGrid.appendChild(previewItem);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    });
});

// Remove Image Function
function removeImage(button, productId, imageType) {
    if (!confirm('Bu görseli silmek istediğinizden emin misiniz?')) {
        return;
    }
    
    const imageItem = button.closest('.current-image-item');
    
    // Add loading state
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    
    // Track deleted images
    const deletedImagesInput = document.getElementById(`deletedImages${productId}`);
    let deletedImages = deletedImagesInput.value ? deletedImagesInput.value.split(',') : [];
    deletedImages.push(imageType);
    deletedImagesInput.value = deletedImages.join(',');
    
    // Visual removal with animation
    imageItem.style.opacity = '0';
    imageItem.style.transform = 'scale(0.8)';
    
    setTimeout(() => {
        imageItem.remove();
        
        // Check if no images left
        const container = document.querySelector(`#editProductModal${productId} .current-images-container`);
        if (container && container.querySelectorAll('.current-image-item').length === 0) {
            container.innerHTML = `
                <div class="no-images-text">
                    <i class="bi bi-image"></i>
                    <p>Henüz görsel eklenmemiş</p>
                </div>
            `;
        }
    }, 300);
    
    // Note: In a real implementation, you would also make an AJAX request to delete the image immediately
    // This would provide better UX and ensure the image is deleted even if the form is not submitted
}

// Handle product update with AJAX
function handleProductUpdate(event, productId) {
    event.preventDefault();
    
    const form = document.getElementById(`editProductForm${productId}`);
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Güncelleniyor...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById(`editProductModal${productId}`));
            if (modal) {
                modal.hide();
            }
            
            // Update product in view (both grid and list)
            updateProductInView(productId, formData);
            
            // Show success toast
            showSuccessToast('Ürün başarıyla güncellendi!');
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        } else {
            alert(data.message || 'Bir hata oluştu!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ürün güncellenirken bir hata oluştu. Lütfen tekrar deneyin.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Update product in view without page reload
function updateProductInView(productId, formData) {
    // Get form values
    const name = formData.get('name');
    const price = formData.get('price');
    const stock = formData.get('stock');
    const status = formData.get('status');
    const categoryId = formData.get('category_id');
    
    console.log('Updating product:', productId, {name, price, stock, status});
    
    // Update in grid view
    const gridCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
    if (gridCard) {
        updateGridCard(gridCard, name, price, stock, status);
    } else {
        console.log('Grid card not found for product:', productId);
    }
    
    // Update in list view  
    const listRow = document.querySelector(`tr[data-product-id="${productId}"]`);
    if (listRow) {
        updateListRow(listRow, name, price, stock, status);
    } else {
        console.log('List row not found for product:', productId);
    }
}

// Update grid card
function updateGridCard(card, name, price, stock, status) {
    // Update name
    const nameEl = card.querySelector('.product-name');
    if (nameEl) nameEl.textContent = name;
    
    // Update price
    const priceEl = card.querySelector('.price-current');
    if (priceEl) priceEl.textContent = '₺' + parseFloat(price).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    
    // Update stock - Grid view'da stok bilgisi meta-item içinde
    const stockMetaItems = card.querySelectorAll('.meta-item');
    stockMetaItems.forEach(item => {
        if (item.innerHTML.includes('bi-box-seam')) {
            item.innerHTML = `<i class="bi bi-box-seam"></i> Stok: ${stock}`;
        }
    });
    
    // Update status badge - Grid view'da status badge meta-item içinde
    stockMetaItems.forEach(item => {
        const badge = item.querySelector('.badge');
        if (badge) {
            const isActive = status == '1' || status == 'active';
            badge.className = isActive ? 'badge bg-success' : 'badge bg-secondary';
            badge.textContent = isActive ? 'Aktif' : 'Pasif';
        }
    });
    
    // Update parent container status
    const parentItem = card.closest('.product-item');
    if (parentItem) {
        parentItem.setAttribute('data-status', status == '1' || status == 'active' ? 'active' : 'inactive');
    }
    
    // Add update animation
    card.style.animation = 'pulse 0.5s ease';
    setTimeout(() => {
        card.style.animation = '';
    }, 500);
}

// Update list row
function updateListRow(row, name, price, stock, status) {
    // Update name
    const nameEl = row.querySelector('.product-details h6');
    if (nameEl) nameEl.textContent = name;
    
    // Update price (4th column)
    const priceCell = row.querySelector('td:nth-child(4)');
    if (priceCell) {
        const formattedPrice = parseFloat(price).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        priceCell.innerHTML = `<span class="price-current">₺${formattedPrice}</span>`;
    }
    
    // Update stock (5th column)
    const stockEl = row.querySelector('td:nth-child(5) .stock-badge');
    if (stockEl) {
        stockEl.textContent = stock;
        // Update stock badge color
        if (stock == 0) {
            stockEl.className = 'stock-badge out-of-stock';
        } else if (stock < 10) {
            stockEl.className = 'stock-badge low-stock';
        } else {
            stockEl.className = 'stock-badge in-stock';
        }
    }
    
    // Update status (7th column)
    const statusCell = row.querySelector('td:nth-child(7) .status-badge');
    if (statusCell) {
        const isActive = status == '1' || status == 'active';
        statusCell.className = isActive ? 'status-badge active' : 'status-badge inactive';
        statusCell.innerHTML = isActive 
            ? '<i class="bi bi-check-circle me-1"></i>Aktif' 
            : '<i class="bi bi-x-circle me-1"></i>Pasif';
    }
    
    // Update row data-status attribute
    row.setAttribute('data-status', status == '1' || status == 'active' ? 'active' : 'inactive');
    
    // Add update animation
    row.style.background = 'rgba(16, 185, 129, 0.1)';
    setTimeout(() => {
        row.style.background = '';
    }, 1000);
}

// Show success toast notification
function showSuccessToast(message) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Show and auto-hide toast
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 3000
    });
    toast.show();
    
    // Remove toast element after it's hidden
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

// Add pulse animation
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);
</script>
@endpush