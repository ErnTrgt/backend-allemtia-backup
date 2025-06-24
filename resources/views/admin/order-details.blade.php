@extends('layouts.layout')

@section('title', 'Order Details')

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
                            <h4>Order Details</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.orders') }}">Orders</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Order #{{ $order->order_number }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.orders') }}" class="btn btn-secondary">
                            <i class="dw dw-arrow-left"></i> Back to Orders
                        </a>
                        <button onclick="printOrder()" class="btn btn-primary ml-2">
                            <i class="dw dw-print"></i> Print Order
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text-blue mb-10">Order #{{ $order->order_number }}</h4>
                            <p class="text-muted mb-0">
                                <i class="dw dw-calendar1 mr-2"></i>
                                Ordered on {{ $order->created_at->format('d M Y, H:i') }}
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
                                " style="font-size: 14px; padding: 8px 15px;">
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
                                </span>
                            </div>
                            <div class="order-total mt-3">
                                <h3 class="text-primary mb-0">₺{{ number_format($order->total, 2) }}</h3>
                                <small class="text-muted">Total Amount</small>
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
                                <i class="dw dw-user1 mr-2"></i>Customer Information
                            </h5>
                        </div>
                        <div class="pd-20">
                            <div class="profile-info">
                                <div class="info-item mb-3">
                                    <span class="info-label">Name:</span>
                                    <span class="info-value">{{ $order->customer_name }}</span>
                                </div>
                                <div class="info-item mb-3">
                                    <span class="info-label">Email:</span>
                                    <span class="info-value">{{ $order->customer_email }}</span>
                                </div>
                                <div class="info-item mb-3">
                                    <span class="info-label">Phone:</span>
                                    <span class="info-value">{{ $order->customer_phone }}</span>
                                </div>
                                @if($order->shipping_address)
                                <div class="info-item">
                                    <span class="info-label">Address:</span>
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
                                <i class="dw dw-credit-card mr-2"></i>Payment & Shipping
                            </h5>
                        </div>
                        <div class="pd-20">
                            <div class="profile-info">
                                <div class="info-item mb-3">
                                    <span class="info-label">Payment Method:</span>
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
                                    <span class="info-label">Tracking Number:</span>
                                    <span class="info-value">
                                        <code>{{ $order->tracking_number }}</code>
                                        <button class="btn btn-sm btn-outline-primary ml-2" onclick="copyToClipboard('{{ $order->tracking_number }}')">
                                            Copy
                                        </button>
                                    </span>
                                </div>
                                @endif
                                @if($order->status_note)
                                <div class="info-item mb-3">
                                    <span class="info-label">Order Note:</span>
                                    <span class="info-value">{{ $order->status_note }}</span>
                                </div>
                                @endif
                                @if($order->status === 'cancelled' && $order->cancellation_reason)
                                <div class="info-item">
                                    <span class="info-label">Cancellation Reason:</span>
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
                        <i class="dw dw-box mr-2"></i>Order Items ({{ $order->items->count() }} items)
                    </h5>
                </div>
                <div class="pd-20">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Product</th>
                                    <th>Seller</th>
                                    <th>Size</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $index => $item)
                                <tr>
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
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->product && $item->product->user)
                                            <div class="seller-info">
                                                <strong>{{ $item->product->user->name }}</strong>
                                                <br><small class="text-muted">{{ $item->product->user->email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Unknown Seller</span>
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
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="6" class="text-right"><strong>Total Amount:</strong></td>
                                    <td><strong class="text-primary h5">₺{{ number_format($order->total, 2) }}</strong></td>
                                </tr>
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
                        <i class="dw dw-timeline mr-2"></i>Order Timeline
                    </h5>
                </div>
                <div class="pd-20">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6>Order Placed</h6>
                                <p class="text-muted mb-0">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @if($order->status !== 'pending')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Order Confirmed</h6>
                                <p class="text-muted mb-0">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($order->status === 'shipped' || $order->status === 'delivered')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6>Order Shipped</h6>
                                @if($order->tracking_number)
                                    <p class="text-muted mb-0">Tracking: {{ $order->tracking_number }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        @if($order->status === 'delivered')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Order Delivered</h6>
                                <p class="text-muted mb-0">Order completed successfully</p>
                            </div>
                        </div>
                        @endif
                        @if($order->status === 'cancelled')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6>Order Cancelled</h6>
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
                alert('Tracking number copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
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