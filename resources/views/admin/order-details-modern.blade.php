@extends('layouts.admin-modern')

@section('title', 'Sipariş Detayı')

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
                                        @if($item->product && $item->product->images->first())
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sipariş Durumunu Güncelle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Yeni Durum</label>
                        <select class="form-select" name="status" id="orderStatus" required>
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Bekliyor</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>İşleniyor</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Kargoda</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>İptal</option>
                        </select>
                    </div>
                    <div class="mb-3" id="trackingNumberDiv" style="{{ $order->status == 'shipped' ? '' : 'display: none;' }}">
                        <label class="form-label">Kargo Takip No</label>
                        <input type="text" class="form-control" name="tracking_number" value="{{ $order->tracking_number }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Not (Opsiyonel)</label>
                        <textarea class="form-control" name="status_note" rows="3">{{ $order->status_note }}</textarea>
                    </div>
                    <div class="mb-3" id="cancelReasonDiv" style="{{ $order->status == 'cancelled' ? '' : 'display: none;' }}">
                        <label class="form-label">İptal Nedeni</label>
                        <textarea class="form-control" name="cancel_reason" rows="3">{{ $order->cancellation_reason }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>
                        Güncelle
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
    const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    
    // Show/hide fields based on status
    document.getElementById('orderStatus').addEventListener('change', function() {
        const trackingDiv = document.getElementById('trackingNumberDiv');
        const cancelDiv = document.getElementById('cancelReasonDiv');
        
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
</script>
@endpush