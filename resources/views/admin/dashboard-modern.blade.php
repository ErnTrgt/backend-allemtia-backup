@extends('layouts.admin-modern')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">Dashboard</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Admin</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
    
    <!-- Welcome Card -->
    <div class="welcome-card glass-card mb-4">
        <div class="welcome-content">
            <div class="welcome-text">
                <h2 class="welcome-title">Hoş Geldiniz, {{ Auth::user()->name }}! 👋</h2>
                <p class="welcome-subtitle">Bugün sistemde neler oluyor bir göz atalım.</p>
            </div>
            <div class="welcome-stats">
                <div class="stat-item">
                    <span class="stat-value">{{ number_format($orderCount) }}</span>
                    <span class="stat-label">Yeni Sipariş</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">₺{{ number_format($todayRevenue ?? 0, 2) }}</span>
                    <span class="stat-label">Bugünkü Gelir</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Maintenance Mode Widget -->
    <div class="maintenance-widget glass-card mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="maintenance-content d-flex justify-content-between align-items-center p-3">
            <div class="maintenance-info text-white">
                <h4 class="mb-1 d-flex align-items-center">
                    <i class="bi bi-tools me-2"></i> Bakım Modu
                </h4>
                <p class="mb-0">
                    <span class="badge {{ $maintenanceMode ? 'bg-danger' : 'bg-success' }} me-2">
                        {{ $maintenanceMode ? 'Aktif' : 'Kapalı' }}
                    </span>
                    @if($maintenanceMode && $maintenanceMode->estimated_end_time)
                        <small>Tahmini bitiş: {{ $maintenanceMode->estimated_end_time->format('d.m.Y H:i') }}</small>
                    @endif
                </p>
            </div>
            <div class="maintenance-actions">
                <button class="btn btn-light btn-sm me-2" onclick="toggleMaintenanceMode()" id="maintenanceToggleBtn">
                    <i class="bi {{ $maintenanceMode ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                    {{ $maintenanceMode ? 'Kapat' : 'Aç' }}
                </button>
                <a href="{{ route('admin.maintenance.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-gear"></i> Yönet
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Users -->
        <div class="col-xl-3 col-lg-6">
            <div class="stat-card glass-card">
                <div class="stat-card-body">
                    <div class="stat-icon-wrapper red">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-details">
                        <h3 class="stat-number">{{ number_format($userCount) }}</h3>
                        <p class="stat-title">Toplam Kullanıcı</p>
                        <div class="stat-change positive">
                            <i class="bi bi-arrow-up"></i>
                            <span>12% artış</span>
                        </div>
                    </div>
                </div>
                <div class="stat-chart">
                    <canvas id="usersChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Total Products -->
        <div class="col-xl-3 col-lg-6">
            <div class="stat-card glass-card">
                <div class="stat-card-body">
                    <div class="stat-icon-wrapper blue">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div class="stat-details">
                        <h3 class="stat-number">{{ number_format($productCount) }}</h3>
                        <p class="stat-title">Toplam Ürün</p>
                        <div class="stat-change positive">
                            <i class="bi bi-arrow-up"></i>
                            <span>8% artış</span>
                        </div>
                    </div>
                </div>
                <div class="stat-chart">
                    <canvas id="productsChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Total Orders -->
        <div class="col-xl-3 col-lg-6">
            <div class="stat-card glass-card">
                <div class="stat-card-body">
                    <div class="stat-icon-wrapper green">
                        <i class="bi bi-cart-check-fill"></i>
                    </div>
                    <div class="stat-details">
                        <h3 class="stat-number">{{ number_format($orderCount) }}</h3>
                        <p class="stat-title">Toplam Sipariş</p>
                        <div class="stat-change negative">
                            <i class="bi bi-arrow-down"></i>
                            <span>3% düşüş</span>
                        </div>
                    </div>
                </div>
                <div class="stat-chart">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Total Revenue -->
        <div class="col-xl-3 col-lg-6">
            <div class="stat-card glass-card">
                <div class="stat-card-body">
                    <div class="stat-icon-wrapper purple">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stat-details">
                        <h3 class="stat-number">₺{{ number_format($totalRevenue ?? 0, 0) }}</h3>
                        <p class="stat-title">Toplam Gelir</p>
                        <div class="stat-change positive">
                            <i class="bi bi-arrow-up"></i>
                            <span>18% artış</span>
                        </div>
                    </div>
                </div>
                <div class="stat-chart">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Sales Chart -->
        <div class="col-xl-8">
            <div class="chart-card glass-card">
                <div class="card-header">
                    <h4 class="card-title">Satış Grafiği</h4>
                    <div class="chart-controls">
                        <button class="chart-btn active" data-range="week">Hafta</button>
                        <button class="chart-btn" data-range="month">Ay</button>
                        <button class="chart-btn" data-range="year">Yıl</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="salesChart"></div>
                </div>
            </div>
        </div>
        
        <!-- Category Distribution -->
        <div class="col-xl-4">
            <div class="chart-card glass-card">
                <div class="card-header">
                    <h4 class="card-title">Kategori Dağılımı</h4>
                </div>
                <div class="card-body">
                    <div id="categoryChart"></div>
                    <div class="category-legend">
                        <div class="legend-item">
                            <span class="legend-color" style="background: #A90000;"></span>
                            <span class="legend-label">Elektronik</span>
                            <span class="legend-value">35%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #0051BB;"></span>
                            <span class="legend-label">Giyim</span>
                            <span class="legend-value">28%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #3FA1DD;"></span>
                            <span class="legend-label">Ev & Yaşam</span>
                            <span class="legend-value">22%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background: #C1121F;"></span>
                            <span class="legend-label">Diğer</span>
                            <span class="legend-value">15%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tables Row -->
    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-xl-8">
            <div class="table-card glass-card">
                <div class="card-header">
                    <h4 class="card-title">Son Siparişler</h4>
                    <a href="{{ route('admin.orders') }}" class="btn-text">
                        Tümünü Gör <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table modern-table">
                            <thead>
                                <tr>
                                    <th>Sipariş No</th>
                                    <th>Müşteri</th>
                                    <th>Ürün</th>
                                    <th>Tutar</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders ?? [] as $order)
                                <tr>
                                    <td>#{{ $order->order_number }}</td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar-sm">{{ substr($order->customer_name, 0, 1) }}</div>
                                            <span>{{ $order->customer_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $order->items->count() }} ürün</td>
                                    <td class="text-strong">₺{{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $order->status }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d.m.Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Henüz sipariş bulunmuyor</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Sellers -->
        <div class="col-xl-4">
            <div class="table-card glass-card">
                <div class="card-header">
                    <h4 class="card-title">En Çok Satan Satıcılar</h4>
                </div>
                <div class="card-body">
                    <div class="seller-list">
                        @forelse($topSellers ?? [] as $seller)
                        <div class="seller-item">
                            <div class="seller-info">
                                <div class="seller-avatar">
                                    {{ substr($seller->name, 0, 1) }}
                                </div>
                                <div class="seller-details">
                                    <h5 class="seller-name">{{ $seller->name }}</h5>
                                    <p class="seller-sales">{{ $seller->products_count }} ürün</p>
                                </div>
                            </div>
                            <div class="seller-revenue">
                                <span class="revenue-amount">₺{{ number_format($seller->total_revenue ?? 0, 0) }}</span>
                                <span class="revenue-change positive">+12%</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-center py-4">Henüz satıcı bulunmuyor</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Specific Styles */
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Glass Card Base */
.glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    transition: var(--transition-base);
}

.glass-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* Welcome Card */
.welcome-card {
    padding: var(--spacing-2xl);
    background: linear-gradient(135deg, rgba(169, 0, 0, 0.1), rgba(0, 81, 187, 0.1));
}

.welcome-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.welcome-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-sm);
}

.welcome-subtitle {
    font-size: 16px;
    color: var(--gray-600);
}

.welcome-stats {
    display: flex;
    gap: var(--spacing-2xl);
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 32px;
    font-weight: 700;
    color: var(--primary-red);
}

.stat-label {
    font-size: 14px;
    color: var(--gray-600);
}

/* Stat Cards */
.stat-card {
    height: 100%;
    padding: var(--spacing-lg);
    position: relative;
    overflow: hidden;
}

.stat-card-body {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-lg);
}

.stat-icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--white);
}

.stat-icon-wrapper.red {
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
}

.stat-icon-wrapper.blue {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
}

.stat-icon-wrapper.green {
    background: linear-gradient(135deg, #10B981, #059669);
}

.stat-icon-wrapper.purple {
    background: linear-gradient(135deg, #8B5CF6, #7C3AED);
}

.stat-details {
    flex: 1;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-xs);
}

.stat-title {
    font-size: 14px;
    color: var(--gray-600);
    margin-bottom: var(--spacing-sm);
}

.stat-change {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: 13px;
    font-weight: 500;
}

.stat-change.positive {
    color: #10B981;
}

.stat-change.negative {
    color: #EF4444;
}

.stat-chart {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 100px;
    height: 40px;
    opacity: 0.3;
}

/* Chart Cards */
.chart-card {
    height: 100%;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-lg);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.chart-controls {
    display: flex;
    gap: var(--spacing-sm);
}

.chart-btn {
    padding: var(--spacing-xs) var(--spacing-md);
    background: transparent;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-size: 13px;
    color: var(--gray-600);
    cursor: pointer;
    transition: var(--transition-fast);
}

.chart-btn:hover,
.chart-btn.active {
    background: var(--primary-red);
    border-color: var(--primary-red);
    color: var(--white);
}

.btn-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    border-radius: var(--radius-sm);
    color: var(--gray-600);
    cursor: pointer;
    transition: var(--transition-fast);
}

.btn-icon:hover {
    background: var(--gray-100);
}

.card-body {
    padding: var(--spacing-lg);
}

/* Category Legend */
.category-legend {
    margin-top: var(--spacing-lg);
}

.legend-item {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-sm);
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: var(--radius-sm);
}

.legend-label {
    flex: 1;
    font-size: 14px;
    color: var(--gray-700);
}

.legend-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
}

/* Modern Table */
.table-card {
    height: 100%;
}

.btn-text {
    color: var(--primary-red);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    transition: var(--transition-fast);
}

.btn-text:hover {
    gap: var(--spacing-sm);
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table th {
    padding: var(--spacing-md);
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid var(--gray-200);
}

.modern-table td {
    padding: var(--spacing-md);
    font-size: 14px;
    color: var(--gray-700);
    border-bottom: 1px solid var(--gray-100);
}

.modern-table tbody tr:hover {
    background: rgba(0, 0, 0, 0.02);
}

.user-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.user-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 13px;
    font-weight: 600;
}

.text-strong {
    font-weight: 600;
    color: var(--gray-900);
}

.status-badge {
    display: inline-block;
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 500;
}

.status-badge.status-pending {
    background: rgba(251, 191, 36, 0.1);
    color: #F59E0B;
}

.status-badge.status-processing {
    background: rgba(59, 130, 246, 0.1);
    color: #3B82F6;
}

.status-badge.status-delivered {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

.status-badge.status-cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
}

/* Seller List */
.seller-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
}

.seller-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-md);
    background: rgba(0, 0, 0, 0.02);
    border-radius: var(--radius-md);
    transition: var(--transition-fast);
}

.seller-item:hover {
    background: rgba(0, 0, 0, 0.05);
}

.seller-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.seller-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-weight: 600;
}

.seller-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.seller-sales {
    font-size: 13px;
    color: var(--gray-600);
    margin: 0;
}

.revenue-amount {
    display: block;
    font-size: 16px;
    font-weight: 700;
    color: var(--gray-900);
    text-align: right;
}

.revenue-change {
    font-size: 12px;
    font-weight: 500;
}

.revenue-change.positive {
    color: #10B981;
}

/* Responsive */
@media (max-width: 1200px) {
    .welcome-content {
        flex-direction: column;
        gap: var(--spacing-lg);
    }
    
    .welcome-stats {
        width: 100%;
        justify-content: flex-start;
    }
}

@media (max-width: 768px) {
    .stat-card-body {
        flex-direction: column;
    }
    
    .stat-chart {
        display: none;
    }
    
    .chart-controls {
        display: none;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
}
</style>

<script>
// Bakım modunu aç/kapa
function toggleMaintenanceMode() {
    const btn = document.getElementById('maintenanceToggleBtn');
    const isActive = btn.innerHTML.includes('Kapat');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>İşleniyor...';
    
    fetch('{{ route("admin.maintenance.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            is_active: !isActive
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Sayfayı yenile
            window.location.reload();
        } else {
            alert(data.message || 'Bir hata oluştu');
            btn.disabled = false;
            btn.innerHTML = isActive ? '<i class="bi bi-toggle-on"></i> Kapat' : '<i class="bi bi-toggle-off"></i> Aç';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu');
        btn.disabled = false;
        btn.innerHTML = isActive ? '<i class="bi bi-toggle-on"></i> Kapat' : '<i class="bi bi-toggle-off"></i> Aç';
    });
}
</script>
@endsection

@push('scripts')
<script>
// Mini Sparkline Charts for Stat Cards
function createSparkline(canvasId, data, color) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    // Clear canvas
    ctx.clearRect(0, 0, width, height);
    
    // Draw line
    ctx.beginPath();
    ctx.strokeStyle = color;
    ctx.lineWidth = 2;
    
    const points = data.length;
    const stepX = width / (points - 1);
    const maxValue = Math.max(...data);
    const minValue = Math.min(...data);
    const range = maxValue - minValue;
    
    data.forEach((value, index) => {
        const x = index * stepX;
        const y = height - ((value - minValue) / range) * height * 0.8 - height * 0.1;
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
}

// Create mini charts
createSparkline('usersChart', [30, 35, 32, 40, 42, 38, 45], '#A90000');
createSparkline('productsChart', [20, 22, 19, 24, 28, 25, 30], '#0051BB');
createSparkline('ordersChart', [50, 48, 52, 45, 40, 42, 38], '#10B981');
createSparkline('revenueChart', [100, 120, 115, 140, 135, 160, 180], '#8B5CF6');

// Sales Chart
const salesOptions = {
    series: [{
        name: 'Satışlar',
        data: [30, 40, 35, 50, 49, 60, 70]
    }],
    chart: {
        type: 'area',
        height: 350,
        toolbar: {
            show: false
        },
        fontFamily: 'DM Sans, sans-serif'
    },
    colors: ['#A90000'],
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
            opacityFrom: 0.5,
            opacityTo: 0.1,
            stops: [0, 100]
        }
    },
    xaxis: {
        categories: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false
        }
    },
    yaxis: {
        labels: {
            formatter: function(value) {
                return '₺' + value + 'K';
            }
        }
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    },
    tooltip: {
        x: {
            format: 'dd/MM/yy'
        },
        y: {
            formatter: function(value) {
                return '₺' + value + 'K';
            }
        }
    }
};

const salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
salesChart.render();

// Category Chart
const categoryOptions = {
    series: [35, 28, 22, 15],
    chart: {
        type: 'donut',
        height: 280,
        fontFamily: 'DM Sans, sans-serif'
    },
    colors: ['#A90000', '#0051BB', '#3FA1DD', '#C1121F'],
    labels: ['Elektronik', 'Giyim', 'Ev & Yaşam', 'Diğer'],
    dataLabels: {
        enabled: false
    },
    plotOptions: {
        pie: {
            donut: {
                size: '70%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Toplam',
                        fontSize: '14px',
                        fontWeight: 600,
                        color: '#374151',
                        formatter: function (w) {
                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + '%';
                        }
                    }
                }
            }
        }
    },
    legend: {
        show: false
    },
    stroke: {
        width: 0
    }
};

const categoryChart = new ApexCharts(document.querySelector("#categoryChart"), categoryOptions);
categoryChart.render();

// Chart Range Buttons
document.querySelectorAll('.chart-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.chart-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Update chart based on selected range
        const range = this.dataset.range;
        // Implement chart update logic here
    });
});
</script>
@endpush