@extends('layouts.layout')

@section('title', 'Ürün Detayı')

@section('styles')
<style>
    .product-gallery {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }
    
    .product-slide img {
        width: 100%;
        border-radius: 8px;
        height: 400px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .product-slide img:hover {
        transform: scale(1.02);
    }
    
    .product-slide-nav {
        padding: 5px;
        cursor: pointer;
    }
    
    .product-slide-nav img {
        height: 80px;
        width: 100%;
        object-fit: cover;
        border-radius: 6px;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    
    .slick-current .product-slide-nav img {
        border-color: #1b00ff;
    }
    
    .product-info-card {
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        padding: 30px;
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .product-info-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .product-name {
        font-size: 28px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 15px;
    }
    
    .product-description {
        font-size: 16px;
        color: #555;
        margin-bottom: 20px;
        line-height: 1.6;
    }
    
    .product-meta {
        margin: 20px 0;
        padding: 15px 0;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .product-meta-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .meta-label {
        font-weight: 500;
        color: #666;
        min-width: 120px;
    }
    
    .meta-value {
        font-weight: 600;
        color: #333;
    }
    
    .price-tag {
        font-size: 24px;
        font-weight: 700;
        color: #2c6dd5;
        display: inline-block;
        margin-right: 10px;
    }
    
    .stock-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 500;
    }
    
    .stock-available {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    
    .stock-limited {
        background-color: #fff8e1;
        color: #ff8f00;
    }
    
    .stock-out {
        background-color: #ffebee;
        color: #d32f2f;
    }
    
    .btn-action {
        margin-top: 8px;
        border-radius: 6px;
        padding: 10px 15px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-edit {
        background-color: #2c6dd5;
        border-color: #2c6dd5;
    }
    
    .btn-edit:hover {
        background-color: #1a56b3;
        border-color: #1a56b3;
    }
    
    .btn-toggle {
        background-color: transparent;
        color: #2c6dd5;
        border: 1px solid #2c6dd5;
    }
    
    .btn-toggle:hover {
        background-color: rgba(44, 109, 213, 0.1);
    }
    
    .btn-delete {
        background-color: #ff4747;
        border-color: #ff4747;
    }
    
    .btn-delete:hover {
        background-color: #e03a3a;
        border-color: #e03a3a;
    }
    
    .action-buttons {
        margin-top: 20px;
    }
    
    .product-stat {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .stat-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 18px;
    }
    
    .price-icon {
        background-color: rgba(44, 109, 213, 0.1);
        color: #2c6dd5;
    }
    
    .stock-icon {
        background-color: rgba(56, 142, 60, 0.1);
        color: #388e3c;
    }
    
    .date-icon {
        background-color: rgba(255, 143, 0, 0.1);
        color: #ff8f00;
    }
    
    .stat-info {
        flex-grow: 1;
    }
    
    .stat-label {
        font-size: 14px;
        color: #666;
        margin-bottom: 3px;
    }
    
    .stat-value {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }
    
    /* Modal styles */
    .modal-content {
        border-radius: 10px;
        border: none;
    }
    
    .modal-header {
        border-bottom: 1px solid #f0f0f0;
        padding: 20px 25px;
        background: #f8f9fc;
        border-radius: 10px 10px 0 0;
    }
    
    .modal-title {
        font-weight: 600;
        color: #333;
    }
    
    .modal-body {
        padding: 25px;
    }
    
    .form-group label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
    }
    
    /* Kategori Select Styles */
    .category-select-wrapper {
        position: relative;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    
    .category-select-wrapper.select-active {
        transform: translateY(-2px);
    }
    
    .select2-container--default .select2-selection--single {
        border: 1px solid #d4d4d4;
        border-radius: 8px;
        height: 50px;
        padding: 10px 15px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        background: linear-gradient(to bottom, #ffffff 0%, #f9f9f9 100%);
    }
    
    .select2-container--default .select2-selection--single:hover {
        border-color: #2c6dd5;
        background: #ffffff;
    }
    
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #2c6dd5;
        box-shadow: 0 0 0 3px rgba(44, 109, 213, 0.2);
        background: #ffffff;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333;
        line-height: 30px;
        font-size: 15px;
        padding-left: 5px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 48px;
        width: 30px;
        right: 8px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #2c6dd5 transparent transparent transparent;
        border-width: 6px 6px 0 6px;
        margin-left: -6px;
        margin-top: -3px;
        transition: all 0.2s ease;
    }
    
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #2c6dd5 transparent;
        border-width: 0 6px 6px 6px;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2c6dd5;
        color: white;
    }
    
    .select2-dropdown {
        border-color: #2c6dd5;
        border-radius: 8px;
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    
    .select2-search--dropdown {
        padding: 10px;
        background-color: #f8f9fc;
        border-bottom: 1px solid #eaeaea;
    }
    
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #ccc;
        border-radius: 6px;
        padding: 10px 12px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.075);
    }
    
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #2c6dd5;
        outline: none;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.075), 0 0 5px rgba(44, 109, 213, 0.3);
    }
    
    .select2-results__option {
        padding: 10px 15px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .select2-results__option:last-child {
        border-bottom: none;
    }
    
    .select2-option {
        display: flex;
        align-items: center;
        font-weight: 500;
    }
    
    .select-help-text {
        margin-top: 8px;
        color: #666;
        font-style: italic;
        padding-left: 5px;
    }
    
    /* Label style for category */
    label[for="productCategory"] {
        display: inline-block;
        margin-bottom: 15px;
        font-size: 16px;
        color: #333;
        position: relative;
        padding-left: 0;
        background: linear-gradient(45deg, #2c6dd5, #4c8be6);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        text-shadow: 1px 1px 2px rgba(255,255,255,0.5);
    }
    
    label[for="productCategory"] i {
        font-size: 18px;
        vertical-align: middle;
        margin-right: 8px;
        color: #2c6dd5;
        filter: drop-shadow(1px 1px 1px rgba(0,0,0,0.1));
    }
    
    /* Animated effect for select focus */
    .custom-select2:focus + .select-icon-indicator {
        transform: rotate(180deg);
    }
    
    /* Select placeholder styling */
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #aaa;
    }
    
    /* Styling for the dropdown items */
    .select2-container--default .select2-results__option {
        padding: 8px 12px;
        transition: all 0.2s ease;
    }
    
    .select2-container--default .select2-results__option:hover {
        background-color: #f0f7ff;
    }
    
    /* Modal içindeki kategori seçimi için stiller */
    .modal-select-group {
        position: relative;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        border-radius: 6px;
        overflow: hidden;
    }
    
    .modal-select-group .input-group-text {
        border: none;
        padding: 12px 15px;
    }
    
    .category-select-container {
        position: relative;
        margin-bottom: 15px;
    }
    
    /* Modal içindeki Select2 stilleri */
    .select2-container--default .select2-selection--single {
        height: 45px;
        border: 1px solid #e0e0e0;
        border-radius: 0 5px 5px 0;
        padding: 8px 12px;
        display: flex;
        align-items: center;
    }
    
    .modal .select2-container--default .select2-selection--single {
        border-left: none;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333;
        padding-left: 0;
        line-height: 28px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px;
        position: absolute;
        top: 0;
        right: 10px;
    }
    
    /* Modal içindeki Select2 dropdown için z-index ayarı */
    .select2-container--open {
        z-index: 9999;
    }
    
    .modal-body .form-group {
        margin-bottom: 20px;
    }
    
    .modal-body label {
        color: #333;
        font-weight: 500;
    }
</style>
@endsection

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4><i class="icon-copy fa fa-cube mr-2" aria-hidden="true"></i> Ürün Detayı</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="icon-copy fa fa-home mr-1"></i>Ana Sayfa</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.products') }}"><i class="icon-copy fa fa-cubes mr-1"></i>Ürünler</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="product-wrap mb-30">
                <div class="row">
                    <div class="col-lg-5 col-md-12 col-sm-12 mb-30">
                        <div class="product-gallery card-box p-3">
                            <div class="product-slider slider-arrow">
                                @if($product->images && count($product->images) > 0)
                                    @foreach ($product->images as $image)
                                        <div class="product-slide">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="product-slide">
                                        <img src="{{ asset('admin/src/images/product-placeholder.jpg') }}" alt="Ürün Görseli Yok">
                                    </div>
                                @endif
                            </div>
                            @if($product->images && count($product->images) > 1)
                                <div class="product-slider-nav mt-3">
                                    @foreach ($product->images as $image)
                                        <div class="product-slide-nav">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Küçük Resim">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-lg-7 col-md-12 col-sm-12 mb-30">
                        <div class="product-info-card">
                            <h2 class="product-name">{{ $product->name }}</h2>
                            
                            <div class="product-stats">
                                <div class="row">
                                    <div class="col-md-4 col-6">
                                        <div class="product-stat">
                                            <div class="stat-icon price-icon">
                                                <i class="icon-copy fa fa-tag"></i>
                                            </div>
                                            <div class="stat-info">
                                                <div class="stat-label">Fiyat</div>
                                                <div class="stat-value">{{ number_format($product->price, 2) }} TL</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 col-6">
                                        <div class="product-stat">
                                            <div class="stat-icon stock-icon">
                                                <i class="icon-copy fa fa-cubes"></i>
                                            </div>
                                            <div class="stat-info">
                                                <div class="stat-label">Stok</div>
                                                <div class="stat-value">
                                                    @if($product->stock > 10)
                                                        <span class="stock-badge stock-available">{{ $product->stock }} Adet</span>
                                                    @elseif($product->stock > 0)
                                                        <span class="stock-badge stock-limited">{{ $product->stock }} Adet</span>
                                                    @else
                                                        <span class="stock-badge stock-out">Tükendi</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 col-6">
                                        <div class="product-stat">
                                            <div class="stat-icon date-icon">
                                                <i class="icon-copy fa fa-calendar"></i>
                                            </div>
                                            <div class="stat-info">
                                                <div class="stat-label">Durum</div>
                                                <div class="stat-value">
                                                    @if($product->status)
                                                        <span class="badge badge-success">Aktif</span>
                                                    @else
                                                        <span class="badge badge-danger">Pasif</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="product-description mt-4">
                                <h5 class="font-weight-bold">Ürün Açıklaması</h5>
                                <p>{{ $product->description ?: 'Bu ürün için açıklama bulunmamaktadır.' }}</p>
                            </div>
                            
                            <div class="product-meta">
                                <div class="product-meta-item">
                                    <div class="meta-label">Kategori</div>
                                    <div class="meta-value">
                                        @if($product->category)
                                            <span class="badge badge-primary">{{ $product->category->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">Kategorisiz</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="product-meta-item">
                                    <div class="meta-label">SKU</div>
                                    <div class="meta-value">{{ $product->sku ?? 'Belirtilmemiş' }}</div>
                                </div>
                                
                                <div class="product-meta-item">
                                    <div class="meta-label">Oluşturulma Tarihi</div>
                                    <div class="meta-value">{{ $product->created_at->format('d.m.Y H:i') }}</div>
                                </div>
                                
                                <div class="product-meta-item">
                                    <div class="meta-label">Son Güncelleme</div>
                                    <div class="meta-value">{{ $product->updated_at->format('d.m.Y H:i') }}</div>
                                </div>
                            </div>
                            
                            <div class="action-buttons">
                                <div class="row">
                                    <div class="col-md-4 col-6">
                                        <a href="#" class="btn btn-primary btn-block btn-action btn-edit" data-toggle="modal" data-target="#editProductModal">
                                            <i class="icon-copy fa fa-pencil-square-o mr-2"></i>Düzenle
                                        </a>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <a href="#" class="btn btn-outline-primary btn-block btn-action btn-toggle"
                                            onclick="event.preventDefault(); document.getElementById('toggle-status-form').submit();">
                                            @if($product->status)
                                                <i class="icon-copy fa fa-eye-slash mr-2"></i>Devre Dışı Bırak
                                            @else
                                                <i class="icon-copy fa fa-eye mr-2"></i>Aktifleştir
                                            @endif
                                        </a>
                                        <form id="toggle-status-form" action="{{ route('admin.product.toggleStatus', $product->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </div>
                                    <div class="col-md-4 col-12 mt-md-0 mt-2">
                                        <a href="#" class="btn btn-danger btn-block btn-action btn-delete"
                                            onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz? Bu işlem geri alınamaz.') ? document.getElementById('delete-product-form').submit() : false;">
                                            <i class="icon-copy fa fa-trash mr-2"></i>Sil
                                        </a>
                                        <form id="delete-product-form" action="{{ route('admin.product.delete', $product->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(isset($product->variants) && count($product->variants) > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card-box p-3">
                            <h5 class="font-weight-bold mb-3"><i class="icon-copy fa fa-th-list mr-2"></i>Ürün Varyantları</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Varyant</th>
                                            <th>Fiyat</th>
                                            <th>Stok</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variants as $variant)
                                        <tr>
                                            <td>{{ $variant->name }}</td>
                                            <td>{{ number_format($variant->price, 2) }} TL</td>
                                            <td>{{ $variant->stock }}</td>
                                            <td>
                                                @if($variant->status)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-danger">Pasif</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editProductModalLabel"><i class="icon-copy fa fa-pencil-square-o mr-2"></i>Ürünü Düzenle</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="productName">Ürün Adı</label>
                            <input type="text" name="name" id="productName" class="form-control form-control-lg" value="{{ $product->name }}" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productPrice">Fiyat (TL)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">₺</span>
                                        </div>
                                        <input type="number" name="price" id="productPrice" class="form-control" value="{{ $product->price }}" step="0.01" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="productStock">Stok Miktarı</label>
                                    <input type="number" name="stock" id="productStock" class="form-control" value="{{ $product->stock }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="productCategory" class="control-label font-weight-bold mb-2">
                                <i class="icon-copy fa fa-tags mr-2 text-primary"></i>Kategori Seçimi
                            </label>
                            <div class="category-select-container">
                                <div class="input-group modal-select-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="icon-copy fa fa-list-ul"></i>
                                        </span>
                                    </div>
                                    <select name="category_id" id="productCategory" class="form-control modal-select2">
                                        <option value="">Kategori Seçin</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="form-text text-muted mt-2">
                                    <i class="icon-copy fa fa-info-circle mr-1"></i>Ürününüzün görüntüleneceği kategoriyi seçin
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="productDescription">Ürün Açıklaması</label>
                            <textarea name="description" id="productDescription" class="form-control" rows="4">{{ $product->description }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="editProductImages">Ürün Görselleri</label>
                            <div class="custom-file">
                                <input type="file" name="images[]" class="custom-file-input" id="editProductImages" multiple accept="image/*">
                                <label class="custom-file-label" for="editProductImages">Görselleri seçin...</label>
                                <small class="form-text text-muted">Birden fazla görsel seçebilirsiniz. Yeni görseller mevcut görsellere eklenecektir.</small>
                            </div>
                        </div>
                        
                        @if($product->images && count($product->images) > 0)
                        <div class="form-group mt-3">
                            <label>Mevcut Görseller</label>
                            <div class="row">
                                @foreach($product->images as $key => $image)
                                <div class="col-md-3 col-6 mb-3">
                                    <div class="card">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top" alt="Ürün Görseli" style="height: 100px; object-fit: cover;">
                                        <div class="card-body p-2 text-center">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="deleteImage{{ $key }}" name="delete_images[]" value="{{ $image->id }}">
                                                <label class="custom-control-label" for="deleteImage{{ $key }}">Sil</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-copy fa fa-save mr-2"></i>Değişiklikleri Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Görsel yükleyici için dosya adını göster
    $('.custom-file-input').on('change', function() {
        let fileName = '';
        if(this.files && this.files.length > 1) {
            fileName = (this.getAttribute('data-multiple-caption') || '').replace('{count}', this.files.length);
        } else if(this.files && this.files.length > 0) {
            fileName = this.files[0].name;
        }
        
        if(fileName) {
            $(this).next('.custom-file-label').html(fileName);
        }
    });
    
    // Modal içindeki kategori seçimi için Select2 ayarları
    $('.modal-select2').select2({
        dropdownParent: $('#editProductModal .modal-content'),
        placeholder: "Kategori seçin",
        allowClear: true,
        width: '100%',
        templateResult: formatCategoryOption,
        templateSelection: formatCategorySelection
    });
    
    // Normal sayfadaki kategori seçimi için Select2
    $('.custom-select2').select2({
        placeholder: "Kategori seçin",
        allowClear: true,
        width: '100%'
    });
    
    // Kategori seçeneklerini özelleştir
    function formatCategoryOption(category) {
        if (!category.id) {
            return $('<span><i class="icon-copy fa fa-list mr-2 text-muted"></i> Kategori Seçin</span>');
        }
        
        return $('<span><i class="icon-copy fa fa-folder mr-2 text-primary"></i> ' + category.text + '</span>');
    }
    
    // Seçilen kategori görünümünü özelleştir
    function formatCategorySelection(category) {
        if (!category.id) {
            return $('<span><i class="icon-copy fa fa-list mr-2 text-muted"></i> Kategori Seçin</span>');
        }
        
        return $('<span><i class="icon-copy fa fa-folder-open mr-2 text-primary"></i> ' + category.text + '</span>');
    }
    
    // Modal açıldığında Select2'yi yenile
    $('#editProductModal').on('shown.bs.modal', function () {
        $('.modal-select2').select2('destroy').select2({
            dropdownParent: $('#editProductModal .modal-content'),
            placeholder: "Kategori seçin",
            allowClear: true,
            width: '100%',
            templateResult: formatCategoryOption,
            templateSelection: formatCategorySelection
        });
    });
    
    // Slider için ek ayarlar
    $(document).ready(function(){
        $('.product-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            fade: true,
            asNavFor: '.product-slider-nav'
        });
        
        $('.product-slider-nav').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.product-slider',
            dots: false,
            arrows: false,
            centerMode: false,
            focusOnSelect: true,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2
                    }
                }
            ]
        });
    });
</script>
@endsection
