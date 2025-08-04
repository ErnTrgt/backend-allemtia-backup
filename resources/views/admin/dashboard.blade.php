@extends('layouts.admin-modern')

@section('title', 'Dashboard')
@section('header-title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/dashboard.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Dashboard Genel Bakış</h1>
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
        <span class="breadcrumb-separator">/</span>
        <span>Dashboard</span>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="{{ route('admin.products') }}" class="quick-action">
        <i class="bi bi-box-seam quick-action-icon"></i>
        <span class="quick-action-text">Ürünler</span>
    </a>
    <a href="{{ route('admin.users') }}" class="quick-action">
        <i class="bi bi-people quick-action-icon"></i>
        <span class="quick-action-text">Kullanıcılar</span>
    </a>
    <a href="{{ route('admin.orders') }}" class="quick-action">
        <i class="bi bi-cart-check quick-action-icon"></i>
        <span class="quick-action-text">Siparişler</span>
    </a>
    <a href="{{ route('admin.reports') }}" class="quick-action">
        <i class="bi bi-file-earmark-bar-graph quick-action-icon"></i>
        <span class="quick-action-text">Raporlar</span>
    </a>
</div>

<!-- Stat Cards -->
<div class="stat-cards">
    <!-- Total Users -->
    <div class="stat-card" data-animate="fadeIn">
        <div class="stat-icon users">
            <i class="bi bi-people"></i>
        </div>
        <div class="stat-value">{{ number_format($userCount ?? 0) }}</div>
        <div class="stat-label">Toplam Kullanıcı</div>
        <div class="stat-change positive">
            <i class="bi bi-arrow-up"></i>
            <span>12% artış</span>
        </div>
    </div>
    
    <!-- Total Products -->
    <div class="stat-card" data-animate="fadeIn">
        <div class="stat-icon products">
            <i class="bi bi-box-seam"></i>
        </div>
        <div class="stat-value">{{ number_format($productCount ?? 0) }}</div>
        <div class="stat-label">Toplam Ürün</div>
        <div class="stat-change positive">
            <i class="bi bi-arrow-up"></i>
            <span>8% artış</span>
        </div>
    </div>
    
    <!-- Total Orders -->
    <div class="stat-card" data-animate="fadeIn">
        <div class="stat-icon orders">
            <i class="bi bi-cart3"></i>
        </div>
        <div class="stat-value">{{ number_format($orderCount ?? 0) }}</div>
        <div class="stat-label">Toplam Sipariş</div>
        <div class="stat-change negative">
            <i class="bi bi-arrow-down"></i>
            <span>3% düşüş</span>
        </div>
    </div>
    
    <!-- Total Revenue -->
    <div class="stat-card" data-animate="fadeIn">
        <div class="stat-icon revenue">
            <i class="bi bi-currency-exchange"></i>
        </div>
        <div class="stat-value">₺{{ number_format($totalRevenue ?? 0, 2) }}</div>
        <div class="stat-label">Toplam Gelir</div>
        <div class="stat-change positive">
            <i class="bi bi-arrow-up"></i>
            <span>24% artış</span>
        </div>
    </div>
</div>

<!-- Main Dashboard Grid -->
<div class="dashboard-grid">
    <!-- Left Column -->
    <div class="dashboard-left">
        <!-- Sales Chart -->
        <div class="chart-container" data-animate="slideInUp">
            <div class="chart-header">
                <h3 class="chart-title">Satış Grafiği</h3>
                <div class="chart-actions">
                    <div class="chart-period">
                        <button class="period-btn" data-period="week">Haftalık</button>
                        <button class="period-btn active" data-period="month">Aylık</button>
                        <button class="period-btn" data-period="year">Yıllık</button>
                    </div>
                </div>
            </div>
            <div id="salesChart" style="height: 350px;"></div>
        </div>
        
        <!-- Recent Orders -->
        <div class="recent-orders" data-animate="slideInUp">
            <div class="section-header">
                <h3 class="section-title">Son Siparişler</h3>
                <a href="{{ route('admin.orders') }}" class="view-all-link">
                    Tümünü Gör <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table-glass">
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
                        @forelse($recentOrders ?? [] as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user->name }}</td>
                            <td>₺{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d.m.Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Henüz sipariş yok</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="dashboard-right">
        <!-- Top Products -->
        <div class="top-products" data-animate="slideInUp">
            <div class="section-header">
                <h3 class="section-title">En Çok Satanlar</h3>
                <a href="{{ route('admin.products') }}" class="view-all-link">
                    Tümünü Gör <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="products-list">
                @forelse($topProducts ?? [] as $product)
                <div class="product-item">
                    <img src="{{ $product->image_url ?? asset('admin/src/images/product-placeholder.png') }}" 
                         alt="{{ $product->name }}" 
                         class="product-image">
                    <div class="product-info">
                        <div class="product-name">{{ $product->name }}</div>
                        <div class="product-sales">{{ $product->sales_count }} satış</div>
                    </div>
                    <div class="product-revenue">₺{{ number_format($product->revenue, 2) }}</div>
                </div>
                @empty
                <p class="text-center text-muted">Henüz ürün yok</p>
                @endforelse
            </div>
        </div>
        
        <!-- Activity Timeline -->
        <div class="activity-timeline" data-animate="slideInUp">
            <div class="section-header">
                <h3 class="section-title">Son Aktiviteler</h3>
            </div>
            <div class="timeline-list">
                <div class="timeline-item">
                    <div class="timeline-icon order">
                        <i class="bi bi-cart"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Yeni sipariş alındı</div>
                        <div class="timeline-time">5 dakika önce</div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon user">
                        <i class="bi bi-person"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Yeni kullanıcı kaydı</div>
                        <div class="timeline-time">15 dakika önce</div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-icon product">
                        <i class="bi bi-box"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Ürün stok güncellendi</div>
                        <div class="timeline-time">1 saat önce</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const salesOptions = {
        series: [{
            name: 'Satışlar',
            data: [45, 52, 38, 65, 72, 83, 91, 78, 94, 102, 110, 95]
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
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara']
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return '₺' + value + 'K';
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return '₺' + value + ',000';
                }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4
        }
    };
    
    const salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
    salesChart.render();
    
    // Period buttons
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelector('.period-btn.active').classList.remove('active');
            this.classList.add('active');
            
            // Update chart data based on period
            const period = this.dataset.period;
            // This would typically fetch new data from server
            AdminPanel.showToast(`${this.textContent} veriler yükleniyor...`, 'info');
        });
    });
    
    // Animate counters
    const counters = document.querySelectorAll('.stat-value');
    counters.forEach(counter => {
        const target = parseInt(counter.innerText.replace(/[^\d]/g, ''));
        const increment = target / 100;
        let current = 0;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counter.innerText = counter.innerText.includes('₺') ? 
                    '₺' + Math.ceil(current).toLocaleString('tr-TR') : 
                    Math.ceil(current).toLocaleString('tr-TR');
                setTimeout(updateCounter, 20);
            } else {
                counter.innerText = counter.innerText.includes('₺') ? 
                    '₺' + target.toLocaleString('tr-TR') : 
                    target.toLocaleString('tr-TR');
            }
        };
        
        updateCounter();
    });
});
</script>
@endpush