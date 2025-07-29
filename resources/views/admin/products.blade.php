@extends('layouts.layout')

@section('title', 'Tüm Ürünler')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Ürün Yönetimi</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Ürünler</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                Satıcıya Göre Filtrele
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('admin.products') }}">Tümü</a>
                                @foreach ($sellers as $seller)
                                    <a class="dropdown-item"
                                        href="{{ route('admin.products', ['seller_id' => $seller->id]) }}">
                                        {{ $seller->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Ürün Listesi</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Resim</th>
                                <th>Ürün Adı</th>
                                <th>Fiyat</th>
                                <th>Stok</th>
                                <th>Durum</th>
                                <th>Satıcı</th>
                                <th class="datatable-nosort">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                                alt="Product Image" width="80" height="80">
                                        @else
                                            <span>Resim Yok</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>${{ $product->price }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <span class="badge {{ $product->status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $product->status ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.seller.products', $product->user->id) }}">
                                            {{ $product->user->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                                href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.product.details', $product->id) }}">
                                                    <i class="dw dw-eye"></i> Görüntüle
                                                </a>
                                                <a class="dropdown-item" data-toggle="modal"
                                                    data-target="#editProductModal{{ $product->id }}" href="#">
                                                    <i class="dw dw-edit2"></i> Düzenle
                                                </a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('toggle-status-{{ $product->id }}').submit();">
                                                    <i class="dw {{ $product->status ? 'dw-ban' : 'dw-check' }}"></i>
                                                    {{ $product->status ? 'Pasifleştir' : 'Aktifleştir' }}
                                                </a>
                                                <form id="toggle-status-{{ $product->id }}"
                                                    action="{{ route('admin.product.toggleStatus', $product->id) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('delete-product-{{ $product->id }}').submit();">
                                                    <i class="dw dw-delete-3"></i> Sil
                                                </a>
                                                <form id="delete-product-{{ $product->id }}"
                                                    action="{{ route('admin.product.delete', $product->id) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Product Modal -->
                                <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-gradient-primary text-white border-0">
                                                <h4 class="modal-title font-weight-bold">
                                                    <i class="dw dw-edit2 mr-2"></i>Ürün Düzenle: <span class="badge badge-light text-primary">{{ $product->name }}</span>
                                                </h4>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('admin.product.update', $product->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <!-- Temel Bilgiler -->
                                                    <div class="form-section mb-4">
                                                        <h6 class="text-primary font-weight-bold mb-3">
                                                            <i class="dw dw-tag mr-2"></i>Temel Bilgiler
                                                        </h6>
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label for="productName{{ $product->id }}" class="font-weight-semibold text-dark">
                                                                        <i class="dw dw-product mr-1 text-info"></i>Ürün Adı
                                                                    </label>
                                                                    <input type="text" name="name" id="productName{{ $product->id }}"
                                                                        class="form-control form-control-lg border-2" value="{{ $product->name }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="productStock{{ $product->id }}" class="font-weight-semibold text-dark">
                                                                        <i class="dw dw-box mr-1 text-warning"></i>Stok Miktarı
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <input type="number" name="stock" id="productStock{{ $product->id }}"
                                                                            class="form-control form-control-lg border-2" value="{{ $product->stock }}" required>
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">Adet</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Fiyat ve Açıklama -->
                                                    <div class="form-section mb-4">
                                                        <h6 class="text-success font-weight-bold mb-3">
                                                            <i class="dw dw-money-2 mr-2"></i>Fiyat ve Açıklama
                                                        </h6>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="productPrice{{ $product->id }}" class="font-weight-semibold text-dark">
                                                                        <i class="dw dw-price-tag mr-1 text-success"></i>Fiyat
                                                                    </label>
                                                                    <div class="input-group">
                                                                        <input type="number" step="0.01" name="price" id="productPrice{{ $product->id }}"
                                                                            class="form-control form-control-lg border-2" value="{{ $product->price }}" required>
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text">₺</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="form-group">
                                                                    <label for="productDescription{{ $product->id }}" class="font-weight-semibold text-dark">
                                                                        <i class="dw dw-text-width mr-1 text-primary"></i>Ürün Açıklaması
                                                                    </label>
                                                                    <textarea name="description" id="productDescription{{ $product->id }}" 
                                                                        class="form-control form-control-lg border-2" rows="4" 
                                                                        placeholder="Ürün hakkında detaylı bilgi girin...">{{ $product->description }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Ürün Görselleri -->
                                                    <div class="form-section">
                                                        <h6 class="text-info font-weight-bold mb-3">
                                                            <i class="dw dw-image mr-2"></i>Ürün Görselleri
                                                        </h6>
                                                        <div class="form-group">
                                                            <label for="editProductImages{{ $product->id }}" class="font-weight-semibold text-dark">
                                                                <i class="dw dw-upload mr-1 text-warning"></i>Yeni Görseller Ekle
                                                            </label>
                                                            <div class="custom-file-container">
                                                                <input type="file" name="images[]" id="editProductImages{{ $product->id }}"
                                                                    class="form-control-file border-2 p-2" multiple accept="image/*">
                                                                <small class="text-muted d-block mt-2">
                                                                    <i class="dw dw-info mr-1"></i>
                                                                    Birden fazla görsel seçebilirsiniz (JPEG, PNG, WebP - Maks. 5MB)
                                                                </small>
                                                            </div>
                                                        </div>
                                                        
                                                        @if ($product->images->isNotEmpty())
                                                            <div class="mt-4">
                                                                <label class="font-weight-semibold text-dark d-block mb-2">
                                                                    <i class="dw dw-gallery mr-1 text-info"></i>Mevcut Görseller
                                                                </label>
                                                                <div class="product-images-container">
                                                                    <div class="d-flex flex-wrap">
                                                                        @foreach ($product->images as $image)
                                                                            <div class="product-image-item mr-2 mb-2 position-relative">
                                                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                                                    alt="Ürün Görseli" class="border rounded shadow-sm" 
                                                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-0 justify-content-between">
                                                    <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">
                                                        <i class="dw dw-cancel mr-2"></i>İptal
                                                    </button>
                                                    <button type="submit" class="btn btn-primary btn-lg px-4">
                                                        <i class="dw dw-save mr-2"></i>Değişiklikleri Kaydet
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Edit Product Modal End -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
/* Modal Geliştirmeleri */
.modal-xl {
    max-width: 1000px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.form-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border-left: 4px solid #007bff;
}

.form-control-lg {
    height: calc(2.5rem + 2px);
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
}

.border-2 {
    border-width: 2px !important;
    transition: all 0.3s ease;
}

.border-2:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.input-group-text {
    font-weight: 600;
    border-width: 2px;
    border-left: 0;
}

.font-weight-semibold {
    font-weight: 600;
}

.alert {
    border-radius: 10px;
}

/* Badge ve Button Geliştirmeleri */
.badge-light {
    background-color: rgba(255,255,255,0.9) !important;
    border: 1px solid rgba(0,0,0,0.1);
}

.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Form Section Headers */
.form-section h6 {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 20px;
}

/* Modal Shadow */
.modal-content {
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

/* Ürün Görselleri İçin Ek Stiller */
.product-images-container {
    max-height: 200px;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.product-image-item img {
    transition: all 0.3s ease;
}

.product-image-item img:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
}

.custom-file-container {
    border: 2px dashed #e9ecef;
    padding: 15px;
    border-radius: 8px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.custom-file-container:hover {
    border-color: #007bff;
    background: #f0f7ff;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 10px auto;
    }
    
    .form-section {
        padding: 15px;
    }
    
    .btn-lg {
        padding: 10px 20px;
        font-size: 14px;
    }
}
</style>