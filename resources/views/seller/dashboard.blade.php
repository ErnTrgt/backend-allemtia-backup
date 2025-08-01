@extends('seller.layout')

@section('title', 'Gösterge Paneli')

@push('styles')
<link rel="stylesheet" href="{{ asset('seller/css/dashboard.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.css">
@endpush

@section('content')
<div class="dashboard-page">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <div class="dashboard-title">
            <h1>Gösterge Paneli</h1>
            <nav class="dashboard-breadcrumb">
                <a href="{{ route('seller.dashboard') }}">Ana Sayfa</a>
                <i class="bi bi-chevron-right"></i>
                <span>Gösterge Paneli</span>
            </nav>
        </div>
        <div class="welcome-box">
            <div class="welcome-text">
                <i class="bi bi-hand-wave welcome-icon"></i>
                Hoş geldiniz, <span class="user-name">{{ Auth::user()->name }}</span>!
            </div>
            <div class="date-text" data-date="{{ now()->timezone('Europe/Istanbul')->format('Y-m-d') }}">
                <i class="bi bi-calendar3 date-icon"></i>
                <span class="date-full">{{ now()->timezone('Europe/Istanbul')->locale('tr')->isoFormat('dddd, D MMMM YYYY') }}</span>
                <span class="date-time">
                    <i class="bi bi-clock time-icon"></i>
                    {{ now()->timezone('Europe/Istanbul')->format('H:i') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Stat Cards Row -->
    <div class="stat-cards-container">
        <div class="stat-cards-header">
            <h5 class="stat-cards-title">Genel İstatistikler</h5>
            <button class="btn-refresh-stats" onclick="refreshStatCards()" title="İstatistikleri Yenile">
                <i class="bi bi-arrow-clockwise refresh-icon"></i>
                <span class="refresh-text">Yenile</span>
            </button>
        </div>
        <div class="row mb-4" id="statCardsRow">
        <!-- Products Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <a href="{{ route('seller.products') }}" class="stat-card products">
                <div class="stat-content">
                    <div class="stat-info">
                        <h2>{{ $productCount }}</h2>
                        <p>Toplam Ürün</p>
                        <div class="stat-progress">
                            <div class="stat-progress-bar" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Orders Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <a href="{{ route('seller.orders') }}" class="stat-card orders">
                <div class="stat-content">
                    <div class="stat-info">
                        <h2>{{ $orderCount }}</h2>
                        <p>Toplam Sipariş</p>
                        <div class="stat-progress">
                            <div class="stat-progress-bar" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Active Orders Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <a href="{{ route('seller.orders', ['status' => 'processing']) }}" class="stat-card active-orders">
                <div class="stat-content">
                    <div class="stat-info">
                        <h2>{{ $activeOrdersCount ?? 0 }}</h2>
                        <p>Aktif Siparişler</p>
                        <div class="stat-progress">
                            <div class="stat-progress-bar" style="width: {{ $orderCount > 0 ? ($activeOrdersCount / $orderCount) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Coupons Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <a href="{{ route('seller.coupons.index') }}" class="stat-card coupons">
                <div class="stat-content">
                    <div class="stat-info">
                        <h2>{{ $couponCount ?? 0 }}</h2>
                        <p>Aktif Kuponlar</p>
                        <div class="stat-progress">
                            <div class="stat-progress-bar" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
    </div>

    <!-- Revenue Stats Row -->
    <div class="row">
        <!-- Total Revenue -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="revenue-card">
                <div class="revenue-content">
                    <div class="revenue-header">
                        <div class="revenue-info">
                            <h3>Toplam Gelir</h3>
                            <div class="amount">{{ number_format($salesStats['totalRevenue'] ?? 0, 2) }} ₺</div>
                        </div>
                        <div class="revenue-icon" style="color: #10B981;">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancelled Revenue -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="revenue-card">
                <div class="revenue-content">
                    <div class="revenue-header">
                        <div class="revenue-info">
                            <h3>İptal Edilen</h3>
                            <div class="amount">{{ number_format($salesStats['cancelledRevenue'] ?? 0, 2) }} ₺</div>
                        </div>
                        <div class="revenue-icon" style="color: #EF4444;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Order -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="revenue-card">
                <div class="revenue-content">
                    <div class="revenue-header">
                        <div class="revenue-info">
                            <h3>Ortalama Sipariş</h3>
                            <div class="amount">{{ number_format($salesStats['avgOrderValue'] ?? 0, 2) }} ₺</div>
                        </div>
                        <div class="revenue-icon" style="color: #3B82F6;">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="revenue-card">
                <div class="revenue-content">
                    <div class="revenue-header">
                        <div class="revenue-info">
                            <div class="revenue-title-row">
                                <h3>Bu Ay</h3>
                                @if(isset($salesStats['monthlyGrowth']))
                                    <span class="growth-badge {{ $salesStats['monthlyGrowth'] >= 0 ? 'positive' : 'negative' }}">
                                        <i class="bi bi-arrow-{{ $salesStats['monthlyGrowth'] >= 0 ? 'up' : 'down' }}-short"></i>
                                        {{ abs($salesStats['monthlyGrowth']) }}%
                                    </span>
                                @endif
                            </div>
                            <div class="amount">{{ number_format($salesStats['thisMonthRevenue'] ?? 0, 2) }} ₺</div>
                        </div>
                        <div class="revenue-icon" style="color: #8B5CF6;">
                            <i class="bi bi-calendar-month"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Lists Row -->
    <div class="row">
        <!-- Best Selling Products -->
        <div class="col-xl-6 col-lg-6 col-12 mb-4">
            <div class="product-list-card">
                <div class="product-list-header">
                    <h3 class="product-list-title">
                        <i class="bi bi-trophy-fill text-warning"></i>
                        En Çok Satılan Ürünler
                        <span class="product-list-subtitle">(İlk 3)</span>
                    </h3>
                </div>
                @if(isset($salesStats['bestSellingProducts']) && $salesStats['bestSellingProducts']->count() > 0)
                    <div class="product-list">
                        @foreach($salesStats['bestSellingProducts'] as $index => $product)
                            <div class="product-item">
                                <div class="product-rank rank-{{ $index + 1 }}">
                                    @if($index == 0)
                                        <i class="bi bi-trophy-fill"></i>
                                    @elseif($index == 1)
                                        <i class="bi bi-award-fill"></i>
                                    @else
                                        <i class="bi bi-star-fill"></i>
                                    @endif
                                </div>
                                @if($product->product && $product->product->images->isNotEmpty())
                                    <div class="product-image">
                                        <img src="{{ asset('storage/' . $product->product->images->first()->image_path) }}" 
                                             alt="{{ $product->product->name }}">
                                    </div>
                                @else
                                    <div class="product-placeholder">
                                        <i class="bi bi-box"></i>
                                    </div>
                                @endif
                                <div class="product-details">
                                    <h6 class="product-name">{{ $product->product->name ?? 'Ürün Bulunamadı' }}</h6>
                                    <div class="product-meta">
                                        <span class="product-category">{{ $product->product->category->name ?? 'Kategori Yok' }}</span>
                                        <span class="product-stock {{ $product->product->stock < 10 ? 'low-stock' : '' }}">
                                            Stok: {{ $product->product->stock ?? 0 }}
                                        </span>
                                    </div>
                                    <div class="product-stats">
                                        <div class="product-stat">
                                            <i class="bi bi-bag-check"></i>
                                            <strong>{{ $product->total_quantity }}</strong> adet satıldı
                                        </div>
                                        <div class="product-stat revenue">
                                            <i class="bi bi-cash-coin"></i>
                                            <strong>{{ number_format($product->total_revenue, 2) }} ₺</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-bar-chart"></i>
                        <p>Henüz satış verisi bulunmuyor</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Least Selling Products -->
        <div class="col-xl-6 col-lg-6 col-12 mb-4">
            <div class="product-list-card">
                <div class="product-list-header">
                    <h3 class="product-list-title">
                        <i class="bi bi-graph-down text-danger"></i>
                        En Az Satılan Ürünler
                        <span class="product-list-subtitle">(İlk 3)</span>
                    </h3>
                </div>
                @if(isset($salesStats['leastSellingProducts']) && $salesStats['leastSellingProducts']->count() > 0)
                    <div class="product-list">
                        @foreach($salesStats['leastSellingProducts'] as $index => $product)
                            <div class="product-item">
                                <div class="product-rank" style="background: linear-gradient(135deg, #F59E0B, #D97706);">
                                    #{{ $index + 1 }}
                                </div>
                                @if($product->product && $product->product->images->isNotEmpty())
                                    <div class="product-image">
                                        <img src="{{ asset('storage/' . $product->product->images->first()->image_path) }}" 
                                             alt="{{ $product->product->name }}">
                                    </div>
                                @else
                                    <div class="product-placeholder">
                                        <i class="bi bi-box text-muted"></i>
                                    </div>
                                @endif
                                <div class="product-details">
                                    <h6 class="product-name">{{ $product->product->name ?? 'Ürün Bulunamadı' }}</h6>
                                    <div class="product-stats">
                                        <div class="product-stat">
                                            <i class="bi bi-bag-check"></i>
                                            <strong>{{ $product->total_quantity }}</strong> adet
                                        </div>
                                        <div class="product-stat text-warning">
                                            <i class="bi bi-cash"></i>
                                            <strong>{{ number_format($product->total_revenue, 2) }} ₺</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-bar-chart"></i>
                        <p>Henüz satış verisi bulunmuyor</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart and Stock Alerts Row -->
    <div class="row">
        <!-- Sales Chart -->
        <div class="col-xl-8 col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Satış Grafiği</h3>
                    <div class="chart-filters">
                        <button class="chart-filter" id="weekly-sales">Haftalık</button>
                        <button class="chart-filter active" id="monthly-sales">Aylık</button>
                        <button class="chart-filter" id="yearly-sales">Yıllık</button>
                    </div>
                </div>
                <div id="sales-chart" style="min-height: 350px;"></div>
            </div>
        </div>

        <!-- Stock Alerts -->
        <div class="col-xl-4 col-lg-4">
            <div class="alerts-card">
                <div class="product-list-header">
                    <h3 class="product-list-title">Stok Uyarıları</h3>
                </div>
                @if(isset($lowStockProducts) && count($lowStockProducts) > 0)
                    <div class="alerts-list">
                        @foreach($lowStockProducts as $product)
                            <a href="{{ route('seller.products.details', $product->id) }}" class="alert-item">
                                <div class="alert-icon">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <div class="alert-content">
                                    <h6>{{ $product->name }}</h6>
                                    <div class="stock-level">Stok: {{ $product->stock }}</div>
                                    <small>Kritik seviyede</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>Kritik stok seviyesinde ürün yok</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Category Requests and Recent Orders Row -->
    <div class="row">
        <!-- Category Requests -->
        <div class="col-xl-5 col-lg-5">
            <div class="category-card">
                <div class="product-list-header">
                    <h3 class="product-list-title">Kategori Talepleri</h3>
                    <a href="{{ route('seller.category.requests') }}" class="btn btn-sm btn-primary">
                        Tümünü Gör <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                
                <div class="request-stats">
                    <div class="request-stat pending">
                        <i class="bi bi-clock-history"></i>
                        <h3>{{ $pendingRequests }}</h3>
                        <span>Bekleyen</span>
                    </div>
                    <div class="request-stat approved">
                        <i class="bi bi-check-circle"></i>
                        <h3>{{ $approvedRequests }}</h3>
                        <span>Onaylanan</span>
                    </div>
                    <div class="request-stat rejected">
                        <i class="bi bi-x-circle"></i>
                        <h3>{{ $rejectedRequests }}</h3>
                        <span>Reddedilen</span>
                    </div>
                </div>
                
                <div class="request-form">
                    <h5>Yeni Kategori Talebi</h5>
                    <form action="{{ route('seller.category.requests.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Kategori Adı" required>
                        </div>
                        <button type="submit" class="btn-submit">Talep Oluştur</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-xl-7 col-lg-7">
            <div class="orders-card">
                <div class="product-list-header">
                    <h3 class="product-list-title">Son Siparişler</h3>
                    <a href="{{ route('seller.orders') }}" class="btn btn-sm btn-primary">
                        Tümünü Gör <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <table class="orders-table">
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
                                <td><span class="order-number">#{{ $order->order_number }}</span></td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ number_format($order->total, 2) }} ₺</td>
                                <td>
                                    <span class="order-status status-{{ $order->status }}">
                                        @switch($order->status)
                                            @case('pending')
                                                Beklemede
                                                @break
                                            @case('processing')
                                                Hazırlanıyor
                                                @break
                                            @case('shipped')
                                                Kargoda
                                                @break
                                            @case('delivered')
                                                Teslim Edildi
                                                @break
                                            @case('cancelled')
                                                İptal Edildi
                                                @break
                                            @default
                                                {{ $order->status }}
                                        @endswitch
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <a href="{{ route('seller.orders', ['id' => $order->id]) }}" class="btn-detail">Detay</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Henüz sipariş bulunmuyor</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <!-- Mobile Orders View -->
                <div class="mobile-orders">
                    @forelse($recentOrders ?? [] as $order)
                        <div class="mobile-order-card">
                            <div class="mobile-order-header">
                                <div class="mobile-order-info">
                                    <div class="mobile-order-number">#{{ $order->order_number }}</div>
                                    <div class="mobile-order-customer">{{ $order->customer_name }}</div>
                                </div>
                                <span class="order-status status-{{ $order->status }}">
                                    @switch($order->status)
                                        @case('pending')
                                            Beklemede
                                            @break
                                        @case('processing')
                                            Hazırlanıyor
                                            @break
                                        @case('shipped')
                                            Kargoda
                                            @break
                                        @case('delivered')
                                            Teslim Edildi
                                            @break
                                        @case('cancelled')
                                            İptal Edildi
                                            @break
                                        @default
                                            {{ $order->status }}
                                    @endswitch
                                </span>
                            </div>
                            <div class="mobile-order-details">
                                <div class="mobile-order-amount">{{ number_format($order->total, 2) }} ₺</div>
                                <div class="mobile-order-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                            <div class="mobile-order-footer">
                                <a href="{{ route('seller.orders', ['id' => $order->id]) }}" class="btn-detail">
                                    <i class="bi bi-eye"></i> Detay Görüntüle
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-basket"></i>
                            <p>Henüz sipariş bulunmuyor</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Cart and Wishlist Products Row -->
    <div class="row">
        <!-- Top Cart Products -->
        <div class="col-xl-6 col-lg-6">
            <div class="product-list-card">
                <div class="product-list-header">
                    <h3 class="product-list-title">Sepete En Çok Eklenen</h3>
                    <form action="{{ route('abandoned-cart.send-emails') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-envelope"></i> Hatırlatma Gönder
                        </button>
                    </form>
                </div>
                @if(isset($topCartProducts) && $topCartProducts->count() > 0)
                    <div class="product-list">
                        @foreach($topCartProducts as $index => $item)
                            <div class="product-item">
                                <div class="product-rank" style="background: linear-gradient(135deg, #EF4444, #DC2626);">
                                    #{{ $index + 1 }}
                                </div>
                                @if($item->product && $item->product->images->isNotEmpty())
                                    <div class="product-image">
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                             alt="{{ $item->product->name }}">
                                    </div>
                                @else
                                    <div class="product-placeholder">
                                        <i class="bi bi-box text-muted"></i>
                                    </div>
                                @endif
                                <div class="product-details">
                                    <h6 class="product-name">{{ $item->product->name ?? 'Ürün Bulunamadı' }}</h6>
                                    <div class="product-stats">
                                        <div class="product-stat">
                                            <i class="bi bi-cart3"></i>
                                            <strong>{{ $item->cart_count }}</strong> kez eklendi
                                        </div>
                                        <div class="product-stat text-danger">
                                            <i class="bi bi-cash"></i>
                                            <strong>{{ number_format($item->product->price ?? 0, 2) }} ₺</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-cart3"></i>
                        <p>Henüz sepete eklenen ürün bulunmuyor</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Wishlist Products -->
        <div class="col-xl-6 col-lg-6">
            <div class="product-list-card">
                <div class="product-list-header">
                    <h3 class="product-list-title">Favorilere En Çok Eklenen</h3>
                </div>
                @if(isset($topWishlistProducts) && $topWishlistProducts->count() > 0)
                    <div class="product-list">
                        @foreach($topWishlistProducts as $index => $item)
                            <div class="product-item">
                                <div class="product-rank" style="background: linear-gradient(135deg, #3B82F6, #2563EB);">
                                    #{{ $index + 1 }}
                                </div>
                                @if($item->product && $item->product->images->isNotEmpty())
                                    <div class="product-image">
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                             alt="{{ $item->product->name }}">
                                    </div>
                                @else
                                    <div class="product-placeholder">
                                        <i class="bi bi-box text-muted"></i>
                                    </div>
                                @endif
                                <div class="product-details">
                                    <h6 class="product-name">{{ $item->product->name ?? 'Ürün Bulunamadı' }}</h6>
                                    <div class="product-stats">
                                        <div class="product-stat">
                                            <i class="bi bi-heart"></i>
                                            <strong>{{ $item->wishlist_count }}</strong> kez eklendi
                                        </div>
                                        <div class="product-stat text-info">
                                            <i class="bi bi-cash"></i>
                                            <strong>{{ number_format($item->product->price ?? 0, 2) }} ₺</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-heart"></i>
                        <p>Henüz favorilere eklenen ürün bulunmuyor</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>
<script>
// Refresh Stat Cards Function
function refreshStatCards() {
    const button = document.querySelector('.btn-refresh-stats');
    const statCardsRow = document.getElementById('statCardsRow');
    
    // Add loading state
    button.classList.add('loading');
    button.disabled = true;
    
    // Show skeleton loaders
    const originalContent = statCardsRow.innerHTML;
    statCardsRow.innerHTML = `
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <div class="stat-card-skeleton">
                <div class="skeleton-content">
                    <div class="skeleton-info">
                        <div class="skeleton-number"></div>
                        <div class="skeleton-text"></div>
                        <div class="skeleton-progress"></div>
                    </div>
                    <div class="skeleton-icon"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <div class="stat-card-skeleton">
                <div class="skeleton-content">
                    <div class="skeleton-info">
                        <div class="skeleton-number"></div>
                        <div class="skeleton-text"></div>
                        <div class="skeleton-progress"></div>
                    </div>
                    <div class="skeleton-icon"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <div class="stat-card-skeleton">
                <div class="skeleton-content">
                    <div class="skeleton-info">
                        <div class="skeleton-number"></div>
                        <div class="skeleton-text"></div>
                        <div class="skeleton-progress"></div>
                    </div>
                    <div class="skeleton-icon"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-3">
            <div class="stat-card-skeleton">
                <div class="skeleton-content">
                    <div class="skeleton-info">
                        <div class="skeleton-number"></div>
                        <div class="skeleton-text"></div>
                        <div class="skeleton-progress"></div>
                    </div>
                    <div class="skeleton-icon"></div>
                </div>
            </div>
        </div>
    `;
    
    // Reload page to get fresh data from controller
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

document.addEventListener('DOMContentLoaded', function() {
    // Get data from controller
    const salesData = {
        weekly: {
            data: @json($salesData['weekly']['data'] ?? []),
            labels: @json($salesData['weekly']['labels'] ?? [])
        },
        monthly: {
            data: @json($salesData['monthly']['data'] ?? []),
            labels: @json($salesData['monthly']['labels'] ?? [])
        },
        yearly: {
            data: @json($salesData['yearly']['data'] ?? []),
            labels: @json($salesData['yearly']['labels'] ?? [])
        }
    };

    // Use sample data if empty
    if (!salesData.weekly.data.length) {
        salesData.weekly.data = [4500, 5200, 3800, 6700, 4900, 7800, 8500];
        salesData.weekly.labels = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'];
    }
    
    if (!salesData.monthly.data.length) {
        salesData.monthly.data = [18500, 21300, 25600, 19200, 28700, 32500, 38400, 35200, 29700, 42100, 38900, 47500];
        salesData.monthly.labels = ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'];
    }
    
    if (!salesData.yearly.data.length) {
        salesData.yearly.data = [245000, 312000, 387000, 452000, 528000];
        salesData.yearly.labels = ['2020', '2021', '2022', '2023', '2024'];
    }

    // Chart options
    const options = {
        series: [{
            name: 'Satış',
            data: salesData.monthly.data
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 350
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                inverseColors: false,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100]
            }
        },
        xaxis: {
            categories: salesData.monthly.labels,
            labels: {
                style: {
                    colors: '#737373',
                    fontSize: '12px'
                }
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#737373',
                    fontSize: '12px'
                },
                formatter: function(val) {
                    return val.toLocaleString('tr-TR') + ' ₺';
                }
            }
        },
        colors: ['#0051BB'],
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 3,
            xaxis: {
                lines: {
                    show: false
                }
            }
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
            y: {
                formatter: function(val) {
                    return val.toLocaleString('tr-TR') + ' ₺';
                }
            },
            theme: 'light'
        }
    };

    const chart = new ApexCharts(document.querySelector("#sales-chart"), options);
    chart.render();
    

    // Filter buttons
    document.getElementById('weekly-sales').addEventListener('click', function() {
        document.querySelectorAll('.chart-filter').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        chart.updateOptions({
            series: [{
                data: salesData.weekly.data
            }],
            xaxis: {
                categories: salesData.weekly.labels
            }
        });
    });

    document.getElementById('monthly-sales').addEventListener('click', function() {
        document.querySelectorAll('.chart-filter').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        chart.updateOptions({
            series: [{
                data: salesData.monthly.data
            }],
            xaxis: {
                categories: salesData.monthly.labels
            }
        });
    });

    document.getElementById('yearly-sales').addEventListener('click', function() {
        document.querySelectorAll('.chart-filter').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        chart.updateOptions({
            series: [{
                data: salesData.yearly.data
            }],
            xaxis: {
                categories: salesData.yearly.labels
            }
        });
    });

    // Animate stat cards on load
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.stat-card, .revenue-card, .product-list-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
});
</script>
@endpush
