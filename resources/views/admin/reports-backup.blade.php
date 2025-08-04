@extends('layouts.admin')

@section('title', 'Raporlar')
@section('header-title', 'Raporlar')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/reports.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Satış Raporları</h1>
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
        <span class="breadcrumb-separator">/</span>
        <span>Raporlar</span>
    </div>
</div>

<!-- Report Stats -->
<div class="report-stats">
    <!-- Total Revenue Card -->
    <div class="stat-card revenue">
        <div class="stat-icon">
            <i class="bi bi-currency-dollar"></i>
        </div>
        <div class="stat-value">₺1.245.680,00</div>
        <div class="stat-label">Toplam Gelir</div>
        <div class="stat-change positive">
            <i class="bi bi-arrow-up-short"></i>
            <span>%12.5 artış</span>
        </div>
    </div>
    
    <!-- Total Orders Card -->
    <div class="stat-card orders">
        <div class="stat-icon">
            <i class="bi bi-cart-check"></i>
        </div>
        <div class="stat-value">3.456</div>
        <div class="stat-label">Toplam Sipariş</div>
        <div class="stat-change positive">
            <i class="bi bi-arrow-up-short"></i>
            <span>%8.3 artış</span>
        </div>
    </div>
    
    <!-- Total Products Card -->
    <div class="stat-card products">
        <div class="stat-icon">
            <i class="bi bi-box-seam"></i>
        </div>
        <div class="stat-value">12.847</div>
        <div class="stat-label">Toplam Ürün</div>
        <div class="stat-change positive">
            <i class="bi bi-arrow-up-short"></i>
            <span>%5.2 artış</span>
        </div>
    </div>
    
    <!-- Total Users Card -->
    <div class="stat-card users">
        <div class="stat-icon">
            <i class="bi bi-people"></i>
        </div>
        <div class="stat-value">8.932</div>
        <div class="stat-label">Toplam Kullanıcı</div>
        <div class="stat-change positive">
            <i class="bi bi-arrow-up-short"></i>
            <span>%15.7 artış</span>
        </div>
    </div>
</div>

<!-- Report Filters -->
<div class="report-filters">
    <div class="filters-header">
        <h3 class="filters-title">
            <i class="bi bi-funnel me-2"></i>
            Rapor Filtreleri
        </h3>
        <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">
            <i class="bi bi-arrow-clockwise me-1"></i>
            Filtreleri Sıfırla
        </button>
    </div>
    
    <form id="reportFilters" method="GET" action="{{ route('admin.reports') }}">
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Başlangıç Tarihi</label>
                <input type="date" name="start_date" class="filter-control" 
                       value="{{ request('start_date', now()->subMonth()->format('Y-m-d')) }}">
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Bitiş Tarihi</label>
                <input type="date" name="end_date" class="filter-control" 
                       value="{{ request('end_date', now()->format('Y-m-d')) }}">
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Mağaza</label>
                <select name="store_id" class="filter-control">
                    <option value="">Tüm Mağazalar</option>
                    <option value="1">TechStore Pro</option>
                    <option value="2">Digital World</option>
                    <option value="3">Mobile Center</option>
                    <option value="4">Gadget Hub</option>
                    <option value="5">Smart Shop</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label class="filter-label">Kategori</label>
                <select name="category_id" class="filter-control">
                    <option value="">Tüm Kategoriler</option>
                    <option value="1">Elektronik</option>
                    <option value="2">Giyim</option>
                    <option value="3">Ev & Yaşam</option>
                    <option value="4">Kozmetik</option>
                    <option value="5">Spor & Outdoor</option>
                </select>
            </div>
        </div>
        
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary" style="background: var(--primary-red); border-color: var(--primary-red);">
                <i class="bi bi-search me-1"></i>
                Filtrele
            </button>
        </div>
    </form>
</div>

<!-- Charts Section -->
<div class="charts-grid">
    <!-- Sales Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Satış Grafiği</h3>
            <div class="chart-tabs">
                <button class="chart-tab active" data-period="week">Haftalık</button>
                <button class="chart-tab" data-period="month">Aylık</button>
                <button class="chart-tab" data-period="year">Yıllık</button>
            </div>
        </div>
        <div class="chart-body">
            <canvas id="salesChart"></canvas>
        </div>
    </div>
    
    <!-- Category Distribution -->
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">Kategori Dağılımı</h3>
        </div>
        <div class="chart-body">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<!-- Top Items Section -->
<div class="top-items-grid">
    <!-- Top Selling Products -->
    <div class="top-items-card">
        <div class="table-header">
            <h3 class="table-title">En Çok Satan Ürünler</h3>
        </div>
        <div class="top-items-list">
            @php
                $topProducts = [
                    ['name' => 'iPhone 15 Pro Max', 'sales' => 145, 'revenue' => 217500],
                    ['name' => 'Samsung Galaxy S24', 'sales' => 98, 'revenue' => 117600],
                    ['name' => 'MacBook Pro M3', 'sales' => 67, 'revenue' => 167500],
                    ['name' => 'iPad Pro 12.9"', 'sales' => 54, 'revenue' => 64800],
                    ['name' => 'AirPods Pro 2', 'sales' => 203, 'revenue' => 50750]
                ];
            @endphp
            
            @foreach($topProducts as $index => $product)
                <div class="top-item">
                    <div class="item-rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="item-info">
                        <div class="item-name">{{ $product['name'] }}</div>
                        <div class="item-meta">{{ $product['sales'] }} satış</div>
                    </div>
                    <div class="item-value">₺{{ number_format($product['revenue'], 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Top Stores -->
    <div class="top-items-card">
        <div class="table-header">
            <h3 class="table-title">En İyi Performans Gösteren Mağazalar</h3>
        </div>
        <div class="top-items-list">
            @php
                $topStores = [
                    ['name' => 'TechStore Pro', 'orders' => 342, 'revenue' => 684000],
                    ['name' => 'Digital World', 'orders' => 287, 'revenue' => 574000],
                    ['name' => 'Mobile Center', 'orders' => 256, 'revenue' => 512000],
                    ['name' => 'Gadget Hub', 'orders' => 198, 'revenue' => 396000],
                    ['name' => 'Smart Shop', 'orders' => 167, 'revenue' => 334000]
                ];
            @endphp
            
            @foreach($topStores as $index => $store)
                <div class="top-item">
                    <div class="item-rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : '')) }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="item-info">
                        <div class="item-name">{{ $store['name'] }}</div>
                        <div class="item-meta">{{ $store['orders'] }} sipariş</div>
                    </div>
                    <div class="item-value">₺{{ number_format($store['revenue'], 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="data-table-card">
    <div class="table-header">
        <h3 class="table-title">Son Siparişler</h3>
        <div class="table-actions">
            <button class="export-btn" onclick="showExportModal()">
                <i class="bi bi-download"></i>
                <span>Dışa Aktar</span>
            </button>
        </div>
    </div>
    
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Sipariş ID</th>
                    <th>Müşteri</th>
                    <th>Ürün</th>
                    <th>Mağaza</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                    <th>Tutar</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $mockOrders = [
                        ['id' => 3456, 'customer' => 'Ahmet Yılmaz', 'product' => 'iPhone 15 Pro', 'store' => 'TechStore Pro', 'date' => '01.08.2025 14:32', 'status' => 'completed', 'price' => 54999],
                        ['id' => 3455, 'customer' => 'Ayşe Demir', 'product' => 'Samsung Galaxy S24', 'store' => 'Digital World', 'date' => '01.08.2025 13:21', 'status' => 'pending', 'price' => 42999],
                        ['id' => 3454, 'customer' => 'Mehmet Kaya', 'product' => 'MacBook Air M2', 'store' => 'Mobile Center', 'date' => '01.08.2025 12:15', 'status' => 'completed', 'price' => 38999],
                        ['id' => 3453, 'customer' => 'Fatma Öz', 'product' => 'iPad Pro 11"', 'store' => 'Gadget Hub', 'date' => '01.08.2025 11:45', 'status' => 'completed', 'price' => 28999],
                        ['id' => 3452, 'customer' => 'Ali Çelik', 'product' => 'AirPods Pro 2', 'store' => 'Smart Shop', 'date' => '01.08.2025 10:30', 'status' => 'pending', 'price' => 8999],
                    ];
                @endphp
                
                @foreach($mockOrders as $order)
                    <tr>
                        <td>#{{ $order['id'] }}</td>
                        <td>{{ $order['customer'] }}</td>
                        <td>{{ $order['product'] }}</td>
                        <td>{{ $order['store'] }}</td>
                        <td>{{ $order['date'] }}</td>
                        <td>
                            <span class="badge badge-{{ $order['status'] == 'completed' ? 'success' : 'warning' }}">
                                {{ $order['status'] == 'completed' ? 'Tamamlandı' : 'Beklemede' }}
                            </span>
                        </td>
                        <td>₺{{ number_format($order['price'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Export Modal -->
<div class="export-modal" id="exportModal">
    <div class="export-content">
        <h3 class="export-title">Raporu Dışa Aktar</h3>
        
        <div class="export-options">
            <label class="export-option" data-format="excel">
                <input type="radio" name="export_format" value="excel" style="display: none;">
                <div class="export-icon">
                    <i class="bi bi-file-earmark-excel" style="color: #10B981; font-size: 20px;"></i>
                </div>
                <div class="export-info">
                    <h4>Excel (.xlsx)</h4>
                    <p>Microsoft Excel formatında</p>
                </div>
            </label>
            
            <label class="export-option" data-format="csv">
                <input type="radio" name="export_format" value="csv" style="display: none;">
                <div class="export-icon">
                    <i class="bi bi-file-earmark-text" style="color: #3B82F6; font-size: 20px;"></i>
                </div>
                <div class="export-info">
                    <h4>CSV (.csv)</h4>
                    <p>Virgülle ayrılmış değerler</p>
                </div>
            </label>
            
            <label class="export-option" data-format="pdf">
                <input type="radio" name="export_format" value="pdf" style="display: none;">
                <div class="export-icon">
                    <i class="bi bi-file-earmark-pdf" style="color: #EF4444; font-size: 20px;"></i>
                </div>
                <div class="export-info">
                    <h4>PDF (.pdf)</h4>
                    <p>Yazdırılabilir doküman</p>
                </div>
            </label>
        </div>
        
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="closeExportModal()">
                İptal
            </button>
            <button type="button" class="btn btn-primary" onclick="exportReport()" style="background: var(--primary-red); border-color: var(--primary-red);">
                <i class="bi bi-download me-1"></i>
                Dışa Aktar
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
        datasets: [{
            label: 'Satışlar',
            data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
            borderColor: '#A90000',
            backgroundColor: 'rgba(169, 0, 0, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₺' + value.toLocaleString('tr-TR');
                    }
                }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: ['Elektronik', 'Giyim', 'Ev & Yaşam', 'Kozmetik', 'Diğer'],
        datasets: [{
            data: [35, 25, 20, 15, 5],
            backgroundColor: [
                '#A90000',
                '#0051BB',
                '#F59E0B',
                '#10B981',
                '#8B5CF6'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});

// Chart Period Toggle
document.querySelectorAll('.chart-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        // Update chart data based on period
        const period = this.dataset.period;
        updateSalesChart(period);
    });
});

function updateSalesChart(period) {
    let labels, data;
    
    switch(period) {
        case 'week':
            labels = ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'];
            data = [12000, 19000, 15000, 25000, 22000, 30000, 28000];
            break;
        case 'month':
            labels = ['1. Hafta', '2. Hafta', '3. Hafta', '4. Hafta'];
            data = [85000, 92000, 110000, 125000];
            break;
        case 'year':
            labels = ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'];
            data = [320000, 350000, 380000, 420000, 450000, 480000, 510000, 490000, 520000, 550000, 580000, 620000];
            break;
    }
    
    salesChart.data.labels = labels;
    salesChart.data.datasets[0].data = data;
    salesChart.update();
}

// Export functionality
function showExportModal() {
    document.getElementById('exportModal').classList.add('show');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.remove('show');
}

// Export option selection
document.querySelectorAll('.export-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.export-option').forEach(opt => opt.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input[type="radio"]').checked = true;
    });
});

function exportReport() {
    const selectedFormat = document.querySelector('input[name="export_format"]:checked')?.value;
    
    if (!selectedFormat) {
        AdminPanel.showToast('Lütfen bir format seçin!', 'warning');
        return;
    }
    
    // Here you would implement actual export functionality
    AdminPanel.showToast(`Rapor ${selectedFormat.toUpperCase()} formatında indiriliyor...`, 'success');
    closeExportModal();
}

// Reset filters
function resetFilters() {
    document.getElementById('reportFilters').reset();
    document.getElementById('reportFilters').submit();
}

// Close modal on outside click
document.getElementById('exportModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeExportModal();
    }
});
</script>
@endpush