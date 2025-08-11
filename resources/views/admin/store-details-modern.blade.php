@extends('layouts.admin-modern')

@section('title', 'Mağaza Detayı')

@section('content')
<div class="store-details-container">
    <!-- Page Header Component -->
    <x-admin.page-header 
        title="{{ $store->name ?? $store->store_name ?? 'Mağaza Detayı' }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Mağazalar', 'url' => route('admin.stores')],
            ['label' => 'Mağaza Detayı']
        ]">
        <x-slot name="actions">
            <button class="btn btn-secondary" onclick="exportStoreData({{ $store->id }})">
                <i class="bi bi-download me-2"></i>
                Rapor İndir
            </button>
            <button class="btn btn-primary" onclick="editStore({{ $store->id }})">
                <i class="bi bi-pencil me-2"></i>
                Düzenle
            </button>
        </x-slot>
    </x-admin.page-header>
    
    <!-- Store Info Card -->
    <x-admin.glass-card class="mb-4">
        <div class="store-info-header">
            <div class="store-avatar-large">
                @if($store->user && $store->user->avatar)
                <img src="{{ asset('storage/' . $store->user->avatar) }}" alt="{{ $store->name }}">
                @else
                <div class="avatar-placeholder">
                    <i class="bi bi-shop"></i>
                </div>
                @endif
            </div>
            <div class="store-details">
                <h2>{{ $store->name ?? $store->store_name ?? 'İsimsiz Mağaza' }}</h2>
                <p class="store-email">{{ $store->email }}</p>
                <p class="store-phone">{{ $store->phone ?? 'Telefon belirtilmemiş' }}</p>
                <div class="store-status-info">
                    <span class="status-badge {{ $store->status ?? 'approved' }}">
                        @if(($store->status ?? 'approved') == 'approved')
                            <i class="bi bi-check-circle me-1"></i>Aktif
                        @elseif($store->status == 'pending')
                            <i class="bi bi-clock me-1"></i>Beklemede
                        @else
                            <i class="bi bi-x-circle me-1"></i>Pasif
                        @endif
                    </span>
                    <span class="join-date">
                        <i class="bi bi-calendar3 me-1"></i>
                        Katılım: {{ $store->created_at->format('d.m.Y') }}
                    </span>
                </div>
            </div>
            <div class="store-actions">
                <button class="btn-icon {{ ($store->status ?? 'approved') == 'approved' ? 'active' : '' }}" 
                        onclick="toggleStoreStatus({{ $store->id }})" 
                        title="Durumu Değiştir">
                    <i class="bi bi-power"></i>
                </button>
            </div>
        </div>
    </x-admin.glass-card>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card products">
                <div class="stat-icon">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['total_products']) }}</h3>
                    <p>Toplam Ürün</p>
                    <span class="stat-detail">{{ $stats['active_products'] }} aktif</span>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card orders">
                <div class="stat-icon">
                    <i class="bi bi-cart3"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($stats['total_orders']) }}</h3>
                    <p>Toplam Sipariş</p>
                    <span class="stat-detail">{{ $stats['pending_orders'] }} beklemede</span>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card revenue">
                <div class="stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-content">
                    <h3>₺{{ number_format($stats['total_revenue'], 2) }}</h3>
                    <p>Toplam Gelir</p>
                    <span class="stat-detail">Bu ay: ₺{{ number_format($stats['this_month_revenue'], 2) }}</span>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card rating">
                <div class="stat-icon">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($store->rating ?? 4.5, 1) }}</h3>
                    <p>Ortalama Puan</p>
                    <span class="stat-detail">{{ $store->review_count ?? 128 }} değerlendirme</span>
                </div>
            </x-admin.glass-card>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <x-admin.glass-card>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Gelir Grafiği</h5>
                    <div class="chart-period">
                        <button class="period-btn active" data-period="week">Haftalık</button>
                        <button class="period-btn" data-period="month">Aylık</button>
                        <button class="period-btn" data-period="year">Yıllık</button>
                    </div>
                </div>
                <div id="revenueChart"></div>
            </x-admin.glass-card>
            
            <!-- Recent Orders -->
            <x-admin.glass-card class="mt-4">
                <h5 class="mb-3">Son Siparişler</h5>
                <div class="table-responsive">
                    <table class="table orders-table">
                        <thead>
                            <tr>
                                <th>Sipariş No</th>
                                <th>Müşteri</th>
                                <th>Tutar</th>
                                <th>Durum</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>#{{ $order->order_number }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>₺{{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="status-badge {{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d.m.Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-admin.glass-card>
        </div>
        
        <!-- Store Info & Products -->
        <div class="col-lg-4">
            <x-admin.glass-card>
                <h5 class="mb-3">Mağaza Bilgileri</h5>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Komisyon Oranı</span>
                        <span class="info-value">%{{ $store->commission_rate ?? 10 }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Toplam Ürün</span>
                        <span class="info-value">{{ $stats['total_products'] }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Aktif Ürün</span>
                        <span class="info-value">{{ $stats['active_products'] }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Son Giriş</span>
                        <span class="info-value">{{ $store->last_login_at ? $store->last_login_at->diffForHumans() : 'Hiç giriş yapmadı' }}</span>
                    </div>
                </div>
            </x-admin.glass-card>
            
            <x-admin.glass-card class="mt-4">
                <h5 class="mb-3">En Çok Satan Ürünler</h5>
                <div class="top-products">
                    @forelse($store->products->sortByDesc('sales_count')->take(5) as $product)
                    <div class="product-item">
                        <div class="product-info">
                            @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}"
                                 class="product-thumb">
                            @elseif($product->images && $product->images->first())
                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                 alt="{{ $product->name }}"
                                 class="product-thumb">
                            @else
                            <img src="/images/default-product.svg" 
                                 alt="{{ $product->name }}"
                                 class="product-thumb">
                            @endif
                            <div>
                                <h6>{{ $product->name }}</h6>
                                <span>{{ $product->sales_count ?? 0 }} satış</span>
                            </div>
                        </div>
                        <span class="product-price">₺{{ number_format($product->price, 2) }}</span>
                    </div>
                    @empty
                    <p class="text-muted text-center">Henüz ürün bulunmuyor</p>
                    @endforelse
                </div>
            </x-admin.glass-card>
        </div>
    </div>
</div>

<style>
/* Store Details Styles */
.store-details-container {
    width: 100%;
    padding: 0 var(--spacing-lg);
}

/* Store Info Header */
.store-info-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-xl);
}

.store-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: var(--radius-lg);
    overflow: hidden;
    flex-shrink: 0;
}

.store-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 48px;
}

.store-details {
    flex: 1;
}

.store-details h2 {
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-sm);
}

.store-email,
.store-phone {
    font-size: 14px;
    color: var(--gray-600);
    margin-bottom: var(--spacing-xs);
}

.store-status-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    margin-top: var(--spacing-md);
}

.join-date {
    font-size: 13px;
    color: var(--gray-600);
    display: flex;
    align-items: center;
}

/* Stat Cards */
.stat-card {
    display: flex;
    align-items: flex-start;
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

.stat-card.products .stat-icon {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
}

.stat-card.orders .stat-icon {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.stat-card.revenue .stat-icon {
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
}

.stat-card.rating .stat-icon {
    background: linear-gradient(135deg, #10B981, #059669);
}

.stat-detail {
    display: block;
    font-size: 12px;
    color: var(--gray-500);
    margin-top: var(--spacing-xs);
}

/* Chart Period */
.chart-period {
    display: flex;
    gap: var(--spacing-xs);
}

.period-btn {
    padding: var(--spacing-xs) var(--spacing-sm);
    background: transparent;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-size: 13px;
    color: var(--gray-700);
    cursor: pointer;
    transition: all 0.2s ease;
}

.period-btn:hover {
    background: rgba(169, 0, 0, 0.05);
    border-color: var(--primary-red);
    color: var(--primary-red);
}

.period-btn.active {
    background: var(--primary-red);
    border-color: var(--primary-red);
    color: var(--white);
}

/* Info List */
.info-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-sm);
    background: rgba(240, 248, 255, 0.5);
    border-radius: var(--radius-sm);
}

.info-label {
    font-size: 14px;
    color: var(--gray-600);
}

.info-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
}

/* Top Products */
.top-products {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.product-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-sm);
    background: rgba(240, 248, 255, 0.3);
    border-radius: var(--radius-sm);
    transition: all 0.2s ease;
}

.product-item:hover {
    background: rgba(240, 248, 255, 0.5);
    transform: translateX(2px);
}

.product-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.product-thumb {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-sm);
    object-fit: cover;
}

.product-info h6 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    color: var(--gray-900);
}

.product-info span {
    font-size: 12px;
    color: var(--gray-600);
}

.product-price {
    font-size: 14px;
    font-weight: 600;
    color: var(--primary-red);
}

/* Orders Table */
.orders-table {
    font-size: 14px;
}

.orders-table th {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 11px;
    font-weight: 500;
    text-transform: capitalize;
}

.status-badge.pending {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
}

.status-badge.processing {
    background: rgba(0, 81, 187, 0.1);
    color: var(--primary-blue);
}

.status-badge.delivered {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

/* Responsive */
@media (max-width: 991px) {
    .store-info-header {
        flex-direction: column;
        text-align: center;
    }
    
    .store-actions {
        width: 100%;
        display: flex;
        justify-content: center;
    }
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Revenue Chart
const revenueOptions = {
    series: [{
        name: 'Gelir',
        data: [30000, 40000, 35000, 50000, 49000, 60000, 70000]
    }],
    chart: {
        height: 350,
        type: 'area',
        toolbar: {
            show: false
        }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth',
        width: 3,
        colors: ['#A90000']
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.3,
            stops: [0, 90, 100],
            colorStops: [{
                offset: 0,
                color: '#A90000',
                opacity: 0.7
            }, {
                offset: 100,
                color: '#C1121F',
                opacity: 0.3
            }]
        }
    },
    xaxis: {
        categories: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz']
    },
    yaxis: {
        labels: {
            formatter: function(value) {
                return '₺' + value.toLocaleString('tr-TR');
            }
        }
    },
    tooltip: {
        y: {
            formatter: function(value) {
                return '₺' + value.toLocaleString('tr-TR');
            }
        }
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    }
};

const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
revenueChart.render();

// Period Buttons
document.querySelectorAll('.period-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Update chart data based on period
        // This is where you would fetch new data
    });
});

// Toggle Store Status
function toggleStoreStatus(storeId) {
    if (confirm('Mağaza durumunu değiştirmek istediğinizden emin misiniz?')) {
        window.location.href = `/admin/stores/${storeId}/toggle-status`;
    }
}

// Edit Store
function editStore(storeId) {
    window.location.href = `/admin/stores/${storeId}/edit`;
}

// Export Store Data
function exportStoreData(storeId) {
    window.location.href = `/admin/stores/${storeId}/export`;
}
</script>
@endpush