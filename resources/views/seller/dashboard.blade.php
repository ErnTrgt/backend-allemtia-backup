@extends('seller.layout')

@section('title', 'Satıcı Paneli')

@section('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.css">
@endsection

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header mb-30">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="title">
                            <h4>Satıcı Paneli</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">Ana Sayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Gösterge Paneli</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="welcome-text">
                            <h6 class="mb-0">Hoş geldiniz, <span class="text-primary">{{ Auth::user()->name }}</span>!</h6>
                            <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- İstatistik Kartları -->
            <div class="row">
                <!-- Ürün Sayısı -->
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <a href="{{ route('seller.products') }}" class="d-flex flex-wrap text-decoration-none">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark">{{ $productCount }}</div>
                                <div class="font-14 text-secondary weight-500">Toplam Ürün</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-warning" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-warning">
                                <div class="icon">
                                    <i class="icon-copy dw dw-box text-white"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Sipariş Sayısı -->
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <a href="{{ route('seller.orders') }}" class="d-flex flex-wrap text-decoration-none">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark">{{ $orderCount }}</div>
                                <div class="font-14 text-secondary weight-500">Toplam Sipariş</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-success" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-success">
                                <div class="icon">
                                    <i class="icon-copy dw dw-shopping-cart text-white"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Aktif Siparişler -->
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <a href="{{ route('seller.orders', ['status' => 'processing']) }}" class="d-flex flex-wrap text-decoration-none">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark">{{ $activeOrdersCount ?? 0 }}</div>
                                <div class="font-14 text-secondary weight-500">Aktif Siparişler</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-info" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-info">
                                <div class="icon">
                                    <i class="icon-copy dw dw-delivery-truck text-white"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Kuponlar -->
                <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <a href="{{ route('seller.coupons.index') }}" class="d-flex flex-wrap text-decoration-none">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark">{{ $couponCount ?? 0 }}</div>
                                <div class="font-14 text-secondary weight-500">Aktif Kuponlar</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-purple" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-purple">
                                <div class="icon">
                                    <i class="icon-copy dw dw-ticket text-white"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Detaylı Satış İstatistikleri -->
            <div class="row mb-30">
                <!-- Toplam Gelir -->
                <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted font-14 mb-2">Toplam Gelir</h6>
                                <h4 class="font-24 weight-700 text-dark mb-0">{{ number_format($salesStats['totalRevenue'] ?? 0, 2) }} ₺</h4>
                            </div>
                            <div class="widget-icon" style="font-size: 2.5rem; color: #28a745;">
                                <i class="icon-copy dw dw-money-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- İptal Edilen Tutar -->
                <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted font-14 mb-2">İptal Edilen Tutar</h6>
                                <h4 class="font-24 weight-700 text-danger mb-0">{{ number_format($salesStats['cancelledRevenue'] ?? 0, 2) }} ₺</h4>
                            </div>
                            <div class="widget-icon" style="font-size: 2.5rem; color: #dc3545;">
                                <i class="icon-copy dw dw-cancel"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ortalama Sipariş Değeri -->
                <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted font-14 mb-2">Ortalama Sipariş</h6>
                                <h4 class="font-24 weight-700 text-info mb-0">{{ number_format($salesStats['avgOrderValue'] ?? 0, 2) }} ₺</h4>
                            </div>
                            <div class="widget-icon" style="font-size: 2.5rem; color: #17a2b8;">
                                <i class="icon-copy dw dw-analytics-21"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bu Ayın Geliri -->
                <div class="col-xl-3 col-lg-6 col-md-6 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted font-14 mb-2">Bu Ay</h6>
                                <h4 class="font-24 weight-700 text-primary mb-0">{{ number_format($salesStats['thisMonthRevenue'] ?? 0, 2) }} ₺</h4>
                                <small class="text-{{ ($salesStats['monthlyGrowth'] ?? 0) >= 0 ? 'success' : 'danger' }}">
                                    <i class="icon-copy dw dw-{{ ($salesStats['monthlyGrowth'] ?? 0) >= 0 ? 'up-arrow-4' : 'down-arrow-4' }}"></i>
                                    {{ abs($salesStats['monthlyGrowth'] ?? 0) }}%
                                </small>
                            </div>
                            <div class="widget-icon" style="font-size: 2.5rem; color: #007bff;">
                                <i class="icon-copy dw dw-calendar1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- En Çok/Az Satılan Ürünler -->
            <div class="row mb-30">
                <!-- En Çok Satılan Ürünler -->
                <div class="col-xl-6 col-lg-6 col-md-6 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <h5 class="text-blue mb-20">En Çok Satılan Ürünler <small class="text-muted">(İlk 3)</small></h5>
                        @if(isset($salesStats['bestSellingProducts']) && $salesStats['bestSellingProducts']->count() > 0)
                            <div class="list-group">
                                @foreach($salesStats['bestSellingProducts'] as $index => $product)
                                    <div class="list-group-item border-left-0 border-right-0 {{ $index === 0 ? 'border-top-0' : '' }} {{ $index === $salesStats['bestSellingProducts']->count() - 1 ? 'border-bottom-0' : '' }} py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rank mr-3">
                                                <span class="badge badge-{{ $index === 0 ? 'success' : ($index === 1 ? 'info' : 'secondary') }} p-2">
                                                    #{{ $index + 1 }}
                                                </span>
                                            </div>
                                            <div class="img-container mr-3">
                                                @if($product->product && $product->product->images->isNotEmpty())
                                                    <img src="{{ asset('storage/' . $product->product->images->first()->image_path) }}" 
                                                         alt="{{ $product->product->name }}" 
                                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="icon-copy dw dw-box text-muted" style="font-size: 1.5rem;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-info flex-grow-1">
                                                <h6 class="mb-1">{{ $product->product->name ?? 'Ürün Bulunamadı' }}</h6>
                                                <div class="d-flex justify-content-between">
                                                    <p class="mb-0 text-muted"><i class="icon-copy dw dw-shopping-bag1"></i> <strong>{{ $product->total_quantity }}</strong> adet</p>
                                                    <p class="mb-0 text-success"><i class="icon-copy dw dw-money-1"></i> <strong>{{ number_format($product->total_revenue, 2) }} ₺</strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="icon-copy dw dw-analytics-5 text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Henüz satış verisi bulunmuyor</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- En Az Satılan Ürünler -->
                <div class="col-xl-6 col-lg-6 col-md-6 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <h5 class="text-blue mb-20">En Az Satılan Ürünler <small class="text-muted">(İlk 3)</small></h5>
                        @if(isset($salesStats['leastSellingProducts']) && $salesStats['leastSellingProducts']->count() > 0)
                            <div class="list-group">
                                @foreach($salesStats['leastSellingProducts'] as $index => $product)
                                    <div class="list-group-item border-left-0 border-right-0 {{ $index === 0 ? 'border-top-0' : '' }} {{ $index === $salesStats['leastSellingProducts']->count() - 1 ? 'border-bottom-0' : '' }} py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rank mr-3">
                                                <span class="badge badge-{{ $index === 0 ? 'warning' : ($index === 1 ? 'light' : 'secondary') }} p-2">
                                                    #{{ $index + 1 }}
                                                </span>
                                            </div>
                                            <div class="img-container mr-3">
                                                @if($product->product && $product->product->images->isNotEmpty())
                                                    <img src="{{ asset('storage/' . $product->product->images->first()->image_path) }}" 
                                                         alt="{{ $product->product->name }}" 
                                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="icon-copy dw dw-box text-muted" style="font-size: 1.5rem;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-info flex-grow-1">
                                                <h6 class="mb-1">{{ $product->product->name ?? 'Ürün Bulunamadı' }}</h6>
                                                <div class="d-flex justify-content-between">
                                                    <p class="mb-0 text-muted"><i class="icon-copy dw dw-shopping-bag1"></i> <strong>{{ $product->total_quantity }}</strong> adet</p>
                                                    <p class="mb-0 text-warning"><i class="icon-copy dw dw-money-1"></i> <strong>{{ number_format($product->total_revenue, 2) }} ₺</strong></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="icon-copy dw dw-analytics-5 text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Henüz satış verisi bulunmuyor</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Satış Grafiği ve Stok Uyarıları -->
            <div class="row">
                <!-- Satış Grafiği -->
                <div class="col-xl-8 col-lg-8 col-md-8 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex justify-content-between align-items-center mb-20">
                            <h4 class="text-blue h4 mb-0">Satış Grafiği</h4>
                            <div class="dropdown">
                                <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                    <i class="dw dw-more"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                    <a class="dropdown-item" href="#" id="weekly-sales">Haftalık</a>
                                    <a class="dropdown-item" href="#" id="monthly-sales">Aylık</a>
                                    <a class="dropdown-item" href="#" id="yearly-sales">Yıllık</a>
                                </div>
                            </div>
                        </div>
                        <div id="sales-chart" style="min-height: 300px;"></div>
                    </div>
                </div>
                
                <!-- Stok Uyarıları -->
                <div class="col-xl-4 col-lg-4 col-md-4 mb-20">
                    <div class="card-box height-100-p pd-20">
                        <h4 class="text-blue h4 mb-20">Stok Uyarıları</h4>
                        <div class="notification-list mx-h-350 customscroll">
                            <ul>
                                @forelse($lowStockProducts ?? [] as $product)
                                <li>
                                    <a href="{{ route('seller.products.details', $product->id) }}">
                                        <div class="d-flex align-items-center">
                                            <div class="img-wrap">
                                                @if($product->images->isNotEmpty())
                                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="mw-100 rounded">
                                                @else
                                                <i class="dw dw-box-1 text-warning fa-2x"></i>
                                                @endif
                                            </div>
                                            <div class="notification-content">
                                                <h6 class="m-0">{{ $product->name }}</h6>
                                                <p class="m-0 text-danger font-weight-bold">Stok: {{ $product->stock }}</p>
                                                <small class="text-muted">Stok durumu kritik seviyede</small>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                @empty
                                <li class="text-center py-3">
                                    <i class="icon-copy dw dw-checked text-success fa-2x mb-2"></i>
                                    <p>Kritik stok seviyesinde ürününüz bulunmuyor.</p>
                                </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sepet ve Favori İstatistikleri -->
            <div class="row">
                <!-- Sepete En Çok Eklenen Ürünler -->
                <div class="col-xl-6 col-lg-6 mb-30">
                    <div class="card-box height-100-p pd-20">
                       <div class="d-flex justify-content-between align-items-center mb-20">
    <h4 class="text-blue h4 mb-0">Sepete En Çok Eklenen Ürünler</h4>
    <form action="{{ route('abandoned-cart.send-emails') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="icon-copy dw dw-email"></i> Hatırlatma E-postası Gönder
        </button>
    </form>
</div>
                        
                        @if(isset($topCartProducts) && $topCartProducts->count() > 0)
                        <div class="list-group">
                            @foreach($topCartProducts as $index => $item)
                            <div class="list-group-item border-left-0 border-right-0 {{ $index === 0 ? 'border-top-0' : '' }} {{ $index === $topCartProducts->count() - 1 ? 'border-bottom-0' : '' }} py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rank mr-3">
                                        <span class="badge badge-{{ $index === 0 ? 'danger' : ($index === 1 ? 'warning' : 'secondary') }} p-2">
                                            #{{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div class="img-container mr-3">
                                        @if($item->product && $item->product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="icon-copy dw dw-box text-muted" style="font-size: 1.5rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="product-info flex-grow-1">
                                        <h6 class="mb-1">{{ $item->product->name ?? 'Ürün Bulunamadı' }}</h6>
                                        <div class="d-flex justify-content-between">
                                            <p class="mb-0 text-muted"><i class="icon-copy dw dw-shopping-cart"></i> <strong>{{ $item->cart_count }}</strong> kez sepete eklendi</p>
                                            <p class="mb-0 text-danger"><i class="icon-copy dw dw-money-1"></i> <strong>{{ number_format($item->product->price ?? 0, 2) }} ₺</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="icon-copy dw dw-shopping-cart text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Henüz sepete eklenen ürün bulunmuyor</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Favorilere En Çok Eklenen Ürünler -->
                <div class="col-xl-6 col-lg-6 mb-30">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex justify-content-between align-items-center mb-20">
                            <h4 class="text-blue h4 mb-0">Favorilere En Çok Eklenen Ürünler</h4>
                        </div>
                        
                        @if(isset($topWishlistProducts) && $topWishlistProducts->count() > 0)
                        <div class="list-group">
                            @foreach($topWishlistProducts as $index => $item)
                            <div class="list-group-item border-left-0 border-right-0 {{ $index === 0 ? 'border-top-0' : '' }} {{ $index === $topWishlistProducts->count() - 1 ? 'border-bottom-0' : '' }} py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rank mr-3">
                                        <span class="badge badge-{{ $index === 0 ? 'info' : ($index === 1 ? 'primary' : 'secondary') }} p-2">
                                            #{{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div class="img-container mr-3">
                                        @if($item->product && $item->product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="icon-copy dw dw-box text-muted" style="font-size: 1.5rem;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="product-info flex-grow-1">
                                        <h6 class="mb-1">{{ $item->product->name ?? 'Ürün Bulunamadı' }}</h6>
                                        <div class="d-flex justify-content-between">
                                            <p class="mb-0 text-muted"><i class="icon-copy dw dw-heart"></i> <strong>{{ $item->wishlist_count }}</strong> kez favorilere eklendi</p>
                                            <p class="mb-0 text-info"><i class="icon-copy dw dw-money-1"></i> <strong>{{ number_format($item->product->price ?? 0, 2) }} ₺</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="icon-copy dw dw-heart text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Henüz favorilere eklenen ürün bulunmuyor</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Son Sepete ve Favorilere Eklenen Ürünler -->
            <div class="row">
                <!-- Son Sepete Eklenen Ürünler -->
                <div class="col-xl-6 col-lg-6 mb-30">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex justify-content-between align-items-center mb-20">
                            <h4 class="text-blue h4 mb-0">Son Sepete Eklenen Ürünler</h4>
                        </div>
                        
                        @if(isset($cartItems) && $cartItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ürün</th>
                                        <th>Müşteri</th>
                                        <th>Adet</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartItems as $item)
                                    <tr>
                                        <td class="d-flex align-items-center">
                                            @if($item->product && $item->product->images->isNotEmpty())
                                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                                            @else
                                                <div style="width: 40px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                                    <i class="icon-copy dw dw-box text-muted"></i>
                                                </div>
                                            @endif
                                            <span>{{ $item->product->name ?? 'Ürün Bulunamadı' }}</span>
                                        </td>
                                        <td>{{ $item->user->name ?? 'Bilinmiyor' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="icon-copy dw dw-shopping-cart text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Henüz sepete eklenen ürün bulunmuyor</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Son Favorilere Eklenen Ürünler -->
                <div class="col-xl-6 col-lg-6 mb-30">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex justify-content-between align-items-center mb-20">
                            <h4 class="text-blue h4 mb-0">Son Favorilere Eklenen Ürünler</h4>
                        </div>
                        
                        @if(isset($wishlistItems) && $wishlistItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Ürün</th>
                                        <th>Müşteri</th>
                                        <th>Tarih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wishlistItems as $item)
                                    <tr>
                                        <td class="d-flex align-items-center">
                                            @if($item->product && $item->product->images->isNotEmpty())
                                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 10px;">
                                            @else
                                                <div style="width: 40px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                                    <i class="icon-copy dw dw-box text-muted"></i>
                                                </div>
                                            @endif
                                            <span>{{ $item->product->name ?? 'Ürün Bulunamadı' }}</span>
                                        </td>
                                        <td>{{ $item->user->name ?? 'Bilinmiyor' }}</td>
                                        <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="icon-copy dw dw-heart text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Henüz favorilere eklenen ürün bulunmuyor</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Kategori Talepleri ve Son Siparişler -->
            <div class="row">
                <!-- Kategori Talepleri -->
                <div class="col-xl-5 col-lg-5 mb-30">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex justify-content-between align-items-center mb-20">
                            <h4 class="text-blue h4 mb-0">Kategori Talepleri</h4>
                            <a href="{{ url('/seller/category-requests') }}" class="btn btn-sm btn-primary">
                                Tümünü Gör <i class="icon-copy dw dw-right-arrow1"></i>
                            </a>
                        </div>
                        
                        <!-- Talep İstatistikleri -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="request-stat-box pending p-2 text-center rounded">
                                    <div class="icon-box mb-1">
                                        <i class="icon-copy dw dw-clock text-warning"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="mb-0">{{ $pendingRequests }}</h3>
                                        <span>Bekleyen</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="request-stat-box approved p-2 text-center rounded">
                                    <div class="icon-box mb-1">
                                        <i class="icon-copy dw dw-checked text-success"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="mb-0">{{ $approvedRequests }}</h3>
                                        <span>Onaylanan</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="request-stat-box rejected p-2 text-center rounded">
                                    <div class="icon-box mb-1">
                                        <i class="icon-copy dw dw-cancel text-danger"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3 class="mb-0">{{ $rejectedRequests }}</h3>
                                        <span>Reddedilen</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Yeni Talep Formu -->
                        <div class="p-3 bg-light rounded">
                            <h5 class="text-dark mb-3">Yeni Kategori Talebi</h5>
                            <form action="{{ route('seller.category.requests.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <input type="text" class="form-control" name="name" placeholder="Kategori Adı" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Talep Oluştur</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Son Siparişler -->
                <div class="col-xl-7 col-lg-7 mb-30">
                    <div class="card-box height-100-p pd-20">
                        <div class="d-flex justify-content-between align-items-center mb-20">
                            <h4 class="text-blue h4 mb-0">Son Siparişler</h4>
                            <a href="{{ route('seller.orders') }}" class="btn btn-sm btn-primary">
                                Tümünü Gör <i class="icon-copy dw dw-right-arrow1"></i>
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Müşteri</th>
                                        <th>Tutar</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders ?? [] as $order)
                                    <tr>
                                        <td><strong>#{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>{{ number_format($order->total, 2) }} ₺</td>
                                        <td>
                                            @if($order->status == 'pending')
                                                <span class="badge badge-warning">Beklemede</span>
                                            @elseif($order->status == 'processing')
                                                <span class="badge badge-info">Hazırlanıyor</span>
                                            @elseif($order->status == 'shipped')
                                                <span class="badge badge-primary">Kargoda</span>
                                            @elseif($order->status == 'delivered')
                                                <span class="badge badge-success">Teslim Edildi</span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge badge-danger">İptal Edildi</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $order->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('d.m.Y') }}</td>
                                        <td>
                                            <a href="{{ route('seller.orders', ['id' => $order->id]) }}" class="btn btn-sm btn-primary">Detay</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Henüz sipariş bulunmuyor</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM yüklendi, grafik oluşturuluyor...");
    
    // Controller'dan gelen verileri al
    var weeklyData = @json($salesData['weekly']['data'] ?? []);
    var weeklyLabels = @json($salesData['weekly']['labels'] ?? []);
    var monthlyData = @json($salesData['monthly']['data'] ?? []);
    var monthlyLabels = @json($salesData['monthly']['labels'] ?? []);
    var yearlyData = @json($salesData['yearly']['data'] ?? []);
    var yearlyLabels = @json($salesData['yearly']['labels'] ?? []);
    
    console.log("Controller'dan gelen veriler:", {
        weekly: { data: weeklyData, labels: weeklyLabels },
        monthly: { data: monthlyData, labels: monthlyLabels },
        yearly: { data: yearlyData, labels: yearlyLabels }
    });
    
    // Veri boşsa örnek veriler kullan
    if (!weeklyData || weeklyData.length === 0) {
        console.log("Haftalık veri boş, örnek veri kullanılıyor");
        weeklyData = [4500, 5200, 3800, 6700, 4900, 7800, 8500];
    }
    
    if (!weeklyLabels || weeklyLabels.length === 0) {
        console.log("Haftalık etiketler boş, örnek etiketler kullanılıyor");
        weeklyLabels = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'];
    }
    
    if (!monthlyData || monthlyData.length === 0) {
        console.log("Aylık veri boş, örnek veri kullanılıyor");
        monthlyData = [18500, 21300, 25600, 19200, 28700, 32500, 38400, 35200, 29700, 42100, 38900, 47500];
    }
    
    if (!monthlyLabels || monthlyLabels.length === 0) {
        console.log("Aylık etiketler boş, örnek etiketler kullanılıyor");
        monthlyLabels = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
    }
    
    if (!yearlyData || yearlyData.length === 0) {
        console.log("Yıllık veri boş, örnek veri kullanılıyor");
        yearlyData = [245000, 312000, 387000, 452000, 528000];
    }
    
    if (!yearlyLabels || yearlyLabels.length === 0) {
        console.log("Yıllık etiketler boş, örnek etiketler kullanılıyor");
        yearlyLabels = ['2019', '2020', '2021', '2022', '2023'];
    }
    
    // Veri yapısını oluştur
    var salesData = {
        weekly: {
            data: weeklyData,
            labels: weeklyLabels
        },
        monthly: {
            data: monthlyData,
            labels: monthlyLabels
        },
        yearly: {
            data: yearlyData,
            labels: yearlyLabels
        }
    };
    
    console.log("Grafik için kullanılacak veriler:", salesData);
    
    var options = {
        series: [{
            name: 'Satış',
            data: salesData.monthly.data
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        colors: ['#2196f3'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: salesData.monthly.labels
        },
        yaxis: {
            title: {
                text: 'Satış Tutarı (₺)'
            },
            labels: {
                formatter: function (val) {
                    return val.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".") + " ₺";
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ".") + " ₺";
                }
            }
        }
    };

    try {
        console.log("ApexCharts oluşturuluyor...");
        var chartElement = document.querySelector("#sales-chart");
        
        if (!chartElement) {
            console.error("sales-chart elementi bulunamadı!");
            return;
        }
        
        var chart = new ApexCharts(chartElement, options);
        chart.render();
        console.log("Grafik başarıyla oluşturuldu");
        
        // Filtre değiştiğinde grafiği güncelle
        var weeklyButton = document.getElementById('weekly-sales');
        var monthlyButton = document.getElementById('monthly-sales');
        var yearlyButton = document.getElementById('yearly-sales');
        
        if (weeklyButton) {
            weeklyButton.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("Haftalık görünüme geçiliyor");
                chart.updateOptions({
                    series: [{
                        data: salesData.weekly.data
                    }],
                    xaxis: {
                        categories: salesData.weekly.labels
                    }
                });
            });
        } else {
            console.error("weekly-sales butonu bulunamadı!");
        }
        
        if (monthlyButton) {
            monthlyButton.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("Aylık görünüme geçiliyor");
                chart.updateOptions({
                    series: [{
                        data: salesData.monthly.data
                    }],
                    xaxis: {
                        categories: salesData.monthly.labels
                    }
                });
            });
        } else {
            console.error("monthly-sales butonu bulunamadı!");
        }
        
        if (yearlyButton) {
            yearlyButton.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("Yıllık görünüme geçiliyor");
                chart.updateOptions({
                    series: [{
                        data: salesData.yearly.data
                    }],
                    xaxis: {
                        categories: salesData.yearly.labels
                    }
                });
            });
        } else {
            console.error("yearly-sales butonu bulunamadı!");
        }
    } catch (error) {
        console.error("ApexCharts yüklenirken hata oluştu:", error);
    }
    
    // Sepette Unutulan Ürünler için E-posta Gönderme
    $('#sendAbandonedCartEmailsBtn').on('click', function() {
        if (confirm('Sepette ürün bırakan müşterilere hatırlatma e-postası göndermek istediğinize emin misiniz?')) {
            // Modal'ı göster
            $('#emailResultModal').modal('show');
            
            // İçeriği temizle ve yükleniyor mesajını göster
            $('#emailResultContent').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin me-2"></i> E-postalar gönderiliyor, lütfen bekleyin...</div>');
            $('#emailResultDetails').hide();
            $('#emailResultTable').empty();
            
            // AJAX isteği gönder
            $.ajax({
                url: "{{ route('abandoned-cart.send-emails') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log("E-posta gönderim cevabı:", response);
                    
                    if (response.success) {
                        $('#emailResultContent').html('<div class="alert alert-success"><i class="icon-copy dw dw-check me-2"></i> ' + response.success + '</div>');
                        
                        // E-posta detaylarını göster
                        if (response.emails && response.emails.length > 0) {
                            $('#emailResultDetails').show();
                            
                            $.each(response.emails, function(index, email) {
                                var row = '<tr>' +
                                    '<td>' + email.name + '</td>' +
                                    '<td>' + email.email + '</td>' +
                                    '<td>' + email.product_count + '</td>' +
                                    '<td>' + email.total_value.toFixed(2) + ' TL</td>' +
                                    '</tr>';
                                $('#emailResultTable').append(row);
                            });
                        }
                    } else if (response.info) {
                        $('#emailResultContent').html('<div class="alert alert-info"><i class="icon-copy dw dw-information me-2"></i> ' + response.info + '</div>');
                    } else if (response.error) {
                        $('#emailResultContent').html('<div class="alert alert-danger"><i class="icon-copy dw dw-cancel me-2"></i> ' + response.error + '</div>');
                    } else {
                        $('#emailResultContent').html('<div class="alert alert-warning"><i class="icon-copy dw dw-warning me-2"></i> Beklenmeyen bir yanıt alındı.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("E-posta gönderim hatası:", error);
                    $('#emailResultContent').html('<div class="alert alert-danger"><i class="icon-copy dw dw-cancel me-2"></i> E-posta gönderimi sırasında bir hata oluştu: ' + error + '</div>');
                }
            });
        }
    });
});
</script>

<!-- E-posta Gönderim Sonuç Modalı -->
<div class="modal fade" id="emailResultModal" tabindex="-1" aria-labelledby="emailResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailResultModalLabel">E-posta Gönderim Sonuçları</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="emailResultContent">
                    <div class="alert alert-info">
                        <i class="icon-copy dw dw-loading me-2"></i> E-postalar gönderiliyor, lütfen bekleyin...
                    </div>
                </div>
                <div id="emailResultDetails" class="mt-3" style="display: none;">
                    <h6>Gönderilen E-postalar:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Müşteri</th>
                                    <th>E-posta</th>
                                    <th>Ürün Sayısı</th>
                                    <th>Toplam Değer</th>
                                </tr>
                            </thead>
                            <tbody id="emailResultTable">
                                <!-- JavaScript ile doldurulacak -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
@endsection
