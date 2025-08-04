@extends('layouts.admin')

@section('title', 'Kuponlar')
@section('header-title', 'Kupon Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/coupons.css') }}">
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Kupon Yönetimi</h1>
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
        <span class="breadcrumb-separator">/</span>
        <span>Kuponlar</span>
    </div>
</div>

<!-- Page Actions -->
<div class="page-actions">
    <div class="page-actions-left">
        <!-- Search -->
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Kupon kodu ara..." id="couponSearch">
        </div>
    </div>
    
    <!-- Add New Coupon -->
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal" 
            style="background: var(--primary-red); border-color: var(--primary-red);">
        <i class="bi bi-plus-circle"></i>
        Yeni Kupon
    </button>
</div>

<!-- Coupon Stats -->
<div class="coupon-stats">
    <!-- Total Coupons -->
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi bi-ticket-perforated"></i>
        </div>
        <div class="stat-value">{{ $coupons->count() }}</div>
        <div class="stat-label">Toplam Kupon</div>
    </div>
    
    <!-- Active Coupons -->
    <div class="stat-card active">
        <div class="stat-icon">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-value">{{ $coupons->where('active', true)->count() }}</div>
        <div class="stat-label">Aktif Kupon</div>
    </div>
    
    <!-- Used Count -->
    <div class="stat-card used">
        <div class="stat-icon">
            <i class="bi bi-bag-check"></i>
        </div>
        <div class="stat-value">{{ $coupons->sum('used_count') }}</div>
        <div class="stat-label">Toplam Kullanım</div>
    </div>
    
    <!-- Expired Coupons -->
    <div class="stat-card expired">
        <div class="stat-icon">
            <i class="bi bi-calendar-x"></i>
        </div>
        <div class="stat-value">{{ $coupons->filter(function($c) { return $c->expires_at && $c->expires_at->isPast(); })->count() }}</div>
        <div class="stat-label">Süresi Dolan</div>
    </div>
</div>

<!-- Coupons Table -->
<div class="coupons-table-card">
    <div class="table-header">
        <h3 class="table-title">Kupon Listesi</h3>
    </div>
    
    <div class="table-wrapper">
        <table class="coupons-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kod</th>
                    <th>Tür</th>
                    <th>Değer</th>
                    <th>Min. Tutar</th>
                    <th>Kullanım</th>
                    <th>Son Kullanım</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                    <tr data-coupon="{{ strtolower($coupon->code) }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span class="coupon-code">{{ $coupon->code }}</span>
                            <button class="action-btn copy ms-2" onclick="copyCouponCode('{{ $coupon->code }}')" title="Kopyala">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </td>
                        <td>
                            @if($coupon->type === 'fixed')
                                <span class="type-badge fixed">
                                    <i class="bi bi-currency-dollar"></i>
                                    Sabit Tutar
                                </span>
                            @elseif($coupon->type === 'percent')
                                <span class="type-badge percent">
                                    <i class="bi bi-percent"></i>
                                    Yüzde
                                </span>
                            @else
                                <span class="type-badge shipping">
                                    <i class="bi bi-truck"></i>
                                    Ücretsiz Kargo
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($coupon->type === 'percent')
                                %{{ $coupon->value }}
                            @elseif($coupon->type === 'fixed')
                                ₺{{ number_format($coupon->value, 2, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($coupon->min_order_amount)
                                ₺{{ number_format($coupon->min_order_amount, 2, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <div class="usage-progress">
                                @if($coupon->usage_limit)
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ ($coupon->used_count / $coupon->usage_limit) * 100 }}%"></div>
                                    </div>
                                    <span class="usage-text">{{ $coupon->used_count }}/{{ $coupon->usage_limit }}</span>
                                @else
                                    <span class="usage-text">{{ $coupon->used_count }} kullanım</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($coupon->expires_at)
                                @if($coupon->expires_at->isPast())
                                    <span class="text-danger">
                                        <i class="bi bi-exclamation-circle"></i>
                                        {{ $coupon->expires_at->format('d.m.Y') }}
                                    </span>
                                @else
                                    {{ $coupon->expires_at->format('d.m.Y') }}
                                @endif
                            @else
                                <span class="text-muted">Süresiz</span>
                            @endif
                        </td>
                        <td>
                            @if($coupon->expires_at && $coupon->expires_at->isPast())
                                <span class="status-badge expired">
                                    <i class="bi bi-x-circle"></i>
                                    Süresi Doldu
                                </span>
                            @elseif($coupon->active)
                                <span class="status-badge active">
                                    <i class="bi bi-check-circle"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="status-badge inactive">
                                    <i class="bi bi-pause-circle"></i>
                                    Pasif
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="action-btn edit" data-bs-toggle="modal" data-bs-target="#editCouponModal{{ $coupon->id }}" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.coupons.toggle', $coupon->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="action-btn {{ $coupon->active ? 'deactivate' : 'activate' }}" 
                                            title="{{ $coupon->active ? 'Pasifleştir' : 'Aktifleştir' }}">
                                        <i class="bi bi-{{ $coupon->active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Bu kuponu silmek istediğinizden emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete" title="Sil">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-ticket-perforated empty-icon"></i>
                                <h3 class="empty-title">Henüz Kupon Yok</h3>
                                <p class="empty-text">İlk kuponunuzu oluşturmak için yukarıdaki butonu kullanın.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($coupons->hasPages())
        <div class="pagination-wrapper">
            {{ $coupons->links('components.admin-pagination') }}
        </div>
    @endif
</div>

<!-- Add Coupon Modal -->
<div class="modal fade" id="addCouponModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Yeni Kupon Oluştur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Basic Info -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-info-circle"></i>
                            Temel Bilgiler
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kupon Kodu</label>
                                    <input type="text" name="code" class="form-control" required 
                                           placeholder="Örn: INDIRIM2025" style="text-transform: uppercase;">
                                    <small class="text-muted">Büyük/küçük harf duyarlı değildir</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">İndirim Türü</label>
                                    <select name="type" class="form-control" required onchange="updateValueField(this)">
                                        <option value="fixed">Sabit Tutar (₺)</option>
                                        <option value="percent">Yüzde İndirim (%)</option>
                                        <option value="free_shipping">Ücretsiz Kargo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Discount Value -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-tag"></i>
                            İndirim Değeri ve Koşullar
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">İndirim Değeri</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="value" 
                                               class="form-control" required placeholder="0.00" id="valueInput">
                                        <span class="input-group-text" id="valueSymbol">₺</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Min. Sipariş Tutarı</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="min_order_amount" 
                                               class="form-control" placeholder="0.00">
                                        <span class="input-group-text">₺</span>
                                    </div>
                                    <small class="text-muted">Boş bırakılırsa sınır yok</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Kullanım Limiti</label>
                                    <input type="number" min="1" name="usage_limit" 
                                           class="form-control" placeholder="Sınırsız">
                                    <small class="text-muted">Boş bırakılırsa sınırsız</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Validity -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-calendar"></i>
                            Geçerlilik Süresi
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Son Kullanım Tarihi</label>
                                    <input type="date" name="expires_at" class="form-control">
                                    <small class="text-muted">Boş bırakılırsa süresiz</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kupon Durumu</label>
                                    <select name="active" class="form-control" required>
                                        <option value="1" selected>Aktif</option>
                                        <option value="0">Pasif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Products -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-box-seam"></i>
                            Geçerli Ürünler
                        </h6>
                        <div class="form-group">
                            <label class="form-label">
                                Ürün Seçimi
                                <small class="text-muted">(Ctrl ile çoklu seçim)</small>
                            </label>
                            <select name="product_ids[]" class="form-control product-select" multiple>
                                @foreach($products ?? [] as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Seçim yapılmazsa tüm ürünlerde geçerli olur</small>
                        </div>
                    </div>
                    
                    <div class="info-message">
                        <i class="bi bi-info-circle-fill"></i>
                        <div class="info-message-content">
                            <div class="info-message-title">İpucu</div>
                            <div class="info-message-text">
                                Kupon oluşturduktan sonra müşterilerinizle paylaşabilir ve kullanım durumunu takip edebilirsiniz.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary-red); border-color: var(--primary-red);">
                        <i class="bi bi-check-lg me-1"></i>
                        Kupon Oluştur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Coupon Modals -->
@foreach($coupons as $coupon)
<div class="modal fade" id="editCouponModal{{ $coupon->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Kupon Düzenle: <span class="badge bg-light text-dark">{{ $coupon->code }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Basic Info -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-info-circle"></i>
                            Temel Bilgiler
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kupon Kodu</label>
                                    <input type="text" name="code" class="form-control" required 
                                           value="{{ $coupon->code }}" style="text-transform: uppercase;">
                                    <small class="text-muted">Büyük/küçük harf duyarlı değildir</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">İndirim Türü</label>
                                    <select name="type" class="form-control" required onchange="updateValueField(this, {{ $coupon->id }})">
                                        <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Sabit Tutar (₺)</option>
                                        <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>Yüzde İndirim (%)</option>
                                        <option value="free_shipping" {{ $coupon->type == 'free_shipping' ? 'selected' : '' }}>Ücretsiz Kargo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Discount Value -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-tag"></i>
                            İndirim Değeri ve Koşullar
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">İndirim Değeri</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="value" 
                                               class="form-control" required value="{{ $coupon->value }}" 
                                               id="valueInput{{ $coupon->id }}">
                                        <span class="input-group-text" id="valueSymbol{{ $coupon->id }}">
                                            {{ $coupon->type == 'percent' ? '%' : '₺' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Min. Sipariş Tutarı</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="min_order_amount" 
                                               class="form-control" value="{{ $coupon->min_order_amount }}">
                                        <span class="input-group-text">₺</span>
                                    </div>
                                    <small class="text-muted">Boş bırakılırsa sınır yok</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Kullanım Limiti</label>
                                    <input type="number" min="1" name="usage_limit" 
                                           class="form-control" value="{{ $coupon->usage_limit }}">
                                    <small class="text-muted">Boş bırakılırsa sınırsız</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Validity -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-calendar"></i>
                            Geçerlilik Süresi
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Son Kullanım Tarihi</label>
                                    <input type="date" name="expires_at" class="form-control"
                                           value="{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '' }}">
                                    <small class="text-muted">Boş bırakılırsa süresiz</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kupon Durumu</label>
                                    <select name="active" class="form-control" required>
                                        <option value="1" {{ $coupon->active ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ !$coupon->active ? 'selected' : '' }}>Pasif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Products -->
                    <div class="form-section">
                        <h6 class="form-section-title">
                            <i class="bi bi-box-seam"></i>
                            Geçerli Ürünler
                        </h6>
                        <div class="form-group">
                            <label class="form-label">
                                Ürün Seçimi
                                <small class="text-muted">(Ctrl ile çoklu seçim)</small>
                            </label>
                            <select name="product_ids[]" class="form-control product-select" multiple>
                                @php
                                    $selectedProducts = $coupon->products->pluck('id')->toArray();
                                @endphp
                                @foreach($products ?? [] as $product)
                                    <option value="{{ $product->id }}" 
                                            {{ in_array($product->id, $selectedProducts) ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Seçim yapılmazsa tüm ürünlerde geçerli olur</small>
                        </div>
                    </div>
                    
                    <!-- Usage Info -->
                    <div class="info-message">
                        <i class="bi bi-info-circle-fill"></i>
                        <div class="info-message-content">
                            <div class="info-message-title">Kullanım Bilgisi</div>
                            <div class="info-message-text">
                                Bu kupon şu ana kadar <strong>{{ $coupon->used_count }}</strong> kez kullanıldı
                                @if($coupon->usage_limit)
                                    (Limit: {{ $coupon->usage_limit }})
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary" style="background: var(--primary-red); border-color: var(--primary-red);">
                        <i class="bi bi-check-lg me-1"></i>
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Copy Notification -->
<div class="copy-notification" id="copyNotification">
    <i class="bi bi-check-circle"></i>
    <span>Kupon kodu kopyalandı!</span>
</div>
@endsection

@push('scripts')
<script>
// Search functionality
let searchTimer;
document.getElementById('couponSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const rows = document.querySelectorAll('tbody tr[data-coupon]');
        
        rows.forEach(row => {
            const couponCode = row.dataset.coupon;
            if (couponCode.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }, 300);
});

// Update value field based on type
function updateValueField(selectElement, couponId = '') {
    const suffix = couponId ? couponId : '';
    const valueInput = document.getElementById('valueInput' + suffix);
    const valueSymbol = document.getElementById('valueSymbol' + suffix);
    
    switch(selectElement.value) {
        case 'percent':
            valueSymbol.textContent = '%';
            valueInput.setAttribute('max', '100');
            valueInput.setAttribute('placeholder', '0-100');
            valueInput.removeAttribute('readonly');
            break;
        case 'fixed':
            valueSymbol.textContent = '₺';
            valueInput.removeAttribute('max');
            valueInput.setAttribute('placeholder', '0.00');
            valueInput.removeAttribute('readonly');
            break;
        case 'free_shipping':
            valueSymbol.textContent = '🚚';
            valueInput.value = '0';
            valueInput.setAttribute('readonly', 'readonly');
            break;
    }
}

// Copy coupon code
function copyCouponCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        const notification = document.getElementById('copyNotification');
        notification.classList.add('show');
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 2000);
    });
}

// Initialize value fields on page load
document.addEventListener('DOMContentLoaded', function() {
    // For edit modals
    document.querySelectorAll('select[name="type"]').forEach(select => {
        if (select.closest('.modal')) {
            const couponId = select.closest('.modal').id.replace('editCouponModal', '');
            updateValueField(select, couponId);
        }
    });
});
</script>
@endpush