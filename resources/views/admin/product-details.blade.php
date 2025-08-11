@extends('layouts.admin-modern')

@section('title', 'Ürün Detayı')

@section('header-title', 'Ürün Detayı')

@push('styles')
<style>
    /* Glass Morphism Ana Stiller */
    .product-gallery {
        position: relative;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .product-gallery:hover {
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        transform: translateY(-4px);
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
    
    /* Ürün Bilgileri Kartı - Glass Morphism */
    .product-info-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        padding: 30px;
        height: 100%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .product-info-card:hover {
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        transform: translateY(-4px);
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
    
    /* Meta Bilgiler - Glass Style */
    .product-meta {
        margin: 20px 0;
        padding: 20px;
        background: rgba(248, 249, 250, 0.6);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
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
    
    /* İstatistik Kutuları - Modern */
    .price-tag {
        font-size: 24px;
        font-weight: 700;
        color: #0051BB;
        display: inline-block;
        margin-right: 10px;
    }
    
    .stock-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    
    .stock-available {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }
    
    .stock-limited {
        background-color: rgba(245, 158, 11, 0.1);
        color: #F59E0B;
    }
    
    .stock-out {
        background-color: rgba(239, 68, 68, 0.1);
        color: #EF4444;
    }
    
    /* Buton Stilleri - Modern Glass */
    .btn-action {
        margin-top: 8px;
        border-radius: 12px;
        padding: 12px 20px;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    
    .btn-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .btn-edit {
        background: linear-gradient(135deg, #A90000, #C1121F);
        border: none;
        color: white;
    }
    
    .btn-edit:hover {
        background: linear-gradient(135deg, #900000, #A01010);
        box-shadow: 0 8px 25px rgba(169, 0, 0, 0.3);
    }
    
    .btn-toggle {
        background: rgba(169, 0, 0, 0.1);
        color: #A90000;
        border: 1px solid rgba(169, 0, 0, 0.3);
    }
    
    .btn-toggle:hover {
        background: rgba(169, 0, 0, 0.2);
        border-color: #A90000;
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #EF4444, #DC2626);
        border: none;
        color: white;
    }
    
    .btn-delete:hover {
        background: linear-gradient(135deg, #DC2626, #B91C1C);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
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
        width: 50px;
        height: 50px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.6));
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .price-icon {
        background: linear-gradient(135deg, rgba(169, 0, 0, 0.1), rgba(193, 18, 31, 0.1));
        color: #A90000;
    }
    
    .stock-icon {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(34, 197, 94, 0.1));
        color: #10B981;
    }
    
    .date-icon {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(251, 191, 36, 0.1));
        color: #F59E0B;
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
    
    /* Modal Stilleri - Users Sayfası ile Aynı */
    .modal-content {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .modal-header {
        background: #c1121f0d;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }
    
    .modal-title {
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
    }
    
    .modal-body {
        padding: 2rem;
    }
    
    .modal-footer {
        background: rgba(255, 255, 255, 0.8);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }
    
    .btn-close {
        color: #6b7280;
        opacity: 1;
    }
    
    .btn-close:hover {
        color: #374151;
    }
    
    /* Form Stilleri - Users Modal'a Uygun */
    .form-control {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 0.625rem 1rem;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        background: white;
        border-color: #A90000;
        box-shadow: 0 0 0 3px rgba(169, 0, 0, 0.1);
        outline: none;
    }
    
    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    h6.text-muted {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 1rem;
    }
    
    /* Form Section Stilleri kaldırıldı - Users gibi basit olacak */
    .form-section {
        margin-bottom: 1.5rem;
    }
    
    .form-section-title {
        display: none; /* Users modal'ında böyle section başlıkları yok */
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    /* Mevcut Görseller */
    .current-images {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 0.5rem;
    }
    
    .current-image-item {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #e5e7eb;
    }
    
    .current-image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .current-image-item:hover .image-overlay {
        opacity: 1;
    }
    
    .btn-remove-image {
        background: #ef4444;
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-remove-image:hover {
        background: #dc2626;
        transform: scale(1.1);
    }
    
    
    /* Modal close button için ek stiller zaten yukarıda tanımlı */
    
    /* Row and Column Spacing */
    .row.g-3 > * {
        padding-right: calc(var(--bs-gutter-x) * .5);
        padding-left: calc(var(--bs-gutter-x) * .5);
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
        background: linear-gradient(135deg, #0051BB, #3FA1DD);
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
@endpush

@section('content')
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.products') }}">Ürünler</a></li>
                            <li class="breadcrumb-item active">{{ $product->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <button class="btn btn-primary btn-icon-split" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                        <span class="icon"><i class="bi bi-pencil-square"></i></span>
                        <span class="text">Düzenle</span>
                    </button>
                    <button class="btn btn-secondary ms-2" onclick="window.history.back()">
                        <i class="bi bi-arrow-left"></i> Geri
                    </button>
                </div>
            </div>
        </div>

        <!-- Product Content -->  
        <div class="product-wrap">
                <div class="row">
                    <div class="col-lg-5 col-md-12 mb-4">
                        <!-- Görsel Galerisi Kartı -->
                        <div class="product-gallery p-4 h-100">
                            <!-- Kart Başlığı -->
                            <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="bi bi-images text-primary me-2"></i>Ürün Görselleri
                                </h5>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-camera me-1"></i>
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
                    
                    <div class="col-lg-7 col-md-12 mb-4">
                        <!-- Ürün Bilgileri Kartı -->
                        <div class="product-info-card">
                            <!-- Ürün Başlığı ve Durum -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="product-name mb-0">{{ $product->name }}</h2>
                                    @if($product->status)
                                        <span class="badge bg-success-subtle text-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Pasif</span>
                                    @endif
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                                                <i class="bi bi-pencil me-2"></i> Düzenle
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('toggle-status-form').submit();">
                                                @if($product->status)
                                                    <i class="bi bi-eye-slash me-2"></i> Devre Dışı Bırak
                                                @else
                                                    <i class="bi bi-eye me-2"></i> Aktifleştir
                                                @endif
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" onclick="confirmDelete(event)">
                                                <i class="bi bi-trash me-2"></i> Sil
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Fiyat ve Stok Bilgileri -->
                            <div class="product-stats mt-4">
                                <div class="row">
                                    <div class="col-md-4 col-6">
                                        <div class="product-stat">
                                            <div class="stat-icon price-icon">
                                                <i class="bi bi-tag"></i>
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
                                                <i class="bi bi-box-seam"></i>
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
                                                <i class="bi bi-calendar-check"></i>
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
                            <div class="product-meta mt-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="product-meta-item">
                                            <div class="meta-label"><i class="bi bi-folder2-open text-primary me-2"></i>Kategori</div>
                                            <div class="meta-value">
                                                @if($product->category)
                                                    <span class="badge bg-primary">{{ $product->category->name }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Kategorisiz</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="product-meta-item">
                                            <div class="meta-label"><i class="bi bi-upc-scan text-primary me-2"></i>SKU</div>
                                            <div class="meta-value">{{ $product->sku ?? 'Belirtilmemiş' }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="product-meta-item">
                                            <div class="meta-label"><i class="bi bi-clock-history text-primary me-2"></i>Oluşturulma</div>
                                            <div class="meta-value">{{ $product->created_at->format('d.m.Y H:i') }}</div>
                                        </div>
                                        
                                        <div class="product-meta-item">
                                            <div class="meta-label"><i class="bi bi-arrow-repeat text-primary me-2"></i>Güncelleme</div>
                                            <div class="meta-value">{{ $product->updated_at->format('d.m.Y H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ürün Açıklaması -->
                            <div class="product-description mt-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-file-text text-primary me-2"></i>Ürün Açıklaması
                                </h5>
                                <div class="p-3 bg-white border rounded-3">
                                    <p class="mb-0">{{ $product->description ?: 'Bu ürün için açıklama bulunmamaktadır.' }}</p>
                                </div>
                            </div>
                            
                            <!-- Aksiyon Butonları -->
                            <div class="action-buttons mt-4 pt-3 border-top">
                                <div class="row">
                                    <div class="col-md-4 col-6">
                                        <button class="btn btn-primary w-100 btn-action btn-edit" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}">
                                            <i class="bi bi-pencil-square me-2"></i>Düzenle
                                        </button>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <button class="btn btn-outline-primary w-100 btn-action btn-toggle"
                                            onclick="event.preventDefault(); document.getElementById('toggle-status-form').submit();">
                                            @if($product->status)
                                                <i class="bi bi-eye-slash me-2"></i>Devre Dışı Bırak
                                            @else
                                                <i class="bi bi-eye me-2"></i>Aktifleştir
                                            @endif
                                        </button>
                                        <form id="toggle-status-form" action="{{ route('admin.product.toggleStatus', $product->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </div>
                                    <div class="col-md-4 col-12 mt-md-0 mt-2">
                                        <button class="btn btn-danger w-100 btn-action btn-delete" onclick="confirmDelete(event)">
                                            <i class="bi bi-trash me-2"></i>Sil
                                        </button>
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
                        <div class="product-gallery p-3">
                            <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-3">
                                <h5 class="fw-bold mb-0">
                                    <i class="bi bi-list-ul text-primary me-2"></i>Ürün Varyantları
                                </h5>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-box me-1"></i>
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
    <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i>
                        Ürün Düzenle: <span class="badge bg-light text-dark">{{ $product->name }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="deleted_images" id="deletedImages{{ $product->id }}" value="">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Temel Bilgiler -->
                            <div class="col-12">
                                <h6 class="text-muted mb-3">Temel Bilgiler</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ürün Adı</label>
                                <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <select name="category_id" class="form-control" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Fiyat ve Stok -->
                            <div class="col-12 mt-4">
                                <h6 class="text-muted mb-3">Fiyat ve Stok Bilgileri</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fiyat (₺)</label>
                                <input type="number" name="price" class="form-control" step="0.01" value="{{ $product->price }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stok Miktarı</label>
                                <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                            </div>
                            
                            <!-- Açıklama -->
                            <div class="col-12 mt-4">
                                <h6 class="text-muted mb-3">Ürün Açıklaması</h6>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Açıklama</label>
                                <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                            </div>
                            
                            <!-- Görseller -->
                            <div class="col-12 mt-4">
                                <h6 class="text-muted mb-3">Ürün Görselleri</h6>
                            </div>
                            @if($product->images && $product->images->isNotEmpty())
                            <div class="col-12">
                                <label class="form-label">Mevcut Görseller</label>
                                <div class="current-images">
                                    @foreach($product->images as $image)
                                        <div class="current-image-item" data-image-id="{{ $image->id }}">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Ürün görseli">
                                            <div class="image-overlay">
                                                <button type="button" class="btn-remove-image" onclick="removeImage(this, '{{ $product->id }}', '{{ $image->id }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <label class="form-label">Yeni Görseller</label>
                                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                <small class="text-muted">Birden fazla görsel seçebilirsiniz</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Durum</label>
                                <select name="status" class="form-control" required>
                                    <option value="1" {{ $product->status ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$product->status ? 'selected' : '' }}>Pasif</option>
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
@endsection

@push('scripts')
<script>
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
        
        // Tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Açılışta animasyonları ekle
        $('.product-gallery, .product-info-card').addClass('animated fadeIn');
    });
    
    // Ürün silme onay işlevi
    function confirmDelete(event) {
        event.preventDefault();
        if (confirm('Bu ürünü silmek istediğinize emin misiniz? Bu işlem geri alınamaz.')) {
            document.getElementById('delete-product-form').submit();
        }
    }
    
    // Görsel silme fonksiyonu
    function removeImage(button, productId, imageId) {
        // Görsel kartını gizle
        const imageItem = button.closest('.current-image-item');
        imageItem.style.display = 'none';
        
        // Silinecek görsel ID'sini hidden input'a ekle
        const deletedImagesInput = document.getElementById('deletedImages' + productId);
        const currentValue = deletedImagesInput.value;
        const deletedImages = currentValue ? currentValue.split(',') : [];
        deletedImages.push(imageId);
        deletedImagesInput.value = deletedImages.join(',');
    }
</script>
@endpush
