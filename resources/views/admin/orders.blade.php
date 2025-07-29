@extends('layouts.layout')

@section('title', 'Siparişler')

@section('content')
    <!-- CSRF Token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Sipariş Yönetimi</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Siparişler</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <div class="dropdown d-inline-block mr-2">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                Duruma Göre Filtrele
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('admin.orders') }}">Tümü</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'pending']) }}">Beklemede</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'waiting_payment']) }}">Ödeme Bekleniyor</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'paid']) }}">Ödendi</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'processing']) }}">Hazırlanıyor</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'shipped']) }}">Kargoda</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'delivered']) }}">Teslim Edildi</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['is_partially_cancelled' => '1']) }}">Kısmi İptal</a>
                                <a class="dropdown-item" href="{{ route('admin.orders', ['status' => 'cancelled']) }}">İptal Edildi</a>
                            </div>
                        </div>
                        
                        <div class="dropdown d-inline-block">
                            <a class="btn btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                Satıcıya Göre Filtrele
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('admin.orders') }}">Tüm Satıcılar</a>
                                @foreach($sellers as $seller)
                                <a class="dropdown-item" href="{{ route('admin.orders', ['seller_id' => $seller->id]) }}">
                                    {{ $seller->name }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Sipariş Listesi</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sipariş Numarası</th>
                                <th>Müşteri</th>
                                <th>E-posta</th>
                                <th>Toplam</th>
                                <th>Durum</th>
                                <th>Ödeme Yöntemi</th>
                                <th>Satıcılar</th>
                                <th>Tarih</th>
                                <th class="datatable-nosort">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                @php
                                    // İptal edilen ürünlerin toplamını hesapla
                                    $cancelledTotal = $order->items->where('is_cancelled', true)->sum('subtotal');
                                    // Güncel toplam tutarı hesapla
                                    $currentTotal = $order->total - $cancelledTotal;
                                    // İptal edilmiş ürün var mı?
                                    $hasCancelledItems = $order->items->where('is_cancelled', true)->count() > 0;
                                    // Tüm ürünler iptal edilmiş mi?
                                    $allItemsCancelled = $hasCancelledItems && $order->items->count() === $order->items->where('is_cancelled', true)->count();
                                    
                                    // Siparişteki satıcıları al
                                    $orderSellers = $order->items->map(function($item) {
                                        return $item->product->user ?? null;
                                    })->filter()->unique('id');
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $order->order_number }}
                                        @if($hasCancelledItems)
                                            @if($allItemsCancelled)
                                                <span class="badge badge-danger badge-sm">İptal Edildi</span>
                                            @else
                                                <span class="badge badge-danger badge-sm">Kısmi İptal</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->customer_email }}</td>
                                    <td>
                                        @if($hasCancelledItems)
                                            <div class="d-flex flex-column">
                                                <del class="text-muted">₺{{ number_format($order->total, 2) }}</del>
                                                <strong class="text-success">₺{{ number_format($currentTotal, 2) }}</strong>
                                            </div>
                                        @else
                                            <strong>₺{{ number_format($order->total, 2) }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @switch($order->status)
                                                @case('pending') badge-warning @break
                                                @case('waiting_payment') badge-info @break
                                                @case('paid') badge-success @break
                                                @case('processing') badge-primary @break
                                                @case('shipped') badge-info @break
                                                @case('delivered') badge-success @break
                                                @case('cancelled') badge-danger @break
                                                @default badge-secondary
                                            @endswitch
                                            @if(($order->is_partially_cancelled || $hasCancelledItems) && !$allItemsCancelled) badge-warning @endif
                                            @if($allItemsCancelled) badge-danger @endif
                                        ">
                                            @if($order->status === 'cancelled' || $allItemsCancelled)
                                                İptal Edildi
                                            @elseif($order->is_partially_cancelled || ($hasCancelledItems && !$allItemsCancelled))
                                                Kısmen İptal
                                            @else
                                                @switch($order->status)
                                                    @case('pending') Beklemede @break
                                                    @case('waiting_payment') Ödeme Bekleniyor @break
                                                    @case('paid') Ödendi @break
                                                    @case('processing') Hazırlanıyor @break
                                                    @case('shipped') Kargoda @break
                                                    @case('delivered') Teslim Edildi @break
                                                    @default {{ ucfirst($order->status) }}
                                                @endswitch
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @switch($order->payment_method)
                                            @case('eft') EFT/Havale @break
                                            @case('cash_on_delivery') Kapıda Nakit @break
                                            @default {{ ucfirst($order->payment_method) }}
                                        @endswitch
                                    </td>
                                    <td>
                                        @foreach($orderSellers as $seller)
                                            <span class="badge badge-pill badge-info mb-1">
                                                {{ $seller->name }}
                                                <a href="{{ route('admin.orders', ['seller_id' => $seller->id]) }}" class="text-white ml-1" title="Bu satıcıya göre filtrele">
                                                    <i class="icon-copy fa fa-filter" aria-hidden="true"></i>
                                                </a>
                                            </span>
                                            {{ !$loop->last ? ' ' : '' }}
                                        @endforeach
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                                href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.orders.show', $order->id) }}">
                                                    <i class="dw dw-eye"></i> Detayları Görüntüle
                                                </a>
                                                <a class="dropdown-item" data-toggle="modal"
                                                    data-target="#editOrderModal{{ $order->id }}" href="#">
                                                    <i class="dw dw-edit2"></i> Durumu Düzenle
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="printInvoice({{ $order->id }})">
                                                    <i class="dw dw-print"></i> Fatura Yazdır
                                                </a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="if(confirm('Bu siparişi silmek istediğinizden emin misiniz?')) deleteOrder({{ $order->id }});">
                                                    <i class="dw dw-delete-3"></i> Sil
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Order Status Modal -->
                                <div class="modal fade" id="editOrderModal{{ $order->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="editOrderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-gradient-primary text-white border-0">
                                                <h4 class="modal-title font-weight-bold">
                                                    <i class="dw dw-edit2 mr-2"></i>Sipariş Güncelle: <span class="badge badge-light text-primary">{{ $order->order_number }}</span>
                                                </h4>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('admin.orders') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <input type="hidden" name="action" value="update_status">
                                                <div class="modal-body p-4">
                                                    <!-- Sipariş Durumu -->
                                                    <div class="form-section mb-4">
                                                        <h6 class="text-primary font-weight-bold mb-3">
                                                            <i class="dw dw-settings mr-2"></i>Sipariş Durumu Güncelleme
                                                        </h6>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="orderStatus{{ $order->id }}" class="font-weight-semibold text-dark">
                                                                        <i class="dw dw-tag mr-1 text-info"></i>Sipariş Durumu
                                                                    </label>
                                                                    <select name="status" id="orderStatus{{ $order->id }}" class="form-control form-control-lg border-2" required>
                                                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ Beklemede</option>
                                                                        <option value="waiting_payment" {{ $order->status == 'waiting_payment' ? 'selected' : '' }}>💰 Ödeme Bekleniyor</option>
                                                                        <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>✅ Ödendi</option>
                                                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>🔄 Hazırlanıyor</option>
                                                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>🚚 Kargoda</option>
                                                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>📦 Teslim Edildi</option>
                                                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ İptal Edildi</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="trackingNumber{{ $order->id }}" class="font-weight-semibold text-dark">
                                                                        <i class="dw dw-delivery-truck mr-1 text-warning"></i>Kargo Takip Numarası
                                                                    </label>
                                                                    <input type="text" name="tracking_number" id="trackingNumber{{ $order->id }}"
                                                                        class="form-control form-control-lg border-2" value="{{ $order->tracking_number ?? '' }}"
                                                                        placeholder="Takip numarasını girin">
                                                                    <small class="text-muted">
                                                                        <i class="dw dw-info mr-1"></i>
                                                                        Kargo takip numarası müşteriyle paylaşılacaktır
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Notlar ve İptal Bilgileri -->
                                                    <div class="form-section mb-4">
                                                        <h6 class="text-success font-weight-bold mb-3">
                                                            <i class="dw dw-notepad mr-2"></i>Notlar ve İptal Bilgileri
                                                        </h6>
                                                        <div class="form-group">
                                                            <label for="statusNote{{ $order->id }}" class="font-weight-semibold text-dark">
                                                                <i class="dw dw-edit mr-1 text-success"></i>Durum Güncelleme Notu
                                                            </label>
                                                            <textarea name="status_note" id="statusNote{{ $order->id }}" class="form-control form-control-lg border-2" rows="3"
                                                                placeholder="Bu durum güncellmesi hakkında bir not ekleyin (isteğe bağlı)..."></textarea>
                                                        </div>

                                                        <!-- İptal Nedeni (sadece iptal seçildiğinde göster) -->
                                                        <div class="form-group" id="cancelReasonGroup{{ $order->id }}" style="{{ $order->status == 'cancelled' ? 'display: block;' : 'display: none;' }}">
                                                            <label for="cancelReason{{ $order->id }}" class="font-weight-semibold text-dark">
                                                                <i class="dw dw-cancel mr-1 text-danger"></i>İptal Nedeni
                                                            </label>
                                                            <textarea name="cancel_reason" id="cancelReason{{ $order->id }}" class="form-control form-control-lg border-2" rows="2"
                                                                placeholder="İptal nedenini belirtin...">{{ $order->cancellation_reason ?? '' }}</textarea>
                                                            <small class="text-muted">
                                                                <i class="dw dw-info mr-1"></i>
                                                                İptal nedeni müşteriyle paylaşılacaktır
                                                            </small>
                                                        </div>
                                                    </div>

                                                    <!-- Sipariş Özet Bilgileri -->
                                                    <div class="form-section">
                                                        <h6 class="text-info font-weight-bold mb-3">
                                                            <i class="dw dw-shopping-cart mr-2"></i>Sipariş Özet Bilgileri
                                                        </h6>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="info-box mb-3">
                                                                    <label class="font-weight-semibold text-dark d-block mb-1">
                                                                        <i class="dw dw-user mr-1 text-primary"></i>Müşteri Bilgileri
                                                                    </label>
                                                                    <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                                                                    <p class="mb-1 text-muted">{{ $order->customer_email }}</p>
                                                                    <p class="mb-0 text-muted">{{ $order->customer_phone }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="info-box mb-3">
                                                                    <label class="font-weight-semibold text-dark d-block mb-1">
                                                                        <i class="dw dw-credit-card mr-1 text-info"></i>Ödeme Bilgileri
                                                                    </label>
                                                                    <p class="mb-1">
                                                                        @switch($order->payment_method)
                                                                            @case('eft') EFT/Havale @break
                                                                            @case('cash_on_delivery') Kapıda Nakit @break
                                                                            @default {{ ucfirst($order->payment_method) }}
                                                                        @endswitch
                                                                    </p>
                                                                    <p class="mb-0 text-muted">Sipariş Tarihi: {{ $order->created_at->format('d M Y H:i') }}</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="info-box mb-3">
                                                                    @if($hasCancelledItems)
                                                                    <label class="font-weight-semibold text-dark d-block mb-1">
                                                                        <i class="dw dw-money mr-1 text-success"></i>Tutar Bilgileri
                                                                    </label>
                                                                    <p class="mb-1">
                                                                        <del class="text-muted">₺{{ number_format($order->total, 2) }}</del>
                                                                        <span class="text-success font-weight-bold">₺{{ number_format($currentTotal, 2) }}</span>
                                                                    </p>
                                                                    <p class="mb-0 text-danger">İptal: ₺{{ number_format($cancelledTotal, 2) }}</p>
                                                                    @else
                                                                    <label class="font-weight-semibold text-dark d-block mb-1">
                                                                        <i class="dw dw-money mr-1 text-success"></i>Toplam Tutar
                                                                    </label>
                                                                    <p class="mb-0 font-weight-bold text-success h5">₺{{ number_format($order->total, 2) }}</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-0 justify-content-between">
                                                    <button type="button" class="btn btn-light btn-lg px-4" data-dismiss="modal">
                                                        <i class="dw dw-cancel mr-2"></i>İptal
                                                    </button>
                                                    <button type="submit" class="btn btn-primary btn-lg px-4">
                                                        <i class="dw dw-save mr-2"></i>Değişiklikleri Kaydet
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Edit Order Status Modal End -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Status değiştiğinde cancel reason göster/gizle
        document.addEventListener('DOMContentLoaded', function() {
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
        });

        // Notification fonksiyonu
        function showNotification(message, type = 'info') {
            // Mevcut notification varsa kaldır
            const existingNotification = document.querySelector('.custom-notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // SweetAlert kullanıyorsanız
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type === 'success' ? 'success' : type === 'error' ? 'error' : 'info',
                    title: type === 'success' ? 'Başarılı!' : type === 'error' ? 'Hata!' : 'Bilgi',
                    text: message,
                    showConfirmButton: true,
                    timer: 4000,
                    timerProgressBar: true
                });
            }
            // Custom notification div
            else {
                const notificationDiv = document.createElement('div');
                notificationDiv.className = `custom-notification alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
                notificationDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                notificationDiv.innerHTML = `
                    <strong>${type === 'success' ? 'Başarılı!' : type === 'error' ? 'Hata!' : 'Bilgi:'}</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                document.body.appendChild(notificationDiv);
                
                // 5 saniye sonra otomatik kaldır
                setTimeout(() => {
                    if (notificationDiv.parentNode) {
                        notificationDiv.remove();
                    }
                }, 5000);
            }
        }

        // Notification fonksiyonu (sadece başarı/hata mesajları için)
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif

        function printInvoice(orderId) {
            const printUrl = `/admin/orders/${orderId}/invoice`;
            window.open(printUrl, '_blank');
        }

        function deleteOrder(orderId) {
            // AJAX ile delete işlemi
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
                    showNotification('Sipariş başarıyla silindi', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Sipariş silinirken hata oluştu', 'error');
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                showNotification('Silme fonksiyonu uygulanması gerekiyor', 'error');
            });
        }
    </script>
@endsection

<style>
/* Modal Geliştirmeleri */
.modal-xl {
    max-width: 1000px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.form-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border-left: 4px solid #007bff;
}

.form-control-lg {
    height: calc(2.5rem + 2px);
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
}

.border-2 {
    border-width: 2px !important;
    transition: all 0.3s ease;
}

.border-2:focus {
    border-color: #007bff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.input-group-text {
    font-weight: 600;
    border-width: 2px;
    border-left: 0;
}

.font-weight-semibold {
    font-weight: 600;
}

.alert {
    border-radius: 10px;
}

/* Badge ve Button Geliştirmeleri */
.badge-light {
    background-color: rgba(255,255,255,0.9) !important;
    border: 1px solid rgba(0,0,0,0.1);
}

.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-lg:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Form Section Headers */
.form-section h6 {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 20px;
}

/* Modal Shadow */
.modal-content {
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

/* Info Box Styling */
.info-box {
    padding: 15px;
    background-color: #f8fafc;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.info-box p {
    margin-bottom: 0.5rem;
}

.info-box p:last-child {
    margin-bottom: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 10px auto;
    }
    
    .form-section {
        padding: 15px;
    }
    
    .btn-lg {
        padding: 10px 20px;
        font-size: 14px;
    }
    
    .info-box {
        padding: 12px;
    }
}
</style>