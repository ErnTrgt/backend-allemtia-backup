@extends('layouts.layout')

@section('title', 'Ürün Detayı')

@section('styles')
<style>
    /* Ana Stiller */
    .product-gallery {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    
    .product-gallery:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    
    /* Ana ürün görseli için standart boyut */
    .product-slide {
        height: 400px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        position: relative;
        overflow: hidden;
    }
    
    .product-slide img {
        width: 100%;
        height: 400px;
        object-fit: contain; /* Görselin oranını koruyarak tüm alanı kaplar */
        object-position: center center;
        transition: transform 0.3s ease;
        padding: 10px;
    }
    
    .product-slide img:hover {
        transform: scale(1.04);
    }
    
    /* Küçük resimler için standart boyut */
    .product-slide-nav {
        padding: 5px;
        cursor: pointer;
        height: 85px;
    }
    
    .product-slide-nav img {
        height: 80px;
        width: 100%;
        object-fit: contain; /* Görselin oranını koruyarak tüm alanı kaplar */
        object-position: center center;
        border-radius: 6px;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        background-color: #f8f9fa;
        padding: 3px;
    }
    
    .slick-current .product-slide-nav img {
        border-color: #1b00ff;
    }
    
    /* Modal içindeki görseller için */
    .modal .card-img-top {
        height: 100px;
        object-fit: contain;
        background-color: #f8f9fa;
        padding: 5px;
        transition: all 0.3s ease;
    }
    
    .modal .card-img-top:hover {
        transform: scale(1.05);
    }
    
    /* Görsel konteynerı için sabit yükseklik */
    .product-slider {
        min-height: 400px;
        background-color: #f8f9fa;
    }
    
    .product-slider-nav {
        min-height: 85px;
        margin-top: 10px;
    }
    
    /* Slick navigasyon okları */
    .slick-arrow {
        background: rgba(0, 0, 0, 0.2);
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex !important;
        align-items: center;
        justify-content: center;
        z-index: 5;
        transition: all 0.2s ease;
    }
    
    .slick-arrow:hover, .slick-arrow:focus {
        background: rgba(0, 0, 0, 0.5);
    }
    
    .slick-arrow:before {
        font-size: 18px;
    }
    
    .slick-prev {
        left: 10px;
    }
    
    .slick-next {
        right: 10px;
    }
    
    /* Görsel bulunamadığında gösterilecek yer tutucu */
    .product-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 400px;
        background-color: #f8f9fa;
        color: #adb5bd;
        font-size: 18px;
        flex-direction: column;
    }
    
    .product-placeholder i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.6;
    }
    
    /* Ürün Bilgileri Kartı */
    .product-info-card {
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        padding: 30px;
        height: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .product-info-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }
    
    .product-name {
        font-size: 28px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 15px;
    }
    
    /* Ürün Açıklaması */
    .product-description {
        font-size: 15px;
        color: #555;
        margin-bottom: 20px;
        line-height: 1.6;
    }
    
    /* Meta Bilgiler */
    .product-meta {
        margin: 20px 0;
        padding: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
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
        display: flex;
        align-items: center;
    }
    
    .meta-value {
        font-weight: 600;
        color: #333;
    }
    
    /* İstatistik Kutuları */
    .price-tag {
        font-size: 24px;
        font-weight: 700;
        color: #2c6dd5;
        display: inline-block;
        margin-right: 10px;
    }
    
    .stock-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
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
    
    /* Buton Stilleri */
    .btn-action {
        margin-top: 8px;
        border-radius: 6px;
        padding: 10px 15px;
        font-weight: 500;
        transition: all 0.25s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
    
    /* İstatistik Blokları */
    .product-stat {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        padding: 10px;
        border-radius: 8px;
    }
    
    .product-stat:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
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
        transition: all 0.3s ease;
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
    
    /* Modal Stilleri */
    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
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
        display: flex;
        align-items: center;
    }
    
    .modal-body {
        padding: 25px;
    }
    
    .form-group label {
        font-weight: 500;
        color: #555;
        margin-bottom: 8px;
    }
    
    /* Tab Stilleri */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #555;
        font-weight: 500;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: #2c6dd5;
        color: #2c6dd5;
    }
    
    .nav-tabs .nav-link.active {
        color: #2c6dd5;
        background-color: transparent;
        border-color: #2c6dd5;
    }
    
    /* Modal İçindeki Kartlar */
    .modal .card {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        overflow: hidden;
        border: 1px solid #e0e0e0;
    }
    
    .modal .card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    
    .modal .card-header {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .modal .card-title {
        font-size: 16px;
        font-weight: 600;
    }
    
    /* Kategori Select Stilleri */
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
        border-radius: 0 5px 5px 0;
        height: 45px;
        padding: 8px 15px;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .select2-container--default .select2-selection--single:hover {
        border-color: #2c6dd5;
    }
    
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #2c6dd5;
        box-shadow: 0 0 0 3px rgba(44, 109, 213, 0.2);
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333;
        line-height: 28px;
        padding-left: 5px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 43px;
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
        transition: all 0.2s ease;
    }
    
    .select2-results__option:last-child {
        border-bottom: none;
    }
    
    .select2-results__option:hover {
        background-color: #f0f7ff;
    }
    
    .select2-option {
        display: flex;
        align-items: center;
        font-weight: 500;
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
        background-color: #1b00ff;
    }
    
    .category-select-container {
        position: relative;
        margin-bottom: 15px;
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
    
    /* Görsel Galerisi */
    .product-images-gallery .card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .product-images-gallery .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .product-images-gallery .card .card-img-container {
        height: 100px; /* Fixed height for image container */
        overflow: hidden;
        position: relative;
        background-color: #f8f9fa;
    }
    
    .product-images-gallery .card .card-img-top {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 5px;
        transition: transform 0.3s ease;
    }
    
    .product-images-gallery .card .card-img-top:hover {
        transform: scale(1.08);
    }
    
    .product-images-gallery .card .card-body {
        padding: 8px 10px;
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
    }
    
    .product-images-gallery .card .custom-control-label {
        font-size: 12px;
        cursor: pointer;
    }
    
    /* Görsel İpuçları */
    .image-tips {
        margin-top: 15px;
    }
    
    .image-tips .alert {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        animation: fadeIn 0.5s ease-in-out;
    }
    
    /* Varyant Tablosu */
    .table {
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 12px 15px;
        border-bottom: 2px solid #e9ecef;
        color: #495057;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f0f7ff;
        transform: translateX(3px);
    }
    
    .table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    /* Animasyonlar */
    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(10px);}
        to {opacity: 1; transform: translateY(0);}
    }
    
    /* Silinmek için seçilmiş görseller */
    .image-card.selected-for-deletion img {
        opacity: 0.5;
    }
    
    .image-card.selected-for-deletion {
        border-color: #ff6b6b;
        box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.3);
    }
    
    /* Slick Okları */
    .slick-arrow {
        z-index: 10;
        width: 36px;
        height: 36px;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 50%;
        display: flex !important;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .slick-arrow:before {
        display: none;
    }
    
    .slick-arrow i {
        color: #fff;
        font-size: 24px;
    }
    
    .slick-arrow:hover {
        background: rgba(0, 0, 0, 0.5);
        transform: scale(1.1);
    }
    
    /* Responsive Düzenlemeler */
    @media (max-width: 768px) {
        .product-slide {
            height: 300px;
        }
        
        .product-slide img {
            height: 300px;
        }
        
        .product-name {
            font-size: 22px;
        }
        
        .product-info-card {
            padding: 20px;
        }
        
        .meta-label {
            min-width: 90px;
        }
    }
</style>
@endsection

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <!-- Page Header - Daha modern başlık kısmı -->
            <div class="page-header mb-30">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="title d-flex align-items-center">
                            <h4 class="mb-0 text-blue">
                                <i class="icon-copy fa fa-cube mr-2" aria-hidden="true"></i> 
                                Ürün Detayları
                            </h4>
                            <span class="ml-3">
                                @if($product->status)
                                    <span class="badge badge-pill badge-success font-14">Aktif</span>
                                @else
                                    <span class="badge badge-pill badge-danger font-14">Pasif</span>
                                @endif
                            </span>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation" class="mt-2">
                            <ol class="breadcrumb bg-transparent p-0 mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-primary"><i class="icon-copy fa fa-home mr-1"></i>Ana Sayfa</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.products') }}" class="text-primary"><i class="icon-copy fa fa-cubes mr-1"></i>Ürünler</a></li>
                                <li class="breadcrumb-item active text-muted" aria-current="page">{{ $product->name }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="btn-group mt-2" role="group">
                            <a href="#" class="btn btn-primary btn-rounded" data-toggle="modal" data-target="#editProductModal">
                                <i class="icon-copy fa fa-edit mr-1"></i> Düzenle
                            </a>
                            <a href="#" class="btn btn-light btn-rounded ml-2 border" onclick="window.history.back()">
                                <i class="icon-copy fa fa-arrow-left mr-1"></i> Geri
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-wrap mb-30">
                <div class="row">
                    <div class="col-lg-5 col-md-12 col-sm-12 mb-30">
                        <!-- Görsel Galerisi Kartı -->
                        <div class="product-gallery card-box p-3 h-100">
                            <!-- Kart Başlığı -->
                            <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-3">
                                <h5 class="font-weight-bold mb-0">
                                    <i class="icon-copy fa fa-images text-primary mr-2"></i>Ürün Görselleri
                                </h5>
                                <span class="badge badge-light border">
                                    <i class="icon-copy fa fa-camera mr-1"></i>
                                    {{ $product->images ? count($product->images) : 0 }}
                                </span>
                            </div>
                            
                            <!-- Ana Görsel Slider -->
                            <div class="product-slider slider-arrow">
                                @if($product->images && count($product->images) > 0)
                                    @foreach ($product->images as $image)
                                        <div class="product-slide">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="product-placeholder">
                                        <i class="icon-copy fa fa-image"></i>
                                        <span>Ürün görseli bulunamadı</span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Küçük Görsel Slider -->
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
                        <!-- Ürün Bilgileri Kartı -->
                        <div class="product-info-card">
                            <!-- Ürün Başlığı ve Durum -->
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="product-name mb-0">{{ $product->name }}</h2>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="productActionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-copy fa fa-cog mr-1"></i> İşlemler
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="productActionsDropdown">
                                        <a class="dropdown-item text-primary" href="#" data-toggle="modal" data-target="#editProductModal">
                                            <i class="icon-copy fa fa-edit mr-2"></i> Düzenle
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('toggle-status-form').submit();">
                                            @if($product->status)
                                                <i class="icon-copy fa fa-eye-slash mr-2 text-warning"></i> Devre Dışı Bırak
                                            @else
                                                <i class="icon-copy fa fa-eye mr-2 text-success"></i> Aktifleştir
                                            @endif
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(event)">
                                            <i class="icon-copy fa fa-trash mr-2"></i> Sil
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Fiyat ve Stok Bilgileri -->
                            <div class="product-stats mt-4">
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
                            
                            <!-- Kategori ve Diğer Bilgiler -->
                            <div class="product-meta mt-4 py-3 px-4 bg-light rounded-lg">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="product-meta-item">
                                            <div class="meta-label"><i class="icon-copy fa fa-folder-open text-primary mr-2"></i>Kategori</div>
                                            <div class="meta-value">
                                                @if($product->category)
                                                    <span class="badge badge-primary">{{ $product->category->name }}</span>
                                                @else
                                                    <span class="badge badge-secondary">Kategorisiz</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="product-meta-item">
                                            <div class="meta-label"><i class="icon-copy fa fa-barcode text-primary mr-2"></i>SKU</div>
                                            <div class="meta-value">{{ $product->sku ?? 'Belirtilmemiş' }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="product-meta-item">
                                            <div class="meta-label"><i class="icon-copy fa fa-clock-o text-primary mr-2"></i>Oluşturulma</div>
                                            <div class="meta-value">{{ $product->created_at->format('d.m.Y H:i') }}</div>
                                        </div>
                                        
                                        <div class="product-meta-item">
                                            <div class="meta-label"><i class="icon-copy fa fa-refresh text-primary mr-2"></i>Güncelleme</div>
                                            <div class="meta-value">{{ $product->updated_at->format('d.m.Y H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ürün Açıklaması -->
                            <div class="product-description mt-4">
                                <h5 class="font-weight-bold mb-3">
                                    <i class="icon-copy fa fa-file-text-o text-primary mr-2"></i>Ürün Açıklaması
                                </h5>
                                <div class="p-3 bg-white border rounded">
                                    <p class="mb-0">{{ $product->description ?: 'Bu ürün için açıklama bulunmamaktadır.' }}</p>
                                </div>
                            </div>
                            
                            <!-- Aksiyon Butonları -->
                            <div class="action-buttons mt-4 pt-3 border-top">
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
                                        <a href="#" class="btn btn-danger btn-block btn-action btn-delete" onclick="confirmDelete(event)">
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
                </div>
                
                @if(isset($product->variants) && count($product->variants) > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card-box p-3">
                            <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-3">
                                <h5 class="font-weight-bold mb-0">
                                    <i class="icon-copy fa fa-th-list text-primary mr-2"></i>Ürün Varyantları
                                </h5>
                                <span class="badge badge-light border">
                                    <i class="icon-copy fa fa-cubes mr-1"></i>
                                    {{ count($product->variants) }} varyant
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Varyant</th>
                                            <th>Fiyat</th>
                                            <th>Stok</th>
                                            <th>Durum</th>
                                            <th class="text-center">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variants as $index => $variant)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><span class="font-weight-bold">{{ $variant->name }}</span></td>
                                            <td><span class="text-primary">{{ number_format($variant->price, 2) }} TL</span></td>
                                            <td>
                                                @if($variant->stock > 10)
                                                    <span class="badge badge-success">{{ $variant->stock }} Adet</span>
                                                @elseif($variant->stock > 0)
                                                    <span class="badge badge-warning">{{ $variant->stock }} Adet</span>
                                                @else
                                                    <span class="badge badge-danger">Tükendi</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($variant->status)
                                                    <span class="badge badge-success"><i class="fa fa-check-circle mr-1"></i>Aktif</span>
                                                @else
                                                    <span class="badge badge-danger"><i class="fa fa-times-circle mr-1"></i>Pasif</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <a class="btn btn-light btn-sm" href="#" role="button" data-toggle="dropdown">
                                                        <i class="fa fa-ellipsis-h"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fa fa-pencil text-primary mr-2"></i> Düzenle
                                                        </a>
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fa fa-eye text-info mr-2"></i> Görüntüle
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#">
                                                            <i class="fa fa-trash mr-2"></i> Sil
                                                        </a>
                                                    </div>
                                                </div>
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
                <div class="modal-header bg-light">
                    <h4 class="modal-title" id="editProductModalLabel">
                        <i class="icon-copy fa fa-edit mr-2 text-primary"></i>Ürün Düzenleme
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Form Tab Navigasyonu -->
                        <ul class="nav nav-tabs mb-4" id="productEditTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                                    <i class="icon-copy fa fa-info-circle mr-1"></i> Genel Bilgiler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="images-tab" data-toggle="tab" href="#images" role="tab">
                                    <i class="icon-copy fa fa-image mr-1"></i> Görseller
                                </a>
                            </li>
                        </ul>
                        
                        <!-- Tab İçerikleri -->
                        <div class="tab-content" id="productEditTabContent">
                            <!-- Genel Bilgiler Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
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
                                
                                <!-- Kategori Seçimi -->
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
                                
                                <!-- Ürün Açıklaması -->
                                <div class="form-group">
                                    <label for="productDescription">Ürün Açıklaması</label>
                                    <textarea name="description" id="productDescription" class="form-control" rows="4" placeholder="Ürün detaylarını giriniz...">{{ $product->description }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Görseller Tab -->
                            <div class="tab-pane fade" id="images" role="tabpanel">
                                <!-- Yeni Görsel Yükleme -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">
                                            <i class="icon-copy fa fa-upload text-primary mr-2"></i>Yeni Görseller
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group mb-0">
                                            <div class="custom-file">
                                                <input type="file" name="images[]" class="custom-file-input" id="editProductImages" multiple accept="image/*">
                                                <label class="custom-file-label" for="editProductImages">Görselleri seçin...</label>
                                            </div>
                                            <div class="mt-2 image-tips">
                                                <div class="alert alert-light border p-2">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="icon-copy fa fa-info-circle text-primary mr-2"></i>
                                                        <strong>Görsel Yükleme İpuçları:</strong>
                                                    </div>
                                                    <ul class="mb-0 pl-4">
                                                        <li><small>Önerilen boyut: 800x800 piksel</small></li>
                                                        <li><small>Maksimum dosya boyutu: 2MB</small></li>
                                                        <li><small>İzin verilen formatlar: JPG, PNG, WEBP</small></li>
                                                        <li><small>Görseller ürünü tam olarak göstermeli</small></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Mevcut Görseller -->
                                @if($product->images && count($product->images) > 0)
                                <div class="card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="icon-copy fa fa-images text-primary mr-2"></i>Mevcut Görseller
                                        </h5>
                                        <span class="badge badge-pill badge-primary">{{ count($product->images) }} görsel</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row product-images-gallery">
                                            @foreach($product->images as $key => $image)
                                            <div class="col-md-3 col-6 mb-3">
                                                <div class="card image-card">
                                                    <div class="card-img-container">
                                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top" alt="Ürün Görseli">
                                                    </div>
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
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="icon-copy fa fa-times mr-1"></i>İptal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-copy fa fa-save mr-1"></i>Değişiklikleri Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Görsel yükleyici için dosya adını göster ve temizle
    $('.custom-file-input').on('change', function() {
        let fileName = '';
        let fileCount = 0;
        
        // Önceki alert mesajlarını temizle
        $('.image-tips .alert-success, .image-tips .alert-warning').remove();
        
        if(this.files && this.files.length > 1) {
            fileCount = this.files.length;
            fileName = fileCount + ' görsel seçildi';
        } else if(this.files && this.files.length > 0) {
            fileName = this.files[0].name;
        }
        
        if(fileName) {
            $(this).next('.custom-file-label').html(fileName);
            
            // Seçilen dosya sayısını göster
            if(fileCount > 0) {
                // Maksimum görsel sayısı kontrol et
                let maxFiles = 10; 
                if(fileCount > maxFiles) {
                    $('.image-tips').append('<div class="alert alert-warning mt-2 animated fadeIn">' + 
                                          '<i class="icon-copy fa fa-exclamation-triangle mr-2"></i>' +
                                          'En fazla ' + maxFiles + ' görsel yüklenebilir. İlk ' + maxFiles + ' görsel işlenecek.' +
                                          '</div>');
                } else {
                    $('.image-tips').append('<div class="alert alert-success mt-2 animated fadeIn">' + 
                                          '<i class="icon-copy fa fa-check-circle mr-2"></i>' +
                                          fileCount + ' görsel yüklenmeye hazır.' +
                                          '</div>');
                }
            }
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
    
    // Slider için geliştirilmiş ayarlar
    $(document).ready(function(){
        // Ana görsel slideri
        $('.product-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            fade: true,
            asNavFor: '.product-slider-nav',
            lazyLoad: 'ondemand',
            prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-angle-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fa fa-angle-right"></i></button>',
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        arrows: true,
                        dots: false
                    }
                }
            ]
        });
        
        // Küçük resimler slideri
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
                        slidesToShow: 3
                    }
                }
            ]
        });
        
        // Görsel silinecek mi checkbox kontrolü
        $('.custom-control-input[name="delete_images[]"]').on('change', function() {
            const imageCard = $(this).closest('.image-card');
            if($(this).is(':checked')) {
                imageCard.addClass('selected-for-deletion');
            } else {
                imageCard.removeClass('selected-for-deletion');
            }
        });
        
        // Tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Modal sekmeleri arasında geçiş yapıldığında animasyon ekle
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $($(e.target).attr('href'))
                .find('.card')
                .addClass('animated fadeIn');
        });
        
        // Açılışta animasyonları ekle
        $('.card-box, .product-info-card').addClass('animated fadeIn');
    });
    
    // Ürün silme onay işlevi
    function confirmDelete(event) {
        event.preventDefault();
        if (confirm('Bu ürünü silmek istediğinize emin misiniz? Bu işlem geri alınamaz.')) {
            document.getElementById('delete-product-form').submit();
        }
    }
    
    // Tab değişimlerini kaydet
    $('#productEditTabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
        localStorage.setItem('activeProductTab', $(this).attr('href'));
    });
    
    // Son seçilen tabı göster
    var activeTab = localStorage.getItem('activeProductTab');
    if(activeTab){
        $('#productEditTabs a[href="' + activeTab + '"]').tab('show');
    }
</script>
@endsection
