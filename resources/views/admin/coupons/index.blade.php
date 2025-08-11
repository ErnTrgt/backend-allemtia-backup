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
                                <button class="action-btn edit" onclick="editCoupon({{ $coupon->id }})" title="Düzenle">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button onclick="toggleCoupon({{ $coupon->id }}, {{ $coupon->active ? 'false' : 'true' }})" 
                                        class="action-btn {{ $coupon->active ? 'deactivate' : 'activate' }}" 
                                        title="{{ $coupon->active ? 'Pasifleştir' : 'Aktifleştir' }}">
                                    <i class="bi bi-{{ $coupon->active ? 'pause' : 'play' }}"></i>
                                </button>
                                <button onclick="deleteCoupon({{ $coupon->id }})" 
                                        class="action-btn delete" title="Sil">
                                    <i class="bi bi-trash"></i>
                                </button>
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
            <form id="addCouponForm" action="{{ route('admin.coupons.store') }}" method="POST" onsubmit="handleCouponAdd(event)">
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

<!-- Edit Coupon Modal (Dynamic) -->
<div class="modal fade" id="editCouponModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>
                    Kupon Düzenle: <span id="editCouponCode" class="badge bg-light text-dark"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">×</button>
            </div>
            <form id="editCouponForm" method="POST" onsubmit="handleCouponEdit(event)">
                @csrf
                @method('PUT')
                <input type="hidden" id="editCouponId" name="coupon_id">
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
                                    <input type="text" name="code" id="editCode" class="form-control" required 
                                           style="text-transform: uppercase;">
                                    <small class="text-muted">Büyük/küçük harf duyarlı değildir</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">İndirim Türü</label>
                                    <select name="type" id="editType" class="form-control" required onchange="updateEditValueField(this)">
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
                                               id="editValue" class="form-control" required>
                                        <span class="input-group-text" id="editValueSymbol">₺</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Min. Sipariş Tutarı</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="min_order_amount" 
                                               id="editMinOrderAmount" class="form-control">
                                        <span class="input-group-text">₺</span>
                                    </div>
                                    <small class="text-muted">Boş bırakılırsa sınır yok</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Kullanım Limiti</label>
                                    <input type="number" min="1" name="usage_limit" 
                                           id="editUsageLimit" class="form-control">
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
                                    <input type="date" name="expires_at" id="editExpiresAt" class="form-control">
                                    <small class="text-muted">Boş bırakılırsa süresiz</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Kupon Durumu</label>
                                    <select name="active" id="editActive" class="form-control" required>
                                        <option value="1">Aktif</option>
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
                            <select name="product_ids[]" id="editProductIds" class="form-control product-select" multiple>
                                @foreach($products ?? [] as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Seçim yapılmazsa tüm ürünlerde geçerli olur</small>
                        </div>
                    </div>
                    
                    <!-- Usage Info -->
                    <div class="info-message" id="editUsageInfo" style="display: none;">
                        <i class="bi bi-info-circle-fill"></i>
                        <div class="info-message-content">
                            <div class="info-message-title">Kullanım Bilgisi</div>
                            <div class="info-message-text" id="editUsageText"></div>
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
    // Initialize add modal type field
    const addTypeSelect = document.querySelector('#addCouponModal select[name="type"]');
    if (addTypeSelect) {
        updateValueField(addTypeSelect);
    }
});

// Handle Coupon Add with AJAX
function handleCouponAdd(event) {
    event.preventDefault();
    
    const form = document.getElementById('addCouponForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Ekleniyor...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addCouponModal'));
            if (modal) {
                modal.hide();
            }
            
            // Add new coupon to table
            addCouponToTable(data.coupon);
            
            // Update stats
            updateCouponStats();
            
            // Show success toast
            showSuccessToast('Kupon başarıyla eklendi!');
            
            // Reset form
            form.reset();
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        } else {
            showErrorToast(data.message || 'Kupon eklenirken bir hata oluştu!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Kupon eklenirken bir hata oluştu. Lütfen tekrar deneyin.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Edit Coupon - Load Data
function editCoupon(couponId) {
    // Show loading
    showLoadingToast('Kupon bilgileri yükleniyor...');
    
    // Get coupon data via AJAX
    fetch(`/admin/coupons/${couponId}/edit-ajax`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const coupon = data.coupon;
            
            // Fill the form
            document.getElementById('editCouponId').value = coupon.id;
            document.getElementById('editCouponCode').textContent = coupon.code;
            document.getElementById('editCode').value = coupon.code;
            document.getElementById('editType').value = coupon.type;
            document.getElementById('editValue').value = coupon.value;
            document.getElementById('editMinOrderAmount').value = coupon.min_order_amount || '';
            document.getElementById('editUsageLimit').value = coupon.usage_limit || '';
            document.getElementById('editExpiresAt').value = coupon.expires_at ? coupon.expires_at.split('T')[0] : '';
            document.getElementById('editActive').value = coupon.active ? '1' : '0';
            
            // Update value field based on type
            updateEditValueField(document.getElementById('editType'));
            
            // Set selected products
            const productSelect = document.getElementById('editProductIds');
            const selectedProductIds = coupon.products ? coupon.products.map(p => p.id.toString()) : [];
            Array.from(productSelect.options).forEach(option => {
                option.selected = selectedProductIds.includes(option.value);
            });
            
            // Show usage info
            if (coupon.used_count > 0) {
                document.getElementById('editUsageInfo').style.display = 'block';
                let usageText = `Bu kupon şu ana kadar <strong>${coupon.used_count}</strong> kez kullanıldı`;
                if (coupon.usage_limit) {
                    usageText += ` (Limit: ${coupon.usage_limit})`;
                }
                document.getElementById('editUsageText').innerHTML = usageText;
            } else {
                document.getElementById('editUsageInfo').style.display = 'none';
            }
            
            // Set form action
            document.getElementById('editCouponForm').action = `/admin/coupons/${coupon.id}`;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editCouponModal'));
            modal.show();
            
            hideLoadingToast();
        } else {
            showErrorToast(data.message || 'Kupon bilgileri yüklenemedi!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Kupon bilgileri yüklenirken hata oluştu!');
    });
}

// Handle Coupon Edit with AJAX
function handleCouponEdit(event) {
    event.preventDefault();
    
    const form = document.getElementById('editCouponForm');
    const couponId = document.getElementById('editCouponId').value;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Güncelleniyor...';
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editCouponModal'));
            if (modal) {
                modal.hide();
            }
            
            // Update coupon in table
            updateCouponInTable(data.coupon);
            
            // Update stats
            updateCouponStats();
            
            // Show success toast
            showSuccessToast('Kupon başarıyla güncellendi!');
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        } else {
            showErrorToast(data.message || 'Kupon güncellenirken bir hata oluştu!');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Kupon güncellenirken bir hata oluştu. Lütfen tekrar deneyin.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
    });
}

// Update value field for edit modal
function updateEditValueField(selectElement) {
    const valueInput = document.getElementById('editValue');
    const valueSymbol = document.getElementById('editValueSymbol');
    
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

// Toggle Coupon Status with AJAX
function toggleCoupon(couponId, activate) {
    const button = event.target.closest('button');
    const originalIcon = button.innerHTML;
    
    // Show loading
    button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
    button.disabled = true;
    
    fetch(`/admin/coupons/${couponId}/toggle`, {
        method: 'PUT',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ active: activate })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update button
            const isActive = data.coupon.active;
            button.className = `action-btn ${isActive ? 'deactivate' : 'activate'}`;
            button.title = isActive ? 'Pasifleştir' : 'Aktifleştir';
            button.innerHTML = `<i class="bi bi-${isActive ? 'pause' : 'play'}"></i>`;
            button.onclick = () => toggleCoupon(couponId, !isActive);
            
            // Update status badge
            updateCouponStatus(couponId, isActive);
            
            // Update stats
            updateCouponStats();
            
            // Show success toast
            const message = isActive ? 'Kupon aktifleştirildi!' : 'Kupon pasifleştirildi!';
            showSuccessToast(message);
        } else {
            button.innerHTML = originalIcon;
            showErrorToast(data.message || 'İşlem yapılırken bir hata oluştu!');
        }
        button.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        button.innerHTML = originalIcon;
        button.disabled = false;
        showErrorToast('İşlem yapılırken bir hata oluştu. Lütfen tekrar deneyin.');
    });
}

// Delete Coupon with AJAX
function deleteCoupon(couponId) {
    if (confirm('Bu kuponu silmek istediğinizden emin misiniz?')) {
        const button = event.target.closest('button');
        const originalIcon = button.innerHTML;
        
        // Show loading
        button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        button.disabled = true;
        
        fetch(`/admin/coupons/${couponId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Remove coupon from table
                removeCouponFromTable(couponId);
                
                // Update stats
                updateCouponStats();
                
                // Show success toast
                showSuccessToast('Kupon başarıyla silindi!');
            } else {
                button.innerHTML = originalIcon;
                button.disabled = false;
                showErrorToast(data.message || 'Kupon silinirken bir hata oluştu!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.innerHTML = originalIcon;
            button.disabled = false;
            showErrorToast('Kupon silinirken bir hata oluştu. Lütfen tekrar deneyin.');
        });
    }
}

// Add coupon to table
function addCouponToTable(coupon) {
    const tbody = document.querySelector('.coupons-table tbody');
    const emptyState = tbody.querySelector('.empty-state');
    if (emptyState) {
        emptyState.closest('tr').remove();
    }
    
    const newRow = createCouponRow(coupon);
    tbody.insertAdjacentHTML('afterbegin', newRow);
    
    // Update row numbers
    updateRowNumbers();
    
    // Add animation
    const row = tbody.querySelector('tr:first-child');
    row.style.animation = 'slideIn 0.5s ease';
}

// Update row numbers
function updateRowNumbers() {
    const rows = document.querySelectorAll('.coupons-table tbody tr[data-coupon]');
    rows.forEach((row, index) => {
        const numberCell = row.querySelector('td:first-child');
        if (numberCell) {
            numberCell.textContent = index + 1;
        }
    });
}

// Update coupon in table
function updateCouponInTable(coupon) {
    const rows = document.querySelectorAll('.coupons-table tbody tr');
    let couponFound = false;
    
    rows.forEach(row => {
        // Find row by edit button onclick attribute
        const editBtn = row.querySelector(`button[onclick*="editCoupon(${coupon.id})"]`);
        if (editBtn) {
            couponFound = true;
            
            // Update data-coupon attribute
            row.setAttribute('data-coupon', coupon.code.toLowerCase());
            
            // Update code
            const codeEl = row.querySelector('.coupon-code');
            if (codeEl) codeEl.textContent = coupon.code;
            
            // Update type badge
            const typeEl = row.querySelector('.type-badge');
            if (typeEl) {
                typeEl.className = `type-badge ${coupon.type}`;
                if (coupon.type === 'fixed') {
                    typeEl.innerHTML = '<i class="bi bi-currency-dollar"></i> Sabit Tutar';
                } else if (coupon.type === 'percent') {
                    typeEl.innerHTML = '<i class="bi bi-percent"></i> Yüzde';
                } else {
                    typeEl.innerHTML = '<i class="bi bi-truck"></i> Ücretsiz Kargo';
                }
            }
            
            // Update value
            const valueEl = row.querySelector('td:nth-child(4)');
            if (valueEl) {
                if (coupon.type === 'percent') {
                    valueEl.textContent = `%${coupon.value}`;
                } else if (coupon.type === 'fixed') {
                    valueEl.textContent = `₺${parseFloat(coupon.value).toLocaleString('tr-TR', { minimumFractionDigits: 2 })}`;
                } else {
                    valueEl.textContent = '-';
                }
            }
            
            // Update min order amount
            const minOrderEl = row.querySelector('td:nth-child(5)');
            if (minOrderEl) {
                minOrderEl.textContent = coupon.min_order_amount 
                    ? `₺${parseFloat(coupon.min_order_amount).toLocaleString('tr-TR', { minimumFractionDigits: 2 })}`
                    : '-';
            }
            
            // Update usage
            const usageEl = row.querySelector('td:nth-child(6) .usage-progress');
            if (usageEl) {
                if (coupon.usage_limit) {
                    const percentage = (coupon.used_count / coupon.usage_limit) * 100;
                    usageEl.innerHTML = `
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${percentage}%"></div>
                        </div>
                        <span class="usage-text">${coupon.used_count}/${coupon.usage_limit}</span>
                    `;
                } else {
                    usageEl.innerHTML = `<span class="usage-text">${coupon.used_count} kullanım</span>`;
                }
            }
            
            // Update expiry date
            const expiryEl = row.querySelector('td:nth-child(7)');
            if (expiryEl) {
                if (coupon.expires_at) {
                    const expiryDate = new Date(coupon.expires_at);
                    const isPast = expiryDate < new Date();
                    if (isPast) {
                        expiryEl.innerHTML = `
                            <span class="text-danger">
                                <i class="bi bi-exclamation-circle"></i>
                                ${expiryDate.toLocaleDateString('tr-TR')}
                            </span>
                        `;
                    } else {
                        expiryEl.textContent = expiryDate.toLocaleDateString('tr-TR');
                    }
                } else {
                    expiryEl.innerHTML = '<span class="text-muted">Süresiz</span>';
                }
            }
            
            // Update status badge
            const statusEl = row.querySelector('td:nth-child(8) .status-badge');
            if (statusEl) {
                const isExpired = coupon.expires_at && new Date(coupon.expires_at) < new Date();
                if (isExpired) {
                    statusEl.className = 'status-badge expired';
                    statusEl.innerHTML = '<i class="bi bi-x-circle"></i> Süresi Doldu';
                } else if (coupon.active) {
                    statusEl.className = 'status-badge active';
                    statusEl.innerHTML = '<i class="bi bi-check-circle"></i> Aktif';
                } else {
                    statusEl.className = 'status-badge inactive';
                    statusEl.innerHTML = '<i class="bi bi-pause-circle"></i> Pasif';
                }
            }
            
            // Update action buttons
            const toggleBtn = row.querySelector(`button[onclick*="toggleCoupon(${coupon.id}"]`);
            if (toggleBtn) {
                toggleBtn.className = `action-btn ${coupon.active ? 'deactivate' : 'activate'}`;
                toggleBtn.title = coupon.active ? 'Pasifleştir' : 'Aktifleştir';
                toggleBtn.innerHTML = `<i class="bi bi-${coupon.active ? 'pause' : 'play'}"></i>`;
                toggleBtn.onclick = () => toggleCoupon(coupon.id, !coupon.active);
            }
            
            // Add animation
            row.style.background = 'rgba(16, 185, 129, 0.1)';
            setTimeout(() => {
                row.style.background = '';
            }, 1000);
        }
    });
    
    // If coupon not found in table, add it
    if (!couponFound) {
        addCouponToTable(coupon);
    }
}

// Update coupon status badge
function updateCouponStatus(couponId, isActive) {
    const rows = document.querySelectorAll('.coupons-table tbody tr');
    rows.forEach(row => {
        const toggleBtn = row.querySelector(`button[onclick*="toggleCoupon(${couponId}"]`);
        if (toggleBtn) {
            const statusEl = row.querySelector('td:nth-child(8) .status-badge');
            if (statusEl) {
                if (isActive) {
                    statusEl.className = 'status-badge active';
                    statusEl.innerHTML = '<i class="bi bi-check-circle"></i> Aktif';
                } else {
                    statusEl.className = 'status-badge inactive';
                    statusEl.innerHTML = '<i class="bi bi-pause-circle"></i> Pasif';
                }
            }
        }
    });
}

// Remove coupon from table
function removeCouponFromTable(couponId) {
    const rows = document.querySelectorAll('.coupons-table tbody tr');
    rows.forEach(row => {
        const deleteBtn = row.querySelector(`button[onclick*="deleteCoupon(${couponId})"]`);
        if (deleteBtn) {
            // Fade out animation
            row.style.transition = 'opacity 0.3s ease';
            row.style.opacity = '0';
            
            setTimeout(() => {
                row.remove();
                
                // Update row numbers
                updateRowNumbers();
                
                // Check if table is empty
                const remainingRows = document.querySelectorAll('.coupons-table tbody tr[data-coupon]');
                if (remainingRows.length === 0) {
                    const tbody = document.querySelector('.coupons-table tbody');
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="bi bi-ticket-perforated empty-icon"></i>
                                    <h3 class="empty-title">Henüz Kupon Yok</h3>
                                    <p class="empty-text">İlk kuponunuzu oluşturmak için yukarıdaki butonu kullanın.</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            }, 300);
        }
    });
}

// Create coupon row HTML
function createCouponRow(coupon) {
    // Ensure used_count exists
    if (!coupon.used_count) coupon.used_count = 0;
    
    const typeHtml = coupon.type === 'fixed' 
        ? '<span class="type-badge fixed"><i class="bi bi-currency-dollar"></i> Sabit Tutar</span>'
        : coupon.type === 'percent'
        ? '<span class="type-badge percent"><i class="bi bi-percent"></i> Yüzde</span>'
        : '<span class="type-badge shipping"><i class="bi bi-truck"></i> Ücretsiz Kargo</span>';
    
    const valueHtml = coupon.type === 'percent' 
        ? `%${coupon.value}`
        : coupon.type === 'fixed'
        ? `₺${parseFloat(coupon.value).toLocaleString('tr-TR', { minimumFractionDigits: 2 })}`
        : '-';
    
    // Check if expired
    const isExpired = coupon.expires_at && new Date(coupon.expires_at) < new Date();
    const statusHtml = isExpired
        ? '<span class="status-badge expired"><i class="bi bi-x-circle"></i> Süresi Doldu</span>'
        : coupon.active
        ? '<span class="status-badge active"><i class="bi bi-check-circle"></i> Aktif</span>'
        : '<span class="status-badge inactive"><i class="bi bi-pause-circle"></i> Pasif</span>';
    
    const expiryHtml = coupon.expires_at 
        ? (isExpired 
            ? `<span class="text-danger"><i class="bi bi-exclamation-circle"></i> ${new Date(coupon.expires_at).toLocaleDateString('tr-TR')}</span>`
            : new Date(coupon.expires_at).toLocaleDateString('tr-TR'))
        : '<span class="text-muted">Süresiz</span>';
    
    return `
        <tr data-coupon="${coupon.code.toLowerCase()}">
            <td>1</td>
            <td>
                <span class="coupon-code">${coupon.code}</span>
                <button class="action-btn copy ms-2" onclick="copyCouponCode('${coupon.code}')" title="Kopyala">
                    <i class="bi bi-clipboard"></i>
                </button>
            </td>
            <td>${typeHtml}</td>
            <td>${valueHtml}</td>
            <td>${coupon.min_order_amount ? `₺${parseFloat(coupon.min_order_amount).toLocaleString('tr-TR', { minimumFractionDigits: 2 })}` : '-'}</td>
            <td>
                <div class="usage-progress">
                    ${coupon.usage_limit ? `
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${(coupon.used_count / coupon.usage_limit) * 100}%"></div>
                        </div>
                        <span class="usage-text">${coupon.used_count}/${coupon.usage_limit}</span>
                    ` : `<span class="usage-text">${coupon.used_count || 0} kullanım</span>`}
                </div>
            </td>
            <td>${expiryHtml}</td>
            <td>${statusHtml}</td>
            <td>
                <div class="d-flex gap-2">
                    <button class="action-btn edit" onclick="editCoupon(${coupon.id})" title="Düzenle">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button onclick="toggleCoupon(${coupon.id}, ${!coupon.active})" 
                            class="action-btn ${coupon.active ? 'deactivate' : 'activate'}" 
                            title="${coupon.active ? 'Pasifleştir' : 'Aktifleştir'}">
                        <i class="bi bi-${coupon.active ? 'pause' : 'play'}"></i>
                    </button>
                    <button onclick="deleteCoupon(${coupon.id})" 
                            class="action-btn delete" title="Sil">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
}

// Update coupon stats
function updateCouponStats() {
    fetch('/admin/coupons/stats', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update total coupons
        const totalEl = document.querySelector('.stat-card:nth-child(1) .stat-value');
        if (totalEl) totalEl.textContent = data.total;
        
        // Update active coupons
        const activeEl = document.querySelector('.stat-card.active .stat-value');
        if (activeEl) activeEl.textContent = data.active;
        
        // Update used count
        const usedEl = document.querySelector('.stat-card.used .stat-value');
        if (usedEl) usedEl.textContent = data.used;
        
        // Update expired coupons
        const expiredEl = document.querySelector('.stat-card.expired .stat-value');
        if (expiredEl) expiredEl.textContent = data.expired;
    })
    .catch(error => console.error('Error updating stats:', error));
}

// Show success toast
function showSuccessToast(message) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Show and auto-hide toast
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 3000
    });
    toast.show();
    
    // Remove toast element after it's hidden
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

// Show loading toast
let loadingToast = null;
function showLoadingToast(message) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'loading-toast';
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    ${message}
                </div>
            </div>
        </div>
    `;
    
    // Remove existing loading toast if any
    const existingToast = document.getElementById(toastId);
    if (existingToast) {
        existingToast.remove();
    }
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Show toast
    const toastEl = document.getElementById(toastId);
    loadingToast = new bootstrap.Toast(toastEl, {
        autohide: false
    });
    loadingToast.show();
}

// Hide loading toast
function hideLoadingToast() {
    if (loadingToast) {
        loadingToast.hide();
        setTimeout(() => {
            const toastEl = document.getElementById('loading-toast');
            if (toastEl) {
                toastEl.remove();
            }
        }, 300);
    }
}

// Show error toast
function showErrorToast(message) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    
    // Show and auto-hide toast
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, {
        autohide: true,
        delay: 5000
    });
    toast.show();
    
    // Remove toast element after it's hidden
    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush