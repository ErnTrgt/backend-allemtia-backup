@extends('layouts.admin-modern')

@section('title', 'Siparişler')
@section('header-title', 'Sipariş Yönetimi')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/orders.css') }}">
<style>
/* Force modal styles */
#editOrderModal{{ isset($orders) && $orders->count() > 0 ? $orders->first()->id : '' }} .modal-content,
[id^="editOrderModal"] .modal-content {
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(30px) !important;
    -webkit-backdrop-filter: blur(30px) !important;
}

[id^="editOrderModal"] .modal-header {
    background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%) !important;
}

[id^="editOrderModal"] .form-section {
    background: rgba(240, 248, 255, 0.3) !important;
}

[id^="editOrderModal"] .btn-close {
    font-size: 20px !important;
    line-height: 1 !important;
}
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Sipariş Yönetimi</h1>
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Ana Sayfa</a>
        <span class="breadcrumb-separator">/</span>
        <span>Siparişler</span>
    </div>
</div>

<!-- Order Statistics -->
<div class="order-stats">
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $orders->where('status', 'pending')->count() }}</div>
            <div class="stat-label">Bekleyen Siparişler</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon processing">
            <i class="bi bi-box-seam"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $orders->where('status', 'processing')->count() }}</div>
            <div class="stat-label">Hazırlanan Siparişler</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon completed">
            <i class="bi bi-check-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $orders->where('status', 'delivered')->count() }}</div>
            <div class="stat-label">Teslim Edilen</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon cancelled">
            <i class="bi bi-x-circle"></i>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ $orders->where('status', 'cancelled')->count() + $orders->where('is_partially_cancelled', true)->count() }}</div>
            <div class="stat-label">İptal Edilen</div>
        </div>
    </div>
</div>

<!-- Page Actions -->
<div class="page-actions">
    <div class="page-actions-left">
        <!-- Search -->
        <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Sipariş no veya müşteri ara..." id="orderSearch">
        </div>
        
        <!-- Status Filter -->
        <div class="filter-pills">
            <button class="filter-pill {{ !request('status') && !request('is_partially_cancelled') ? 'active' : '' }}" data-filter="all">
                Tümü
                @if($orders->count() > 0)
                <span class="count">{{ $orders->count() }}</span>
                @endif
            </button>
            <button class="filter-pill {{ request('status') == 'pending' ? 'active' : '' }}" data-filter="pending">
                Beklemede
                <span class="count">{{ $orders->where('status', 'pending')->count() }}</span>
            </button>
            <button class="filter-pill {{ request('status') == 'processing' ? 'active' : '' }}" data-filter="processing">
                Hazırlanıyor
                <span class="count">{{ $orders->where('status', 'processing')->count() }}</span>
            </button>
            <button class="filter-pill {{ request('status') == 'shipped' ? 'active' : '' }}" data-filter="shipped">
                Kargoda
                <span class="count">{{ $orders->where('status', 'shipped')->count() }}</span>
            </button>
            <button class="filter-pill {{ request('status') == 'delivered' ? 'active' : '' }}" data-filter="delivered">
                Teslim Edildi
                <span class="count">{{ $orders->where('status', 'delivered')->count() }}</span>
            </button>
            <button class="filter-pill {{ request('status') == 'cancelled' || request('is_partially_cancelled') ? 'active' : '' }}" data-filter="cancelled">
                İptal
                <span class="count">{{ $orders->where('status', 'cancelled')->count() + $orders->where('is_partially_cancelled', true)->count() }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="orders-table-container">
    <div class="table-header">
        <h3 class="table-title">Sipariş Listesi</h3>
    </div>
    
    <table class="orders-table">
        <thead>
            <tr>
                <th>Sipariş No</th>
                <th>Müşteri</th>
                <th>Ürünler</th>
                <th>Durum</th>
                <th>Ödeme</th>
                <th>Toplam</th>
                <th>Tarih</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                @php
                    $cancelledTotal = $order->items->where('is_cancelled', true)->sum('subtotal');
                    $currentTotal = $order->total - $cancelledTotal;
                    $hasCancelledItems = $order->items->where('is_cancelled', true)->count() > 0;
                    $allItemsCancelled = $hasCancelledItems && $order->items->count() === $order->items->where('is_cancelled', true)->count();
                    
                    $orderSellers = $order->items->map(function($item) {
                        return $item->product->user ?? null;
                    })->filter()->unique('id');
                @endphp
                <tr data-order-id="{{ $order->id }}" data-status="{{ $order->status }}">
                    <td>
                        <span class="order-id">#{{ $order->order_number }}</span>
                    </td>
                    <td>
                        <div class="customer-info">
                            <div class="customer-avatar">
                                {{ strtoupper(substr($order->customer_name, 0, 1)) }}
                            </div>
                            <div class="customer-details">
                                <div class="customer-name">{{ $order->customer_name }}</div>
                                <div class="customer-email">{{ $order->customer_email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="product-list">
                            @foreach($order->items->take(2) as $item)
                                <div class="product-item">
                                    <span class="product-quantity">{{ $item->quantity }}x</span>
                                    <span>{{ Str::limit($item->product->name ?? 'Ürün', 30) }}</span>
                                    @if($item->is_cancelled)
                                        <span class="badge badge-danger badge-sm">İptal</span>
                                    @endif
                                </div>
                            @endforeach
                            @if($order->items->count() > 2)
                                <small class="text-muted">+{{ $order->items->count() - 2 }} ürün daha</small>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($order->status === 'cancelled' || $allItemsCancelled)
                            <span class="status-badge status-cancelled">
                                <span class="status-dot"></span>
                                İptal Edildi
                            </span>
                        @elseif($order->is_partially_cancelled || ($hasCancelledItems && !$allItemsCancelled))
                            <span class="status-badge status-cancelled">
                                <span class="status-dot"></span>
                                Kısmi İptal
                            </span>
                        @else
                            @switch($order->status)
                                @case('pending')
                                    <span class="status-badge status-pending">
                                        <span class="status-dot"></span>
                                        Beklemede
                                    </span>
                                    @break
                                @case('waiting_payment')
                                    <span class="status-badge status-waiting-payment">
                                        <span class="status-dot"></span>
                                        Ödeme Bekliyor
                                    </span>
                                    @break
                                @case('paid')
                                    <span class="status-badge status-processing">
                                        <span class="status-dot"></span>
                                        Ödendi
                                    </span>
                                    @break
                                @case('processing')
                                    <span class="status-badge status-processing">
                                        <span class="status-dot"></span>
                                        Hazırlanıyor
                                    </span>
                                    @break
                                @case('shipped')
                                    <span class="status-badge status-shipped">
                                        <span class="status-dot"></span>
                                        Kargoda
                                    </span>
                                    @break
                                @case('delivered')
                                    <span class="status-badge status-delivered">
                                        <span class="status-dot"></span>
                                        Teslim Edildi
                                    </span>
                                    @break
                                @default
                                    <span class="status-badge">
                                        {{ ucfirst($order->status) }}
                                    </span>
                            @endswitch
                        @endif
                    </td>
                    <td>
                        <div class="payment-method">
                            <div class="payment-icon">
                                @switch($order->payment_method)
                                    @case('eft')
                                        <i class="bi bi-bank"></i>
                                        @break
                                    @case('cash_on_delivery')
                                        <i class="bi bi-cash"></i>
                                        @break
                                    @default
                                        <i class="bi bi-credit-card"></i>
                                @endswitch
                            </div>
                            <span>
                                @switch($order->payment_method)
                                    @case('eft') EFT/Havale @break
                                    @case('cash_on_delivery') Kapıda Nakit @break
                                    @default {{ ucfirst($order->payment_method) }}
                                @endswitch
                            </span>
                        </div>
                    </td>
                    <td>
                        @if($hasCancelledItems)
                            <div>
                                <del class="text-muted">₺{{ number_format($order->total, 2, ',', '.') }}</del>
                                <div class="order-total">₺{{ number_format($currentTotal, 2, ',', '.') }}</div>
                            </div>
                        @else
                            <div class="order-total">₺{{ number_format($order->total, 2, ',', '.') }}</div>
                        @endif
                    </td>
                    <td>
                        <small>{{ $order->created_at->format('d.m.Y') }}<br>{{ $order->created_at->format('H:i') }}</small>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="action-btn view" data-tooltip="Detaylar">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="action-btn edit" data-tooltip="Durum Güncelle" data-bs-toggle="modal" data-bs-target="#editOrderModal{{ $order->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="action-btn print" data-tooltip="Fatura Yazdır" onclick="printInvoice({{ $order->id }})">
                                <i class="bi bi-printer"></i>
                            </button>
                            <button class="action-btn delete" data-tooltip="Sil" onclick="deleteOrder({{ $order->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="bi bi-cart-x empty-icon"></i>
                            <h3 class="empty-title">Sipariş Bulunamadı</h3>
                            <p class="empty-text">Henüz hiç sipariş oluşturulmamış veya arama kriterlerinize uygun sipariş yok.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($orders->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $orders->links('components.admin-pagination') }}
</div>
@endif

<!-- Edit Order Modals -->
@foreach ($orders as $order)
<div class="modal fade" id="editOrderModal{{ $order->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                    Sipariş Güncelle: <span class="badge bg-light text-dark" style="margin-left: 8px; font-size: 14px; font-weight: normal;">#{{ $order->order_number }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form action="{{ route('admin.orders.update') }}" method="POST" id="updateOrderForm{{ $order->id }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body" style="padding: 24px;">
                    <!-- Sipariş Durumu -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-box-seam" style="color: #A90000;"></i>
                            Sipariş Durumu
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Durum</label>
                                    <select name="status" class="form-control" id="orderStatus{{ $order->id }}" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Beklemede</option>
                                        <option value="waiting_payment" {{ $order->status == 'waiting_payment' ? 'selected' : '' }}>Ödeme Bekleniyor</option>
                                        <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Ödendi</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Hazırlanıyor</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Kargoda</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>İptal Edildi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Kargo Takip No</label>
                                    <input type="text" name="tracking_number" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" 
                                           value="{{ $order->tracking_number ?? '' }}" 
                                           placeholder="Takip numarasını girin">
                                    <small class="text-muted">İsteğe bağlı</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notlar -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-chat-left-text" style="color: #A90000;"></i>
                            Notlar
                        </h6>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Durum Notu</label>
                                    <textarea name="status_note" class="form-control" rows="3" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;" 
                                              placeholder="Bu durum güncellemesi hakkında not ekleyin..."></textarea>
                                    <small class="text-muted">Bu not sipariş geçmişinde görünecektir</small>
                                </div>
                            </div>
                            
                            <!-- Cancel Reason (shown only when cancelled is selected) -->
                            <div class="col-12" id="cancelReasonGroup{{ $order->id }}" style="{{ $order->status == 'cancelled' ? 'display: block;' : 'display: none;' }}">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">İptal Nedeni</label>
                                    <textarea name="cancel_reason" class="form-control" rows="2" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;" 
                                              placeholder="İptal nedenini belirtin...">{{ $order->cancellation_reason ?? '' }}</textarea>
                                    <small class="text-muted">İptal nedeni müşteriyle paylaşılacaktır</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sipariş Özeti -->
                    <div class="form-section" style="background: rgba(240, 248, 255, 0.3); border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid rgba(0, 0, 0, 0.05);">
                        <h6 class="form-section-title" style="font-size: 16px; font-weight: 600; color: #374151; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-receipt" style="color: #A90000;"></i>
                            Sipariş Özeti
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Müşteri</label>
                                    <div class="form-control-static" style="padding: 8px 0; font-size: 14px; color: #111827; line-height: 1.5;">
                                        <strong>{{ $order->customer_name }}</strong><br>
                                        <small class="text-muted">{{ $order->customer_email }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Ödeme Yöntemi</label>
                                    <div class="form-control-static" style="padding: 8px 0; font-size: 14px; color: #111827; line-height: 1.5;">
                                        <strong>
                                            @switch($order->payment_method)
                                                @case('eft') EFT/Havale @break
                                                @case('cash_on_delivery') Kapıda Nakit @break
                                                @default {{ ucfirst($order->payment_method) }}
                                            @endswitch
                                        </strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Toplam Tutar</label>
                                    @php
                                        $modalCancelledTotal = $order->items->where('is_cancelled', true)->sum('subtotal');
                                        $modalCurrentTotal = $order->total - $modalCancelledTotal;
                                    @endphp
                                    <div class="form-control-static" style="padding: 8px 0; font-size: 14px; color: #111827; line-height: 1.5;">
                                        <strong class="text-primary" style="font-size: 18px; color: #A90000;">₺{{ number_format($modalCurrentTotal, 2, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-message" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 12px; margin-top: 20px;">
                        <i class="bi bi-info-circle-fill" style="color: #3B82F6; font-size: 20px; flex-shrink: 0;"></i>
                        <div class="info-message-content" style="flex: 1;">
                            <div class="info-message-title" style="font-weight: 600; color: #1f2937; margin-bottom: 2px;">Durum Güncelleme</div>
                            <div class="info-message-text" style="color: #4b5563; font-size: 14px;">
                                Sipariş durumu güncellendiğinde müşteriye otomatik bildirim gönderilecektir.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: linear-gradient(135deg, rgba(0, 0, 0, 0.02) 0%, rgba(0, 0, 0, 0.04) 100%); border-top: 1px solid rgba(0, 0, 0, 0.05); padding: 20px 24px; gap: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05) 0%, rgba(0, 0, 0, 0.08) 100%); color: #374151; border: 1px solid rgba(0, 0, 0, 0.1); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">İptal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; font-weight: 500; background: linear-gradient(135deg, #A90000 0%, #C1121F 100%); color: white; border: none; box-shadow: 0 4px 16px rgba(169, 0, 0, 0.25); display: inline-flex; align-items: center; gap: 4px; font-size: 14px;">
                        <i class="bi bi-check-lg me-1"></i>
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
// Force reload styles when modal opens
document.addEventListener('shown.bs.modal', function (event) {
    if (event.target.id && event.target.id.startsWith('editOrderModal')) {
        console.log('Order modal opened:', event.target.id);
        // Force redraw
        event.target.style.display = 'none';
        event.target.offsetHeight; // trigger reflow
        event.target.style.display = '';
    }
});

// Search functionality
let searchTimer;
document.getElementById('orderSearch').addEventListener('input', function(e) {
    clearTimeout(searchTimer);
    const query = e.target.value.toLowerCase();
    
    searchTimer = setTimeout(() => {
        const rows = document.querySelectorAll('.orders-table tbody tr');
        rows.forEach(row => {
            if (row.querySelector('.empty-state')) return;
            
            const orderNo = row.querySelector('.order-id')?.textContent.toLowerCase() || '';
            const customerName = row.querySelector('.customer-name')?.textContent.toLowerCase() || '';
            const customerEmail = row.querySelector('.customer-email')?.textContent.toLowerCase() || '';
            
            if (orderNo.includes(query) || customerName.includes(query) || customerEmail.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }, 300);
});

// Filter functionality
document.querySelectorAll('.filter-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        const filter = this.dataset.filter;
        
        // Update active state
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        
        // Update URL
        const url = new URL(window.location);
        if (filter === 'all') {
            url.searchParams.delete('status');
            url.searchParams.delete('is_partially_cancelled');
        } else if (filter === 'cancelled') {
            url.searchParams.set('status', 'cancelled');
        } else {
            url.searchParams.set('status', filter);
            url.searchParams.delete('is_partially_cancelled');
        }
        window.location = url.toString();
    });
});

// Show/hide cancel reason based on status
@foreach ($orders as $order)
    const orderStatus{{ $order->id }} = document.getElementById('orderStatus{{ $order->id }}');
    const cancelReasonGroup{{ $order->id }} = document.getElementById('cancelReasonGroup{{ $order->id }}');
    
    if (orderStatus{{ $order->id }} && cancelReasonGroup{{ $order->id }}) {
        orderStatus{{ $order->id }}.addEventListener('change', function() {
            if (this.value === 'cancelled') {
                cancelReasonGroup{{ $order->id }}.style.display = 'block';
            } else {
                cancelReasonGroup{{ $order->id }}.style.display = 'none';
            }
        });
    }
@endforeach

// Print invoice
function printInvoice(orderId) {
    const printUrl = `/admin/orders/${orderId}/invoice`;
    window.open(printUrl, '_blank');
}

// Delete order
function deleteOrder(orderId) {
    if (confirm('Bu siparişi silmek istediğinizden emin misiniz?')) {
        fetch(`/admin/orders/${orderId}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                AdminPanel.showToast('Sipariş başarıyla silindi', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                AdminPanel.showToast(data.message || 'Sipariş silinirken hata oluştu', 'error');
            }
        })
        .catch(error => {
            AdminPanel.showToast('Bir hata oluştu!', 'error');
        });
    }
}

// Initialize tooltips
document.querySelectorAll('[data-tooltip]').forEach(el => {
    el.title = el.dataset.tooltip;
});

// Debug modal triggers
document.addEventListener('DOMContentLoaded', function() {
    // Check if modals exist
    const modals = document.querySelectorAll('[id^="editOrderModal"]');
    console.log('Found ' + modals.length + ' order modals');
    
    // Manual modal trigger for edit buttons
    document.querySelectorAll('.action-btn.edit').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-bs-target');
            console.log('Trying to open modal: ' + modalId);
            const modal = document.querySelector(modalId);
            if (modal) {
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            } else {
                console.error('Modal not found: ' + modalId);
            }
        });
    });
});

// Show notifications
@if(session('success'))
    AdminPanel.showToast('{{ session('success') }}', 'success');
@endif

@if(session('error'))
    AdminPanel.showToast('{{ session('error') }}', 'error');
@endif

// Form submit with AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Add AJAX submission to all order update forms
    @foreach ($orders as $order)
    const form{{ $order->id }} = document.getElementById('updateOrderForm{{ $order->id }}');
    if (form{{ $order->id }}) {
        form{{ $order->id }}.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Güncelleniyor...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editOrderModal{{ $order->id }}'));
                    modal.hide();
                    
                    // Show toast or alert
                    if (typeof AdminPanel !== 'undefined' && AdminPanel.showToast) {
                        AdminPanel.showToast('Sipariş durumu başarıyla güncellendi', 'success');
                    } else {
                        alert('Sipariş durumu başarıyla güncellendi');
                    }
                    
                    // Reload page after 1.5 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Bir hata oluştu');
                }
            })
            .catch(error => {
                // Show error message
                if (typeof AdminPanel !== 'undefined' && AdminPanel.showToast) {
                    AdminPanel.showToast(error.message || 'Bir hata oluştu', 'error');
                } else {
                    alert(error.message || 'Bir hata oluştu');
                }
                
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }
    @endforeach
});
</script>
@endpush