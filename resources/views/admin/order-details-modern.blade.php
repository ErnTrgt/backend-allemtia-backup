@extends('layouts.admin-modern')

@section('title', 'Sipariş Detayı')
@section('header-title', 'Sipariş Detayı #' . $order->order_number)

@section('content')
<div class="order-details-container">
    <!-- Page Header Component -->
    <x-admin.page-header 
        title="Sipariş #{{ $order->order_number }}"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Siparişler', 'url' => route('admin.orders')],
            ['label' => 'Sipariş Detayı']
        ]">
        <x-slot name="actions">
            <a href="{{ route('admin.orders.invoice', $order->id) }}" 
               class="btn btn-secondary" 
               target="_blank">
                <i class="bi bi-file-earmark-pdf me-2"></i>
                Fatura
            </a>
            <button class="btn btn-primary" onclick="updateOrderStatus({{ $order->id }})">
                <i class="bi bi-pencil me-2"></i>
                Durumu Güncelle
            </button>
        </x-slot>
    </x-admin.page-header>
    
    <!-- Order Status Timeline -->
    <x-admin.glass-card class="mb-4">
        <h5 class="mb-3">Sipariş Durumu</h5>
        <div class="status-timeline">
            <div class="timeline-step {{ in_array($order->status, ['pending', 'processing', 'shipped', 'delivered']) ? 'completed' : '' }}">
                <div class="step-icon">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="step-title">Sipariş Alındı</div>
                @if($order->created_at)
                <div class="step-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                @endif
            </div>
            <div class="timeline-line {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'completed' : '' }}"></div>
            <div class="timeline-step {{ in_array($order->status, ['processing', 'shipped', 'delivered']) ? 'completed' : '' }}">
                <div class="step-icon">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="step-title">İşleniyor</div>
                @if($order->status == 'processing' && $order->updated_at)
                <div class="step-date">{{ $order->updated_at->format('d.m.Y H:i') }}</div>
                @endif
            </div>
            <div class="timeline-line {{ in_array($order->status, ['shipped', 'delivered']) ? 'completed' : '' }}"></div>
            <div class="timeline-step {{ in_array($order->status, ['shipped', 'delivered']) ? 'completed' : '' }}">
                <div class="step-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="step-title">Kargoda</div>
                @if($order->tracking_number)
                <div class="step-info">{{ $order->tracking_number }}</div>
                @endif
            </div>
            <div class="timeline-line {{ $order->status == 'delivered' ? 'completed' : '' }}"></div>
            <div class="timeline-step {{ $order->status == 'delivered' ? 'completed' : '' }}">
                <div class="step-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="step-title">Teslim Edildi</div>
                @if($order->status == 'delivered' && $order->updated_at)
                <div class="step-date">{{ $order->updated_at->format('d.m.Y H:i') }}</div>
                @endif
            </div>
        </div>
        
        @if($order->status == 'cancelled')
        <div class="alert alert-danger mt-3">
            <i class="bi bi-x-circle me-2"></i>
            <strong>Sipariş İptal Edildi</strong>
            @if($order->cancellation_reason)
            <p class="mb-0 mt-1">İptal Nedeni: {{ $order->cancellation_reason }}</p>
            @endif
        </div>
        @endif
    </x-admin.glass-card>
    
    <div class="row g-4">
        <!-- Customer & Shipping Info -->
        <div class="col-lg-4">
            <x-admin.glass-card>
                <h5 class="mb-3">Müşteri Bilgileri</h5>
                <div class="info-item">
                    <i class="bi bi-person me-2"></i>
                    <div>
                        <strong>{{ $order->customer_name }}</strong>
                        <span>{{ $order->customer_email }}</span>
                    </div>
                </div>
                @if($order->customer_phone)
                <div class="info-item">
                    <i class="bi bi-telephone me-2"></i>
                    <span>{{ $order->customer_phone }}</span>
                </div>
                @endif
            </x-admin.glass-card>
            
            <x-admin.glass-card class="mt-4">
                <h5 class="mb-3">Teslimat Adresi</h5>
                <div class="address-info">
                    <p class="mb-2">{{ $order->shipping_address }}</p>
                    <p class="mb-0">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                </div>
            </x-admin.glass-card>
            
            <x-admin.glass-card class="mt-4">
                <h5 class="mb-3">Ödeme Bilgileri</h5>
                <div class="payment-info">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ödeme Yöntemi:</span>
                        <strong>{{ $order->payment_method ?? 'Belirtilmemiş' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Ödeme Durumu:</span>
                        <span class="badge bg-success">Ödendi</span>
                    </div>
                </div>
            </x-admin.glass-card>
        </div>
        
        <!-- Order Items -->
        <div class="col-lg-8">
            <x-admin.glass-card>
                <h5 class="mb-3">Sipariş Ürünleri</h5>
                <div class="table-responsive">
                    <table class="table order-items-table">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Birim Fiyat</th>
                                <th>Adet</th>
                                <th>Toplam</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr class="{{ $item->is_cancelled ? 'cancelled-item' : '' }}">
                                <td>
                                    <div class="product-info">
                                        @if($item->product && $item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" 
                                             alt="{{ $item->product_name }}"
                                             class="product-image">
                                        @elseif($item->product && $item->product->images && $item->product->images->first())
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                             alt="{{ $item->product_name }}"
                                             class="product-image">
                                        @else
                                        <img src="/images/default-product.svg" 
                                             alt="{{ $item->product_name }}"
                                             class="product-image">
                                        @endif
                                        <div>
                                            <h6>{{ $item->product_name }}</h6>
                                            @if($item->product && $item->product->user)
                                            <span class="text-muted">Satıcı: {{ $item->product->user->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>₺{{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>₺{{ number_format($item->subtotal, 2) }}</td>
                                <td>
                                    @if($item->is_cancelled)
                                    <span class="badge bg-danger">İptal</span>
                                    @else
                                    <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Ara Toplam:</strong></td>
                                <td colspan="2"><strong>₺{{ number_format($order->subtotal, 2) }}</strong></td>
                            </tr>
                            @if($order->shipping_cost > 0)
                            <tr>
                                <td colspan="3" class="text-end">Kargo:</td>
                                <td colspan="2">₺{{ number_format($order->shipping_cost, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="text-end">İndirim:</td>
                                <td colspan="2" class="text-danger">-₺{{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="total-row">
                                <td colspan="3" class="text-end"><h5>Toplam:</h5></td>
                                <td colspan="2"><h5>₺{{ number_format($order->total, 2) }}</h5></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-admin.glass-card>
            
            @if($order->notes || $order->status_note)
            <x-admin.glass-card class="mt-4">
                <h5 class="mb-3">Notlar</h5>
                @if($order->notes)
                <div class="note-item">
                    <strong>Müşteri Notu:</strong>
                    <p>{{ $order->notes }}</p>
                </div>
                @endif
                @if($order->status_note)
                <div class="note-item">
                    <strong>Durum Notu:</strong>
                    <p>{{ $order->status_note }}</p>
                </div>
                @endif
            </x-admin.glass-card>
            @endif
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(30px); -webkit-backdrop-filter: blur(30px); border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, rgba(169, 0, 0, 0.05) 0%, rgba(193, 18, 31, 0.05) 100%); border-bottom: 1px solid rgba(169, 0, 0, 0.1); padding: 24px; position: relative;">
                <h5 class="modal-title" style="font-size: 20px; font-weight: 600; color: #1f2937; display: flex; align-items: center;">
                    <i class="bi bi-pencil me-2" style="color: #A90000;"></i>
                    Sipariş Güncelle: <span class="badge bg-light text-dark" style="margin-left: 8px; font-size: 14px; font-weight: normal;">#{{ $order->order_number }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: rgba(0, 0, 0, 0.05); border-radius: 8px; opacity: 0.7; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 20px; line-height: 1; color: #4b5563;">×</button>
            </div>
            <form id="updateStatusForm" action="{{ route('admin.orders.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="order_id" id="modalOrderId" value="{{ $order->id }}">
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
                                              placeholder="Bu durum güncellemesi hakkında not ekleyin...">{{ $order->status_note }}</textarea>
                                    <small class="text-muted">Bu not sipariş geçmişinde görünecektir</small>
                                </div>
                            </div>
                            
                            <!-- Cancel Reason (shown only when cancelled is selected) -->
                            <div class="col-12" id="cancelReasonDiv" style="{{ $order->status == 'cancelled' ? '' : 'display: none;' }}">
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
                                        <strong>{{ $order->payment_method ?? 'Belirtilmemiş' }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="margin-bottom: 20px;">
                                    <label class="form-label" style="display: block; font-weight: 500; color: #374151; margin-bottom: 4px; font-size: 14px;">Toplam Tutar</label>
                                    <div class="form-control-static" style="padding: 8px 0; font-size: 14px; color: #111827; line-height: 1.5;">
                                        <strong class="text-primary" style="font-size: 18px; color: #A90000;">₺{{ number_format($order->total, 2, ',', '.') }}</strong>
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

<style>
/* Order Details Styles */
.order-details-container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Status Timeline */
.status-timeline {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--spacing-xl) 0;
}

.timeline-step {
    flex: 1;
    text-align: center;
    position: relative;
}

.step-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto var(--spacing-sm);
    border-radius: 50%;
    background: var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: var(--gray-500);
    transition: all 0.3s ease;
}

.timeline-step.completed .step-icon {
    background: var(--primary-red);
    color: var(--white);
    transform: scale(1.1);
}

.step-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: var(--spacing-xs);
}

.timeline-step.completed .step-title {
    color: var(--gray-900);
}

.step-date {
    font-size: 12px;
    color: var(--gray-500);
}

.step-info {
    font-size: 12px;
    color: var(--primary-blue);
    font-weight: 500;
}

.timeline-line {
    position: absolute;
    top: 30px;
    left: 50%;
    width: calc(100% - 60px);
    height: 2px;
    background: var(--gray-200);
    z-index: -1;
}

.timeline-line.completed {
    background: var(--primary-red);
}

/* Info Items */
.info-item {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.info-item i {
    color: var(--gray-500);
    font-size: 18px;
    margin-top: 2px;
}

.info-item > div {
    display: flex;
    flex-direction: column;
}

.info-item strong {
    font-size: 14px;
    color: var(--gray-900);
}

.info-item span {
    font-size: 13px;
    color: var(--gray-600);
}

/* Address Info */
.address-info p {
    font-size: 14px;
    line-height: 1.6;
    color: var(--gray-700);
}

/* Payment Info */
.payment-info {
    font-size: 14px;
}

/* Order Items Table */
.order-items-table {
    margin-bottom: 0;
}

.order-items-table th {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: var(--spacing-sm) var(--spacing-md);
    background: transparent;
    border-bottom: 2px solid var(--gray-200);
}

.order-items-table td {
    padding: var(--spacing-md);
    vertical-align: middle;
    border-bottom: 1px solid var(--gray-100);
}

.product-info {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.product-image {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-sm);
    object-fit: cover;
    background: var(--gray-100);
}

.product-info h6 {
    font-size: 14px;
    font-weight: 600;
    margin: 0 0 var(--spacing-xs);
    color: var(--gray-900);
}

.product-info span {
    font-size: 12px;
}

.cancelled-item {
    opacity: 0.6;
}

.cancelled-item td {
    text-decoration: line-through;
}

.total-row td {
    border-top: 2px solid var(--gray-200);
    padding-top: var(--spacing-md);
}

/* Note Items */
.note-item {
    padding: var(--spacing-md);
    background: var(--gray-50);
    border-radius: var(--radius-sm);
    margin-bottom: var(--spacing-md);
}

.note-item:last-child {
    margin-bottom: 0;
}

.note-item strong {
    display: block;
    font-size: 13px;
    color: var(--gray-700);
    margin-bottom: var(--spacing-xs);
}

.note-item p {
    font-size: 14px;
    color: var(--gray-600);
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .status-timeline {
        flex-direction: column;
        gap: var(--spacing-lg);
    }
    
    .timeline-line {
        display: none;
    }
    
    .order-items-table {
        font-size: 13px;
    }
    
    .product-image {
        width: 50px;
        height: 50px;
    }
}
</style>
@endsection

@push('scripts')
<script>
// Update Order Status Modal
function updateOrderStatus(orderId) {
    try {
        const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
        
        // Get status select and cancel div
        const statusSelect = document.getElementById('orderStatus');
        const cancelDiv = document.getElementById('cancelReasonDiv');
        
        // Show/hide cancel reason based on current status
        if (cancelDiv && statusSelect) {
            if (statusSelect.value === 'cancelled') {
                cancelDiv.style.display = 'block';
            } else {
                cancelDiv.style.display = 'none';
            }
            
            // Add event listener for status change
            statusSelect.addEventListener('change', function() {
                if (this.value === 'cancelled') {
                    cancelDiv.style.display = 'block';
                } else {
                    cancelDiv.style.display = 'none';
                }
            });
        }
        
        modal.show();
    } catch (error) {
        console.error('Error opening modal:', error);
        alert('Modal açılırken bir hata oluştu. Lütfen sayfayı yenileyip tekrar deneyin.');
    }
}

// Form submit with AJAX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#updateStatusModal form');
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('updateStatusModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    // Update status timeline dynamically
                    updateStatusTimeline(data.order);
                    
                    // Show success toast
                    showSuccessToast('Sipariş durumu başarıyla güncellendi!');
                    
                    // Re-enable button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                } else {
                    // Show error message
                    alert(data.message || 'Bir hata oluştu!');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Sipariş güncellenirken bir hata oluştu. Lütfen tekrar deneyin.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }
});

// Update status timeline dynamically
function updateStatusTimeline(order) {
    const timeline = document.querySelector('.status-timeline');
    if (!timeline) return;
    
    // Reset all steps
    const steps = timeline.querySelectorAll('.timeline-step');
    const lines = timeline.querySelectorAll('.timeline-line');
    
    steps.forEach(step => step.classList.remove('completed'));
    lines.forEach(line => line.classList.remove('completed'));
    
    // Update based on status
    const statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
    const currentIndex = statusOrder.indexOf(order.status);
    
    if (currentIndex >= 0) {
        for (let i = 0; i <= currentIndex; i++) {
            if (steps[i]) steps[i].classList.add('completed');
            if (i > 0 && lines[i-1]) lines[i-1].classList.add('completed');
        }
    }
    
    // Update dates if available
    if (order.status === 'processing' && steps[1]) {
        const dateDiv = steps[1].querySelector('.step-date');
        if (dateDiv) {
            dateDiv.textContent = new Date(order.updated_at).toLocaleString('tr-TR');
        }
    }
    
    if (order.status === 'shipped' && steps[2]) {
        const infoDiv = steps[2].querySelector('.step-info');
        if (infoDiv && order.tracking_number) {
            infoDiv.textContent = order.tracking_number;
        }
    }
    
    if (order.status === 'delivered' && steps[3]) {
        const dateDiv = steps[3].querySelector('.step-date');
        if (dateDiv) {
            dateDiv.textContent = new Date(order.updated_at).toLocaleString('tr-TR');
        }
    }
    
    // Handle cancelled status
    const cancelAlert = document.querySelector('.status-timeline + .alert-danger');
    if (order.status === 'cancelled') {
        if (!cancelAlert) {
            const alertHtml = `
                <div class="alert alert-danger mt-3">
                    <i class="bi bi-x-circle me-2"></i>
                    <strong>Sipariş İptal Edildi</strong>
                    ${order.cancellation_reason ? `<p class="mb-0 mt-1">İptal Nedeni: ${order.cancellation_reason}</p>` : ''}
                </div>
            `;
            timeline.insertAdjacentHTML('afterend', alertHtml);
        }
    } else if (cancelAlert) {
        cancelAlert.remove();
    }
}

// Show success toast notification
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
</script>
@endpush