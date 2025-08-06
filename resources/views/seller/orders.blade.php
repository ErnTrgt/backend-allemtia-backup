@extends('seller.layout')

@section('title', 'Seller Orders')

@section('content')
    <!-- CSRF Token meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="title">
                            <h4 class="mb-2">Siparişlerim</h4>
                            <p class="text-muted mb-0">Ürünlerinize ait siparişleri yönetin</p>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation" class="mt-2">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">Anasayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Siparişler</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="text-md-right text-sm-left mt-md-0 mt-3">
                            <!-- Filter Dropdown -->
                            <div class="dropdown d-inline-block mr-2">
                                <a class="btn btn-outline-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                    <i class="dw dw-filter mr-1"></i>Durum Filtre
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item {{ !request('status') ? 'active' : '' }}" href="{{ route('seller.orders') }}">
                                        <i class="dw dw-list mr-2"></i>Tüm Siparişler
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item {{ request('status') == 'pending' ? 'active' : '' }}" href="{{ route('seller.orders', ['status' => 'pending']) }}">
                                        <span class="badge badge-warning mr-2">●</span>Beklemede
                                    </a>
                                    <a class="dropdown-item {{ request('status') == 'paid' ? 'active' : '' }}" href="{{ route('seller.orders', ['status' => 'paid']) }}">
                                        <span class="badge badge-success mr-2">●</span>Ödendi
                                    </a>
                                    <a class="dropdown-item {{ request('status') == 'processing' ? 'active' : '' }}" href="{{ route('seller.orders', ['status' => 'processing']) }}">
                                        <span class="badge badge-primary mr-2">●</span>İşleniyor
                                    </a>
                                    <a class="dropdown-item {{ request('status') == 'shipped' ? 'active' : '' }}" href="{{ route('seller.orders', ['status' => 'shipped']) }}">
                                        <span class="badge badge-info mr-2">●</span>Kargolandı
                                    </a>
                                    <a class="dropdown-item {{ request('status') == 'delivered' ? 'active' : '' }}" href="{{ route('seller.orders', ['status' => 'delivered']) }}">
                                        <span class="badge badge-success mr-2">●</span>Teslim Edildi
                                    </a>
                                </div>
                            </div>
                            <!-- Refresh Button -->
                            <button onclick="location.reload()" class="btn btn-secondary">
                                <i class="dw dw-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="card-box mb-30 d-none d-lg-block">
                <div class="pd-20 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="text-blue mb-0">Siparişler Listesi</h5>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">{{ $orders->count() }} sipariş bulundu</small>
                        </div>
                    </div>
                </div>
                <div class="pb-20">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">#</th>
                                    <th class="border-0">Sipariş</th>
                                    <th class="border-0">Müşteri</th>
                                    <th class="border-0">Ürünler</th>
                                    <th class="border-0">Kazanç</th>
                                    <th class="border-0">Durum</th>
                                    <th class="border-0">Tarih</th>
                                    <th class="border-0 text-center">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    @php
                                        $sellerItems = $order->items->where('product.user_id', auth()->id());
                                        $sellerTotal = $sellerItems->sum('subtotal');
                                        $sellerQuantity = $sellerItems->sum('quantity');
                                        
                                        // İptal edilen ürünlerin toplam ve adet bilgilerini hesapla
                                        $cancelledItems = $sellerItems->where('is_cancelled', true);
                                        $cancelledTotal = $cancelledItems->sum('subtotal');
                                        $cancelledQuantity = $cancelledItems->sum('quantity');
                                        
                                        // Güncel kazanç hesapla
                                        $currentEarning = $sellerTotal - $cancelledTotal;
                                        
                                        // Satıcının tüm ürünleri iptal edilmiş mi?
                                        $allSellerItemsCancelled = $cancelledItems->count() > 0 && $sellerItems->count() === $cancelledItems->count();
                                    @endphp
                                    
                                    @if($sellerItems->count() > 0)
                                    <tr>
                                        <td class="font-weight-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <div>
                                                <strong class="text-primary">#{{ $order->order_number }}</strong>
                                                <br><small class="text-muted">{{ $sellerQuantity }} items</small>
                                                @if($cancelledItems->count() > 0)
                                                    @if($allSellerItemsCancelled)
                                                        <span class="badge badge-danger">Tamamen İptal</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ $cancelledItems->count() }} iptal</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="customer-info">
                                                <strong>{{ $order->customer_name }}</strong>
                                                <br><small class="text-muted">{{ $order->customer_email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="products-preview">
                                                @foreach($sellerItems->take(2) as $item)
                                                    <div class="d-flex align-items-center mb-1">
                                                        @if($item->product && $item->product->images->isNotEmpty())
                                                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                                 alt="Product" class="product-thumb-mini mr-2">
                                                        @endif
                                                        <span class="text-truncate {{ $item->is_cancelled ? 'text-muted text-decoration-line-through' : '' }}">
                                                            {{ Str::limit($item->product_name, 20) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                                @if($sellerItems->count() > 2)
                                                    <small class="text-muted">+{{ $sellerItems->count() - 2 }} daha fazla</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($cancelledItems->count() > 0)
                                                <div>
                                                    <del class="text-muted">₺{{ number_format($sellerTotal, 2) }}</del>
                                                </div>
                                                <strong class="text-success h6">₺{{ number_format($currentEarning, 2) }}</strong>
                                            @else
                                                <strong class="text-success h6">₺{{ number_format($sellerTotal, 2) }}</strong>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->status === 'cancelled' || $allSellerItemsCancelled)
                                                <span class="status-badge status-cancelled">
                                                    İptal Edildi
                                                </span>
                                            @elseif($order->is_partially_cancelled || ($cancelledItems->count() > 0 && !$allSellerItemsCancelled))
                                                <span class="status-badge status-warning">
                                                    Kısmen İptal
                                                </span>
                                            @else
                                                <span class="status-badge status-{{ $order->status }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $order->created_at->format('d M Y') }}</small>
                                            <br><small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#viewOrderModal{{ $order->id }}">
                                                    <i class="dw dw-eye"></i>
                                                </button>
                                                @if(in_array($order->status, ['paid', 'processing']))
                                                <button class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#updateStatusModal{{ $order->id }}">
                                                    <i class="dw dw-edit2"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="dw dw-shopping-cart1 text-muted mb-3" style="font-size: 48px;"></i>
                                                <h5 class="text-muted">Sipariş bulunamadı</h5>
                                                <p class="text-muted">Henüz sipariş almadınız.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="d-block d-lg-none">
                @forelse ($orders as $order)
                    @php
                        $sellerItems = $order->items->where('product.user_id', auth()->id());
                        $sellerTotal = $sellerItems->sum('subtotal');
                        $sellerQuantity = $sellerItems->sum('quantity');
                        
                        // İptal edilen ürünlerin toplam ve adet bilgilerini hesapla
                        $cancelledItems = $sellerItems->where('is_cancelled', true);
                        $cancelledTotal = $cancelledItems->sum('subtotal');
                        $cancelledQuantity = $cancelledItems->sum('quantity');
                        
                        // Güncel kazanç hesapla
                        $currentEarning = $sellerTotal - $cancelledTotal;
                        
                        // Satıcının tüm ürünleri iptal edilmiş mi?
                        $allSellerItemsCancelled = $cancelledItems->count() > 0 && $sellerItems->count() === $cancelledItems->count();
                    @endphp
                    
                    @if($sellerItems->count() > 0)
                    <div class="card-box mb-20">
                        <div class="pd-15">
                            <!-- Order Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="text-primary mb-1">#{{ $order->order_number }}</h6>
                                    <small class="text-muted">{{ $order->created_at->format('d M Y H:i') }}</small>
                                </div>
                                @if($order->status === 'cancelled' || $allSellerItemsCancelled)
                                    <span class="status-badge status-cancelled">
                                        İptal Edildi
                                    </span>
                                @elseif($order->is_partially_cancelled || ($cancelledItems->count() > 0 && !$allSellerItemsCancelled))
                                    <span class="status-badge status-warning">
                                        Kısmen İptal
                                    </span>
                                @else
                                    <span class="status-badge status-{{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </div>

                            <!-- Customer Info -->
                            <div class="customer-section mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="dw dw-user1 text-muted mr-2"></i>
                                    <div>
                                        <strong>{{ $order->customer_name }}</strong>
                                        <br><small class="text-muted">{{ $order->customer_email }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Products Preview -->
                            <div class="products-section mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="dw dw-box text-muted mr-2"></i>
                                    <span class="font-weight-bold">
                                        My Products ({{ $sellerQuantity }} items)
                                        @if($cancelledItems->count() > 0)
                                            @if($allSellerItemsCancelled)
                                                <span class="badge badge-danger ml-1">Tamamen İptal</span>
                                            @else
                                                <span class="badge badge-danger ml-1">{{ $cancelledItems->count() }} iptal</span>
                                            @endif
                                        @endif
                                    </span>
                                </div>
                                <div class="products-mobile-list">
                                    @foreach($sellerItems->take(3) as $item)
                                        <div class="product-mobile-item {{ $item->is_cancelled ? 'bg-light' : '' }}">
                                            @if($item->product && $item->product->images->isNotEmpty())
                                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                     alt="Product" class="product-thumb-mobile">
                                            @endif
                                            <div class="product-mobile-info">
                                                <div class="product-mobile-name {{ $item->is_cancelled ? 'text-muted text-decoration-line-through' : '' }}">
                                                    {{ $item->product_name }}
                                                    @if($item->is_cancelled)
                                                        <span class="badge badge-danger ml-1">İptal</span>
                                                    @endif
                                                </div>
                                                <div class="product-mobile-details">
                                                    Qty: {{ $item->quantity }} × ₺{{ number_format($item->price, 2) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($sellerItems->count() > 3)
                                        <div class="text-center mt-2">
                                            <small class="text-muted">+{{ $sellerItems->count() - 3 }} more products</small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Earnings & Actions -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="earnings-mobile">
                                    <small class="text-muted">Kazançlarım</small>
                                    @if($cancelledItems->count() > 0)
                                        <div>
                                            <del class="text-muted">₺{{ number_format($sellerTotal, 2) }}</del>
                                        </div>
                                        <div class="h5 text-success mb-0">₺{{ number_format($currentEarning, 2) }}</div>
                                    @else
                                        <div class="h5 text-success mb-0">₺{{ number_format($sellerTotal, 2) }}</div>
                                    @endif
                                </div>
                                <div class="mobile-actions">
                                    <button class="btn btn-sm btn-outline-primary mr-1" data-toggle="modal" data-target="#viewOrderModal{{ $order->id }}">
                                        <i class="dw dw-eye mr-1"></i>Görüntüle
                                    </button>
                                    @if(in_array($order->status, ['paid', 'processing']))
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#updateStatusModal{{ $order->id }}">
                                        <i class="dw dw-edit2 mr-1"></i>Güncelle
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="card-box text-center py-5">
                        <i class="dw dw-shopping-cart1 text-muted mb-3" style="font-size: 64px;"></i>
                        <h5 class="text-muted">Sipariş bulunamadı</h5>
                        <p class="text-muted">Henüz sipariş almadınız.</p>
                    </div>
                @endforelse
            </div>

            <!-- Modals -->
            @foreach ($orders as $order)
                @php
                    $sellerItems = $order->items->where('product.user_id', auth()->id());
                    $sellerTotal = $sellerItems->sum('subtotal');
                @endphp
                
                @if($sellerItems->count() > 0)
                <!-- View Order Details Modal -->
                <div class="modal fade" id="viewOrderModal{{ $order->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">
                                    <i class="dw dw-eye mr-2"></i>Sipariş #{{ $order->order_number }}
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Customer & Order Info -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <h6 class="text-primary mb-3">
                                                <i class="dw dw-user1 mr-2"></i>Müşteri Bilgileri
                                            </h6>
                                            <div class="info-item">
                                                <span class="info-label">Adı:</span>
                                                <span class="info-value">{{ $order->customer_name }}</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Email:</span>
                                                <span class="info-value">{{ $order->customer_email }}</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Telefon:</span>
                                                <span class="info-value">{{ $order->customer_phone }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card">
                                            <h6 class="text-primary mb-3">
                                                <i class="dw dw-calendar1 mr-2"></i>Sipariş Bilgileri
                                            </h6>
                                            <div class="info-item">
                                                <span class="info-label">Tarih:</span>
                                                <span class="info-value">{{ $order->created_at->format('d M Y H:i') }}</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Ödeme:</span>
                                                <span class="info-value">
                                                    @switch($order->payment_method)
                                                        @case('eft') EFT/Banka Transferi @break
                                                        @case('cash_on_delivery') Nakit Ödeme @break
                                                        @default {{ ucfirst($order->payment_method) }}
                                                    @endswitch
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Durum:</span>
                                                <span class="status-badge status-{{ $order->status }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Products Table -->
                                <div class="products-section">
                                    <h6 class="text-primary mb-3">
                                        <i class="dw dw-box mr-2"></i>Bu Siparişteki Ürünler
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Ürün</th>
                                                    <th>Beden</th>
                                                    <th>Fiyat</th>
                                                    <th>Miktar</th>
                                                    <th>Toplam</th>
                                                    <th>Durum</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sellerItems as $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if($item->product && $item->product->images->isNotEmpty())
                                                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                                     alt="Product" class="product-thumb-small mr-2">
                                                            @endif
                                                            <span>{{ $item->product_name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($item->size)
                                                            <span class="badge badge-light">{{ $item->size }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>₺{{ number_format($item->price, 2) }}</td>
                                                    <td><span class="quantity-badge">{{ $item->quantity }}</span></td>
                                                    <td><strong>₺{{ number_format($item->subtotal, 2) }}</strong></td>
                                                    <td>
                                                        @if($item->is_cancelled)
                                                            <span class="badge badge-danger">İptal Edildi</span>
                                                            <br>
                                                            <small>{{ $item->cancelled_at ? \Carbon\Carbon::parse($item->cancelled_at)->format('d.m.Y H:i') : 'N/A' }}</small>
                                                        @else
                                                            <span class="badge badge-success">Aktif</span>
                                                            @if(!in_array($order->status, ['delivered', 'cancelled']))
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
                                                                        <form action="{{ route('seller.orders.cancel_item', ['order' => $order->id, 'item' => $item->id]) }}" method="POST">
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
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <td colspan="4" class="text-right"><strong>Kazançlarım:</strong></td>
                                                    <td><strong class="text-success h6">₺{{ number_format($sellerTotal, 2) }}</strong></td>
                                                </tr>
                                                @php
                                                    $cancelledItems = $sellerItems->where('is_cancelled', true);
                                                    $cancelledTotal = $cancelledItems->sum('subtotal');
                                                @endphp
                                                
                                                @if($cancelledItems->count() > 0)
                                                <tr>
                                                    <td colspan="4" class="text-right text-danger"><strong>İptal Edilen Ürünler:</strong></td>
                                                    <td><strong class="text-danger">-₺{{ number_format($cancelledTotal, 2) }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right text-success"><strong>Güncel Kazanç:</strong></td>
                                                    <td><strong class="text-success h6">₺{{ number_format($sellerTotal - $cancelledTotal, 2) }}</strong></td>
                                                </tr>
                                                @endif
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                                <button type="button" class="btn btn-primary" onclick="printOrderItems({{ $order->id }})">
                                    <i class="dw dw-print mr-1"></i>İndir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Status Modal -->
                @if(in_array($order->status, ['paid', 'processing']))
                <div class="modal fade" id="updateStatusModal{{ $order->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="dw dw-edit2 mr-2"></i>Sipariş Durumunu Güncelle
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('seller.orders.updateStatus', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <i class="dw dw-info mr-2"></i>
                                        <strong>Sipariş #{{ $order->order_number }}</strong> - Sipariş durumunu güncelleyin
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="sellerStatus{{ $order->id }}">Yeni Durum</label>
                                        <select name="status" id="sellerStatus{{ $order->id }}" class="form-control form-control-lg" required>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                                🔄 İşleniyor - Ürünleriniz hazırlanıyor
                                            </option>
                                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>
                                                🚚 Kargolandı - Ürünler yolda
                                            </option>
                                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                                ✅ Teslim Edildi - Ürünler teslim edildi
                                            </option>
                                        </select>
                                        <small class="text-muted mt-2">Sadece siparişi ileri taşıyabilirsiniz.</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="sellerNote{{ $order->id }}">Not (İsteğe bağlı)</label>
                                        <textarea name="seller_note" id="sellerNote{{ $order->id }}" class="form-control" rows="3"
                                            placeholder="Bu durum güncellemesine ilişkin bir not ekleyin..."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="dw dw-check mr-1"></i>Durumu Güncelle
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @endif
            @endforeach
        </div>
    </div>

    <style>
        /* Status Badges */
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-waiting_payment { background-color: #d1ecf1; color: #0c5460; }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-processing { background-color: #d1ecf1; color: #0c5460; }
        .status-shipped { background-color: #cce7ff; color: #004085; }
        .status-delivered { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .status-warning { background-color: #fff3cd; color: #856404; } /* Kısmen iptal için eklendi */

        /* Product Thumbnails */
        .product-thumb-mini {
            width: 25px;
            height: 25px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid #e9ecef;
        }
        
        .product-thumb-small {
            width: 35px;
            height: 35px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid #e9ecef;
        }
        
        .product-thumb-mobile {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e9ecef;
        }

        /* Mobile Card Styles */
        .product-mobile-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .product-mobile-info {
            margin-left: 12px;
            flex: 1;
        }
        
        .product-mobile-name {
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 2px;
        }
        
        .product-mobile-details {
            font-size: 12px;
            color: #6c757d;
        }

        /* Info Cards */
        .info-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-weight: 500;
            color: #6c757d;
        }
        
        .info-value {
            font-weight: 600;
            color: #495057;
        }

        /* Action Buttons */
        .action-buttons .btn {
            margin: 0 2px;
            width: 35px;
            height: 35px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .mobile-actions .btn {
            border-radius: 20px;
        }

        /* Quantity Badge */
        .quantity-badge {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 12px;
            color: #495057;
        }

        /* Table Enhancements */
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            font-size: 13px;
        }
        
        .table td {
            vertical-align: middle;
            border-top: 1px solid #f1f3f4;
        }

        /* Empty State */
        .empty-state {
            padding: 40px 20px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .pd-ltr-20 {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .card-box {
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            
            .modal-dialog {
                margin: 10px;
            }
            
            .table-responsive {
                border-radius: 8px;
            }
        }

        @media (max-width: 576px) {
            .page-header .row {
                text-align: center;
            }
            
            .text-md-right {
                text-align: center !important;
                margin-top: 15px;
            }
            
            .mobile-actions {
                flex-direction: column;
                gap: 5px;
            }
            
            .mobile-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Loading States */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Hover Effects */
        .card-box:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Modal Enhancements */
        .modal-header.bg-primary,
        .modal-header.bg-success {
            border-bottom: none;
        }
        
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
    </style>

    <script>
        // Toast notifications
        function showToast(message, type = 'success') {
            const toastHtml = `
                <div class="toast align-items-center text-white bg-${type}" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast">x</button>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', toastHtml);
            const toastElement = document.querySelector('.toast:last-child');
            setTimeout(() => toastElement?.remove(), 4000);
        }

        // Session messages
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showToast('{{ session('error') }}', 'danger');
        @endif

        // Print function
        function printOrderItems(orderId) {
            const printUrl = `/seller/orders/${orderId}/print-items`;
            window.open(printUrl, '_blank');
        }

        // Auto-refresh every 30 seconds for new orders
        setTimeout(() => {
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        }, 60000);
    </script>
@endsection