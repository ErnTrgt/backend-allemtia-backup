@extends('layouts.layout')

@section('title', 'Sipariş Detayları')

@section('content')
    <!-- Base URL'i sabit olarak tanımla -->
    @php
        $baseUrl = rtrim(url('/'), '/');
    @endphp
    
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Sipariş Detayları</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.orders') }}">Siparişler</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Sipariş #{{ $order->order_number }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.orders') }}" class="btn btn-secondary">
                            <i class="dw dw-arrow-left"></i> Siparişlere Dön
                        </a>
                        <button onclick="printOrder()" class="btn btn-primary ml-2">
                            <i class="dw dw-print"></i> Sipariş Yazdır
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text-blue mb-10">Sipariş #{{ $order->order_number }}</h4>
                            <p class="text-muted mb-0">
                                <i class="dw dw-calendar1 mr-2"></i>
                                Sipariş tarihi {{ $order->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="order-status-badge">
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
                                    @if($order->is_partially_cancelled) badge-warning @endif
                                " style="font-size: 14px; padding: 8px 15px;">
                                    @if($order->is_partially_cancelled)
                                        Kısmen İptal Edildi
                                    @else
                                        @switch($order->status)
                                            @case('pending') Beklemede @break
                                            @case('waiting_payment') Ödeme Bekleniyor @break
                                            @case('paid') Ödendi @break
                                            @case('processing') Hazırlanıyor @break
                                            @case('shipped') Kargoda @break
                                            @case('delivered') Teslim Edildi @break
                                            @case('cancelled') İptal Edildi @break
                                            @default {{ ucfirst($order->status) }}
                                        @endswitch
                                    @endif
                                </span>
                            </div>
                            <div class="order-total mt-3">
                                <h3 class="text-primary mb-0">₺{{ number_format($order->total, 2) }}</h3>
                                <small class="text-muted">Toplam Tutar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Customer Information -->
                <div class="col-md-6">
                    <div class="card-box mb-30">
                        <div class="pd-20 bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="dw dw-user1 mr-2"></i>Müşteri Bilgileri
                            </h5>
                        </div>
                        <div class="pd-20">
                            <div class="profile-info">
                                <div class="info-item mb-3">
                                    <span class="info-label">İsim:</span>
                                    <span class="info-value">{{ $order->customer_name }}</span>
                                </div>
                                <div class="info-item mb-3">
                                    <span class="info-label">E-posta:</span>
                                    <span class="info-value">{{ $order->customer_email }}</span>
                                </div>
                                <div class="info-item mb-3">
                                    <span class="info-label">Telefon:</span>
                                    <span class="info-value">{{ $order->customer_phone }}</span>
                                </div>
                                @if($order->shipping_address)
                                <div class="info-item">
                                    <span class="info-label">Adres:</span>
                                    <span class="info-value">
                                        @if(is_string($order->shipping_address))
                                            @php $address = json_decode($order->shipping_address, true); @endphp
                                            {{ $address['address'] ?? 'N/A' }}<br>
                                            {{ $address['city'] ?? '' }} {{ $address['postal_code'] ?? '' }}
                                        @else
                                            {{ $order->shipping_address->address ?? 'N/A' }}<br>
                                            {{ $order->shipping_address->city ?? '' }} {{ $order->shipping_address->postal_code ?? '' }}
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment & Shipping Info -->
                <div class="col-md-6">
                    <div class="card-box mb-30">
                        <div class="pd-20 bg-success text-white">
                            <h5 class="mb-0">
                                <i class="dw dw-credit-card mr-2"></i>Ödeme ve Kargo
                            </h5>
                        </div>
                        <div class="pd-20">
                            <div class="profile-info">
                                <div class="info-item mb-3">
                                    <span class="info-label">Ödeme Yöntemi:</span>
                                    <span class="info-value">
                                        @switch($order->payment_method)
                                            @case('eft') 
                                                <i class="dw dw-bank text-info mr-1"></i>EFT/Havale 
                                                @break
                                            @case('cash_on_delivery') 
                                                <i class="dw dw-money text-warning mr-1"></i>Kapıda Nakit 
                                                @break
                                            @default 
                                                {{ ucfirst($order->payment_method) }}
                                        @endswitch
                                    </span>
                                </div>
                                @if($order->tracking_number)
                                <div class="info-item mb-3">
                                    <span class="info-label">Takip Numarası:</span>
                                    <span class="info-value">
                                        <code>{{ $order->tracking_number }}</code>
                                        <button class="btn btn-sm btn-outline-primary ml-2" onclick="copyToClipboard('{{ $order->tracking_number }}')">
                                            Kopyala
                                        </button>
                                    </span>
                                </div>
                                @endif
                                @if($order->status_note)
                                <div class="info-item mb-3">
                                    <span class="info-label">Sipariş Notu:</span>
                                    <span class="info-value">{{ $order->status_note }}</span>
                                </div>
                                @endif
                                @if($order->status === 'cancelled' && $order->cancellation_reason)
                                <div class="info-item">
                                    <span class="info-label">İptal Nedeni:</span>
                                    <span class="info-value text-danger">{{ $order->cancellation_reason }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card-box mb-30">
                <div class="pd-20 bg-info text-white">
                    <h5 class="mb-0">
                        <i class="dw dw-box mr-2"></i>Sipariş Ürünleri ({{ $order->items->count() }} ürün)
                        @if($order->items->where('is_cancelled', true)->count() > 0)
                            <span class="badge badge-danger ml-2">{{ $order->items->where('is_cancelled', true)->count() }} İptal Edilmiş Ürün</span>
                        @endif
                    </h5>
                </div>
                <div class="pd-20">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Ürün</th>
                                    <th>Satıcı</th>
                                    <th>Beden</th>
                                    <th>Fiyat</th>
                                    <th>Miktar</th>
                                    <th>Ara Toplam</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $index => $item)
                                <tr class="{{ $item->is_cancelled ? 'bg-light text-muted' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="product-info">
                                            @if($item->product && $item->product->images->isNotEmpty())
                                                @php
                                                    $imagePath = $item->product->images->first()->image_path;
                                                    // Absolute URL oluştur
                                                    $imageUrl = $baseUrl . '/storage/' . $imagePath;
                                                @endphp
                                                <img src="{{ $imageUrl }}" 
                                                     alt="Product Image" class="product-thumb mr-3"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="product-thumb-placeholder mr-3" style="display: none;">
                                                    <i class="dw dw-image"></i>
                                                </div>
                                            @else
                                                <div class="product-thumb-placeholder mr-3">
                                                    <i class="dw dw-image"></i>
                                                </div>
                                            @endif
                                            <div class="product-details">
                                                <strong>{{ $item->product_name }}</strong>
                                                @if($item->product)
                                                    <br><small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                                                @endif
                                                @if($item->is_cancelled)
                                                    <div class="mt-1 text-danger">
                                                        <i class="dw dw-warning"></i> <strong>İptal Edildi</strong>
                                                        @if($item->cancel_reason)
                                                            <br><small>{{ $item->cancel_reason }}</small>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->product && $item->product->user)
                                            <div class="seller-info">
                                                <strong>{{ $item->product->user->name }}</strong>
                                                <br><small class="text-muted">{{ $item->product->user->email }}</small>
                                                <br>
                                                <a href="{{ route('admin.orders.seller.invoice', ['order' => $order->id, 'seller' => $item->product->user->id]) }}" 
                                                   class="btn btn-xs btn-outline-primary mt-1" target="_blank">
                                                    <i class="dw dw-print"></i> Satıcı Faturası
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-muted">Bilinmeyen Satıcı</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->size)
                                            <span class="badge badge-light">{{ $item->size }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>₺{{ number_format($item->price, 2) }}</td>
                                    <td>
                                        <span class="quantity-badge">{{ $item->quantity }}</span>
                                    </td>
                                    <td>
                                        <strong>₺{{ number_format($item->subtotal, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($item->is_cancelled)
                                            <span class="badge badge-danger">İptal</span>
                                            <br>
                                            <small>{{ $item->cancelled_at ? \Carbon\Carbon::parse($item->cancelled_at)->format('d.m.Y H:i') : 'N/A' }}</small>
                                        @else
                                            <span class="badge badge-success">Aktif</span>
                                            @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                                            <div class="mt-2">
                                                <button 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-toggle="modal" 
                                                    data-target="#cancelItemModal{{ $item->id }}">
                                                    <i class="dw dw-cancel"></i> İptal Et
                                                </button>
                                            </div>
                                            
                                            <!-- Item Cancel Modal -->
                                            <div class="modal fade" id="cancelItemModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="cancelItemModalLabel{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="cancelItemModalLabel{{ $item->id }}">Ürün İptal Onayı</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('admin.orders.cancel_item', ['order' => $order->id, 'item' => $item->id]) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <p class="mb-3 text-danger">Bu ürünü iptal etmek istediğinize emin misiniz?</p>
                                                                <div class="item-info mb-3">
                                                                    <p><strong>Ürün:</strong> {{ $item->product_name }}</p>
                                                                    <p><strong>Fiyat:</strong> ₺{{ number_format($item->price, 2) }}</p>
                                                                    <p><strong>Miktar:</strong> {{ $item->quantity }}</p>
                                                                    <p><strong>Toplam:</strong> ₺{{ number_format($item->subtotal, 2) }}</p>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="cancelReason{{ $item->id }}">İptal Nedeni</label>
                                                                    <textarea name="cancel_reason" id="cancelReason{{ $item->id }}" class="form-control" rows="3" required placeholder="İptal nedenini belirtin..."></textarea>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input" id="returnStockCheck{{ $item->id }}" name="return_to_stock" value="1" checked>
                                                                    <label class="form-check-label" for="returnStockCheck{{ $item->id }}">Stoka geri ekle</label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                                                <button type="submit" class="btn btn-danger">Ürünü İptal Et</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End Item Cancel Modal -->
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="6" class="text-right"><strong>Toplam Tutar:</strong></td>
                                    <td colspan="2"><strong class="text-primary h5">₺{{ number_format($order->total, 2) }}</strong></td>
                                </tr>
                                @if($order->items->where('is_cancelled', true)->count() > 0)
                                <tr>
                                    <td colspan="6" class="text-right text-danger"><strong>İptal Edilen Ürünler Toplamı:</strong></td>
                                    <td colspan="2">
                                        <strong class="text-danger">
                                            -₺{{ number_format($order->items->where('is_cancelled', true)->sum('subtotal'), 2) }}
                                        </strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-right text-success"><strong>Güncel Toplam:</strong></td>
                                    <td colspan="2">
                                        <strong class="text-success h5">
                                            ₺{{ number_format($order->total - $order->items->where('is_cancelled', true)->sum('subtotal'), 2) }}
                                        </strong>
                                    </td>
                                </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Timeline (if available) -->
            @if($order->created_at)
            <div class="card-box mb-30">
                <div class="pd-20 bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="dw dw-timeline mr-2"></i>Sipariş Zaman Çizelgesi
                    </h5>
                </div>
                <div class="pd-20">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6>Sipariş Verildi</h6>
                                <p class="text-muted mb-0">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @if($order->status !== 'pending')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Sipariş Onaylandı</h6>
                                <p class="text-muted mb-0">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($order->status === 'shipped' || $order->status === 'delivered')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6>Sipariş Kargoya Verildi</h6>
                                @if($order->tracking_number)
                                    <p class="text-muted mb-0">Takip: {{ $order->tracking_number }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if($order->status === 'delivered')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Sipariş Teslim Edildi</h6>
                                <p class="text-muted mb-0">Sipariş başarıyla tamamlandı</p>
                            </div>
                        </div>
                        @endif
                        @if($order->status === 'cancelled')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6>Sipariş İptal Edildi</h6>
                                @if($order->cancellation_reason)
                                    <p class="text-muted mb-0">{{ $order->cancellation_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <style>
        .info-item {
            display: flex;
            align-items: flex-start;
        }
        
        .info-label {
            font-weight: 600;
            min-width: 120px;
            color: #555;
        }
        
        .info-value {
            flex: 1;
            margin-left: 10px;
        }
        
        .product-info {
            display: flex;
            align-items: center;
        }
        
        .product-thumb {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e0e0e0;
        }
        
        .product-thumb-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e0e0e0;
        }
        
        .seller-info strong {
            color: #007bff;
        }
        
        .quantity-badge {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 2px #e9ecef;
        }
        
        .timeline-content h6 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .badge {
            font-size: 11px;
            padding: 4px 8px;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: #fff;
        }
        
        .badge-success {
            background-color: #28a745;
            color: #fff;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }
        
        .badge-primary {
            background-color: #007bff;
            color: #fff;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        
        .badge-light {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        
        @media print {
            .btn, .page-header .col-md-6:last-child {
                display: none !important;
            }
        }
    </style>

    <script>
        function printOrder() {
            window.print();
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Takip numarası panoya kopyalandı!');
            }, function(err) {
                console.error('Metin kopyalanamadı: ', err);
            });
        }
        
        function tryNextImage(img, paths, index) {
            if (index < paths.length) {
                img.src = paths[index];
                img.onerror = function() {
                    tryNextImage(img, paths, index + 1);
                };
            } else {
                // Tüm yollar başarısız, placeholder göster
                img.style.display = 'none';
                if (img.nextElementSibling) {
                    img.nextElementSibling.style.display = 'flex';
                }
            }
        }
    </script>
@endsection