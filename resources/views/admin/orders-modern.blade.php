@extends('layouts.admin-modern')

@section('title', 'Siparişler')
@section('header-title', 'Siparişler')

@section('content')
<div class="orders-container">
    <!-- Page Header Component -->
    <x-admin.page-header 
        title="Siparişler"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Siparişler']
        ]">
        <x-slot name="actions">
            <button class="btn btn-secondary" onclick="exportOrders()">
                <i class="bi bi-download me-2"></i>
                Dışa Aktar
            </button>
        </x-slot>
    </x-admin.page-header>
    
    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card total">
                <div class="stat-icon">
                    <i class="bi bi-cart-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($orders->count()) }}</h3>
                    <p>Toplam Sipariş</p>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card pending">
                <div class="stat-icon">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($orders->where('status', 'pending')->count()) }}</h3>
                    <p>Bekleyen</p>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card processing">
                <div class="stat-icon">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($orders->where('status', 'processing')->count()) }}</h3>
                    <p>İşleniyor</p>
                </div>
            </x-admin.glass-card>
        </div>
        <div class="col-md-3">
            <x-admin.glass-card class="stat-card delivered">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ number_format($orders->where('status', 'delivered')->count()) }}</h3>
                    <p>Teslim Edildi</p>
                </div>
            </x-admin.glass-card>
        </div>
    </div>
    
    <!-- Orders Table/Timeline -->
    <x-admin.glass-card class="table-card" :padding="false">
        <!-- Table Header Component -->
        <x-admin.table-header search-placeholder="Sipariş ara..." search-id="orderSearch">
            <x-slot name="filters">
                <button class="filter-btn active" data-filter="all">
                    <i class="bi bi-grid-3x3-gap me-1"></i>
                    Tümü
                </button>
                <button class="filter-btn" data-filter="pending">
                    <i class="bi bi-clock me-1"></i>
                    Bekleyen
                </button>
                <button class="filter-btn" data-filter="processing">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    İşleniyor
                </button>
                <button class="filter-btn" data-filter="shipped">
                    <i class="bi bi-truck me-1"></i>
                    Kargoda
                </button>
                <button class="filter-btn" data-filter="delivered">
                    <i class="bi bi-check-circle me-1"></i>
                    Teslim Edildi
                </button>
                <button class="filter-btn" data-filter="cancelled">
                    <i class="bi bi-x-circle me-1"></i>
                    İptal
                </button>
            </x-slot>
            
            <x-slot name="actions">
                <div class="view-toggle">
                    <button class="view-btn active" data-view="table" title="Tablo Görünüm">
                        <i class="bi bi-list-ul"></i>
                    </button>
                    <button class="view-btn" data-view="timeline" title="Zaman Çizelgesi">
                        <i class="bi bi-calendar-week"></i>
                    </button>
                </div>
            </x-slot>
        </x-admin.table-header>
        
        <!-- Table View -->
        <div id="tableView" class="table-responsive">
            <table class="table orders-table" id="ordersTable">
                <thead>
                    <tr>
                        <th>Sipariş No</th>
                        <th>Müşteri</th>
                        <th>Ürünler</th>
                        <th>Toplam</th>
                        <th>Durum</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr data-status="{{ $order->status }}" data-tracking="{{ $order->tracking_number ?? '' }}">
                        <td>
                            <div class="order-number">
                                <strong>#{{ $order->order_number }}</strong>
                                @if($order->is_partially_cancelled)
                                <span class="badge bg-warning ms-1">Kısmi İptal</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="customer-info">
                                <h6>{{ $order->customer_name }}</h6>
                                <span class="text-muted">{{ $order->customer_email }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="products-preview">
                                @foreach($order->items->take(3) as $item)
                                <div class="product-item">
                                    @if($item->product && $item->product->images->first())
                                    <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                         alt="{{ $item->product_name }}"
                                         class="product-thumb">
                                    @else
                                    <img src="/images/default-product.svg" 
                                         alt="{{ $item->product_name }}"
                                         class="product-thumb">
                                    @endif
                                </div>
                                @endforeach
                                @if($order->items->count() > 3)
                                <div class="more-products">
                                    +{{ $order->items->count() - 3 }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="order-amount">
                                <strong>₺{{ number_format($order->total, 2) }}</strong>
                                @if($order->payment_method)
                                <span class="payment-method">{{ $order->payment_method }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="status-badge {{ $order->status }}">
                                @switch($order->status)
                                    @case('pending')
                                        <i class="bi bi-clock me-1"></i>Bekliyor
                                        @break
                                    @case('processing')
                                        <i class="bi bi-arrow-repeat me-1"></i>İşleniyor
                                        @break
                                    @case('shipped')
                                        <i class="bi bi-truck me-1"></i>Kargoda
                                        @break
                                    @case('delivered')
                                        <i class="bi bi-check-circle me-1"></i>Teslim Edildi
                                        @break
                                    @case('cancelled')
                                        <i class="bi bi-x-circle me-1"></i>İptal
                                        @break
                                    @default
                                        {{ ucfirst($order->status) }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            <div class="order-date">
                                <div>{{ $order->created_at->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="btn-action" 
                                   title="Görüntüle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn-action" 
                                        onclick="updateOrderStatus({{ $order->id }}, '{{ $order->status }}', '{{ $order->tracking_number ?? '' }}')" 
                                        title="Durum Güncelle">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" 
                                   class="btn-action" 
                                   title="Fatura"
                                   target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                                @if(!in_array($order->status, ['delivered', 'cancelled']))
                                <button class="btn-action text-danger" 
                                        onclick="cancelOrder({{ $order->id }})" 
                                        title="İptal Et">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Timeline View -->
        <div id="timelineView" class="timeline-container" style="display: none;">
            <div class="timeline">
                @foreach($orders->sortByDesc('created_at') as $order)
                <div class="timeline-item" data-status="{{ $order->status }}">
                    <div class="timeline-marker {{ $order->status }}">
                        @switch($order->status)
                            @case('pending')
                                <i class="bi bi-clock-fill"></i>
                                @break
                            @case('processing')
                                <i class="bi bi-arrow-repeat"></i>
                                @break
                            @case('shipped')
                                <i class="bi bi-truck"></i>
                                @break
                            @case('delivered')
                                <i class="bi bi-check-circle-fill"></i>
                                @break
                            @case('cancelled')
                                <i class="bi bi-x-circle-fill"></i>
                                @break
                        @endswitch
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h5>Sipariş #{{ $order->order_number }}</h5>
                            <span class="timeline-date">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="timeline-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Müşteri:</strong> {{ $order->customer_name }}</p>
                                    <p class="mb-1"><small>{{ $order->customer_email }}</small></p>
                                </div>
                                <div class="col-md-4">
                                    <p class="mb-1"><strong>Toplam:</strong> ₺{{ number_format($order->total, 2) }}</p>
                                    <p class="mb-1"><small>{{ $order->items->count() }} ürün</small></p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        Detayları Gör
                                    </a>
                                </div>
                            </div>
                            @if($order->tracking_number)
                            <div class="tracking-info mt-2">
                                <i class="bi bi-geo-alt me-1"></i>
                                Takip No: {{ $order->tracking_number }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </x-admin.glass-card>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                    Sipariş Durumunu Güncelle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form id="updateStatusForm" method="POST" action="{{ route('admin.orders.update') }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="order_id" id="modalOrderId" value="">
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
                                    <select name="status" class="form-control" id="orderStatus" required style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;">
                                        <option value="pending">Beklemede</option>
                                        <option value="waiting_payment">Ödeme Bekleniyor</option>
                                        <option value="paid">Ödendi</option>
                                        <option value="processing">Hazırlanıyor</option>
                                        <option value="shipped">Kargoda</option>
                                        <option value="delivered">Teslim Edildi</option>
                                        <option value="cancelled">İptal Edildi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Kargo Takip No</label>
                                    <input type="text" name="tracking_number" class="form-control" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px;" 
                                           placeholder="Takip numarasını girin" id="trackingNumberInput">
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
                            <div class="col-12" id="cancelReasonDiv" style="display: none;">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">İptal Nedeni</label>
                                    <textarea name="cancel_reason" class="form-control" rows="2" style="width: 100%; padding: 8px 12px; background: rgba(255, 255, 255, 0.8); border: 2px solid rgba(0, 0, 0, 0.08); border-radius: 8px; font-size: 14px; resize: vertical;" 
                                              placeholder="İptal nedenini belirtin..."></textarea>
                                    <small class="text-muted">İptal nedeni müşteriyle paylaşılacaktır</small>
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

<style>
/* Orders Page Styles */
.orders-container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Stat Cards */
.stat-card {
    display: flex;
    align-items: center;
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

.stat-card.total .stat-icon {
    background: linear-gradient(135deg, var(--gray-600), var(--gray-700));
}

.stat-card.pending .stat-icon {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.stat-card.processing .stat-icon {
    background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
}

.stat-card.delivered .stat-icon {
    background: linear-gradient(135deg, #10B981, #059669);
}

.stat-card .stat-content h3 {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: var(--spacing-xs);
}

.stat-card .stat-content p {
    font-size: 14px;
    color: var(--gray-600);
    margin: 0;
}

/* Filter Buttons */
.filter-btn {
    padding: var(--spacing-sm) var(--spacing-md);
    background: transparent;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-size: 14px;
    color: var(--gray-700);
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
}

.filter-btn:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
}

.filter-btn.active {
    background: var(--primary-red);
    border-color: var(--primary-red);
    color: var(--white);
}

/* View Toggle */
.view-toggle {
    display: flex;
    background: var(--gray-100);
    border-radius: var(--radius-sm);
    padding: 2px;
}

.view-btn {
    padding: var(--spacing-xs) var(--spacing-sm);
    background: transparent;
    border: none;
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: calc(var(--radius-sm) - 2px);
}

.view-btn:hover {
    color: var(--gray-800);
}

.view-btn.active {
    background: var(--white);
    color: var(--primary-red);
    box-shadow: var(--shadow-sm);
}

/* Orders Table */
.orders-table {
    width: 100%;
}

.orders-table th {
    padding: var(--spacing-md);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
}

.orders-table td {
    padding: var(--spacing-md);
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.order-number strong {
    color: var(--primary-red);
    font-size: 14px;
}

.customer-info h6 {
    font-size: 14px;
    font-weight: 600;
    margin: 0;
    color: var(--gray-900);
}

.customer-info span {
    font-size: 12px;
}

/* Products Preview */
.products-preview {
    display: flex;
    align-items: center;
    gap: -10px;
}

.product-item {
    position: relative;
    z-index: 1;
}

.product-item:nth-child(2) {
    margin-left: -15px;
    z-index: 2;
}

.product-item:nth-child(3) {
    margin-left: -15px;
    z-index: 3;
}

.product-thumb {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-sm);
    object-fit: cover;
    border: 2px solid var(--white);
    box-shadow: var(--shadow-sm);
}

.more-products {
    margin-left: var(--spacing-sm);
    padding: var(--spacing-xs) var(--spacing-sm);
    background: var(--gray-100);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-600);
}

/* Order Amount */
.order-amount strong {
    display: block;
    font-size: 16px;
    color: var(--gray-900);
}

.payment-method {
    font-size: 11px;
    color: var(--gray-500);
    text-transform: uppercase;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 500;
}

.status-badge.pending {
    background: rgba(245, 158, 11, 0.1);
    color: #F59E0B;
}

.status-badge.processing {
    background: rgba(0, 81, 187, 0.1);
    color: var(--primary-blue);
}

.status-badge.shipped {
    background: rgba(63, 161, 221, 0.1);
    color: var(--secondary-blue);
}

.status-badge.delivered {
    background: rgba(16, 185, 129, 0.1);
    color: #10B981;
}

.status-badge.cancelled {
    background: rgba(239, 68, 68, 0.1);
    color: #EF4444;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: var(--spacing-xs);
}

.btn-action {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-action:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
    transform: scale(1.05);
    color: var(--gray-700);
}

.btn-action.text-danger:hover {
    background: rgba(239, 68, 68, 0.1);
    border-color: #EF4444;
    color: #EF4444;
}

/* Timeline View */
.timeline-container {
    padding: var(--spacing-xl);
}

.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--gray-200);
}

.timeline-item {
    position: relative;
    margin-bottom: var(--spacing-xl);
    opacity: 0;
    animation: fadeInUp 0.5s ease forwards;
}

.timeline-item:nth-child(1) { animation-delay: 0.1s; }
.timeline-item:nth-child(2) { animation-delay: 0.2s; }
.timeline-item:nth-child(3) { animation-delay: 0.3s; }
.timeline-item:nth-child(4) { animation-delay: 0.4s; }
.timeline-item:nth-child(5) { animation-delay: 0.5s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 14px;
    z-index: 1;
}

.timeline-marker.pending {
    background: #F59E0B;
}

.timeline-marker.processing {
    background: var(--primary-blue);
}

.timeline-marker.shipped {
    background: var(--secondary-blue);
}

.timeline-marker.delivered {
    background: #10B981;
}

.timeline-marker.cancelled {
    background: #EF4444;
}

.timeline-content {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    padding: var(--spacing-lg);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
}

.timeline-content:hover {
    box-shadow: var(--shadow-md);
    transform: translateX(5px);
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--spacing-md);
}

.timeline-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.timeline-date {
    font-size: 13px;
    color: var(--gray-500);
}

.timeline-body p {
    font-size: 14px;
    margin-bottom: var(--spacing-xs);
}

.tracking-info {
    padding: var(--spacing-sm);
    background: var(--gray-50);
    border-radius: var(--radius-sm);
    font-size: 13px;
    color: var(--gray-700);
}

/* Modal Styles */
.modal-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
}

/* Responsive */
@media (max-width: 768px) {
    .filter-group {
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: var(--spacing-sm);
    }
    
    .timeline-content {
        padding: var(--spacing-md);
    }
    
    .timeline-body .row {
        flex-direction: column;
        gap: var(--spacing-md);
    }
    
    .timeline-body .text-end {
        text-align: left !important;
    }
}
</style>
@endsection

@push('scripts')
<script>
// DataTable Initialization
$(document).ready(function() {
    $('#ordersTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [[5, 'desc']], // Sort by date
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Ara...",
            lengthMenu: "_MENU_ kayıt göster",
            paginate: {
                first: "İlk",
                last: "Son",
                next: "Sonraki",
                previous: "Önceki"
            },
            info: "_TOTAL_ kayıttan _START_ - _END_ gösteriliyor",
            infoEmpty: "Kayıt bulunamadı",
            zeroRecords: "Eşleşen kayıt bulunamadı"
        },
        dom: 'rtip'
    });
    
    // Custom search
    $('#orderSearch').on('keyup', function() {
        $('#ordersTable').DataTable().search(this.value).draw();
    });
});

// View Toggle
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const view = this.dataset.view;
        
        if (view === 'table') {
            document.getElementById('tableView').style.display = 'block';
            document.getElementById('timelineView').style.display = 'none';
        } else {
            document.getElementById('tableView').style.display = 'none';
            document.getElementById('timelineView').style.display = 'block';
        }
    });
});

// Filter Buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        const rows = document.querySelectorAll('#ordersTable tbody tr');
        const timelineItems = document.querySelectorAll('.timeline-item');
        
        rows.forEach(row => {
            if (filter === 'all') {
                row.style.display = '';
            } else {
                if (row.dataset.status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        timelineItems.forEach(item => {
            if (filter === 'all') {
                item.style.display = '';
            } else {
                if (item.dataset.status === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            }
        });
        
        // Redraw DataTable
        $('#ordersTable').DataTable().draw();
    });
});

// Update Order Status
function updateOrderStatus(orderId, currentStatus, trackingNumber) {
    const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    const form = document.getElementById('updateStatusForm');
    
    // Set order ID - DO NOT change form action
    document.getElementById('modalOrderId').value = orderId;
    
    // Set current status
    const statusSelect = document.getElementById('orderStatus');
    if (currentStatus) {
        statusSelect.value = currentStatus;
    }
    
    // Set tracking number if exists
    const trackingInput = document.getElementById('trackingNumberInput');
    if (trackingInput && trackingNumber) {
        trackingInput.value = trackingNumber;
    }
    
    // Show/hide fields based on current status
    const trackingDiv = document.getElementById('trackingNumberDiv');
    const cancelDiv = document.getElementById('cancelReasonDiv');
    
    if (currentStatus === 'shipped' && trackingDiv) {
        trackingDiv.style.display = 'block';
    }
    
    if (currentStatus === 'cancelled' && cancelDiv) {
        cancelDiv.style.display = 'block';
    }
    
    // Remove existing event listeners
    const newStatusSelect = statusSelect.cloneNode(true);
    statusSelect.parentNode.replaceChild(newStatusSelect, statusSelect);
    
    // Show/hide fields based on status change
    newStatusSelect.addEventListener('change', function() {
        if (this.value === 'shipped') {
            trackingDiv.style.display = 'block';
        } else {
            trackingDiv.style.display = 'none';
        }
        
        if (this.value === 'cancelled') {
            cancelDiv.style.display = 'block';
        } else {
            cancelDiv.style.display = 'none';
        }
    });
    
    modal.show();
}

// Form submit with AJAX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('updateStatusForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
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
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const modal = bootstrap.Modal.getInstance(document.getElementById('updateStatusModal'));
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
});

// Cancel Order
function cancelOrder(orderId) {
    if (confirm('Bu siparişi iptal etmek istediğinizden emin misiniz?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/orders/${orderId}/cancel`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PUT';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

// Export Orders
function exportOrders() {
    window.location.href = '/admin/orders/export';
}

// Print functionality
function printTable() {
    window.print();
}
</script>
@endpush