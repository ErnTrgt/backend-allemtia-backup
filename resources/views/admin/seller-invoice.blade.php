<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fatura #{{ $order->order_number }} - {{ $seller->name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 20px;
            color: #333;
        }
        .invoice-info {
            width: 100%;
            margin-bottom: 30px;
            margin-top: 20px;
        }
        .customer-info, .order-info {
            width: 48%;
            float: left;
        }
        .order-info {
            float: right;
        }
        .info-title {
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        .order-notes {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .note-item {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #eee;
        }
        .note-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .note-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
            color: #555;
        }
        .note-content {
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            width: 300px;
            float: right;
            margin-bottom: 30px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .total-row.grand-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            padding: 8px 0;
        }
        .cancelled {
            text-decoration: line-through;
            color: #999;
            background-color: #fff0f0;
        }
        .notes {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            clear: both;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-processing { background-color: #d1ecf1; color: #0c5460; }
        .status-shipped { background-color: #cce7ff; color: #004085; }
        .status-delivered { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        
        .tracking-info {
            text-align: center;
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f0f8ff;
            border: 1px dashed #4a89dc;
            border-radius: 4px;
        }
        
        .cancel-reason {
            font-style: italic;
            color: #721c24;
            font-size: 10px;
            margin-top: 3px;
        }
        
        .seller-info {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .seller-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="invoice-title">SATICI FATURASI</div>
    
    <div class="seller-info">
        <div class="seller-title">Satıcı Bilgileri</div>
        <div><strong>Ad:</strong> {{ $seller->name }}</div>
        <div><strong>E-posta:</strong> {{ $seller->email }}</div>
        <div><strong>Telefon:</strong> {{ $seller->phone ?? 'Belirtilmemiş' }}</div>
    </div>

    @if(isset($trackingInfo) && $trackingInfo)
    <div class="tracking-info">
        <strong style="font-size: 14px;">Kargo Takip Numarası:</strong> 
        <span style="font-size: 14px; font-weight: bold; color: #2c3e50;">{{ $trackingInfo['number'] }}</span>
    </div>
    @elseif($order->tracking_number)
    <div class="tracking-info">
        <strong style="font-size: 14px;">Kargo Takip Numarası:</strong> 
        <span style="font-size: 14px; font-weight: bold; color: #2c3e50;">{{ $order->tracking_number }}</span>
    </div>
    @endif

    <div class="invoice-info">
        <div class="customer-info">
            <div class="info-title">MÜŞTERİ BİLGİLERİ</div>
            <div class="info-row">
                <span class="info-label">Ad Soyad:</span>
                <span>{{ $order->customer_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">E-posta:</span>
                <span>{{ $order->customer_email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Telefon:</span>
                <span>{{ $order->customer_phone }}</span>
            </div>
            @if($order->shipping_address)
            <div class="info-row">
                <span class="info-label">Adres:</span>
                <span>
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
        <div class="order-info">
            <div class="info-title">SİPARİŞ BİLGİLERİ</div>
            <div class="info-row">
                <span class="info-label">Sipariş No:</span>
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tarih:</span>
                <span>{{ $order->created_at->format('d.m.Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ödeme:</span>
                <span>
                    @switch($order->payment_method)
                        @case('eft') EFT/Havale @break
                        @case('cash_on_delivery') Kapıda Nakit @break
                        @default {{ ucfirst($order->payment_method) }}
                    @endswitch
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Durum:</span>
                <span class="status-badge status-{{ $order->status }}">
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
                @if($order->is_partially_cancelled)
                    <span class="status-badge status-cancelled">Kısmen İptal</span>
                @endif
            </div>
        </div>
    </div>

    <div style="clear: both;"></div>

    @if(
        (isset($orderNotes) && ($orderNotes['customer_note'] || $orderNotes['status_note'] || $orderNotes['cancellation_reason'] || $orderNotes['seller_note'])) || 
        $order->status_note || $order->cancellation_reason || $order->seller_note || $order->notes
    )
    <div class="order-notes">
        <div class="info-title">SİPARİŞ NOTLARI</div>
        
        @if(isset($orderNotes) && $orderNotes['customer_note'])
        <div class="note-item">
            <span class="note-label">Müşteri Notu:</span>
            <span class="note-content">{{ $orderNotes['customer_note'] }}</span>
        </div>
        @elseif($order->notes)
        <div class="note-item">
            <span class="note-label">Müşteri Notu:</span>
            <span class="note-content">{{ $order->notes }}</span>
        </div>
        @endif
        
        @if(isset($orderNotes) && $orderNotes['status_note'])
        <div class="note-item">
            <span class="note-label">Durum Notu:</span>
            <span class="note-content">{{ $orderNotes['status_note'] }}</span>
        </div>
        @elseif($order->status_note)
        <div class="note-item">
            <span class="note-label">Durum Notu:</span>
            <span class="note-content">{{ $order->status_note }}</span>
        </div>
        @endif
        
        @if(isset($orderNotes) && $orderNotes['cancellation_reason'])
        <div class="note-item">
            <span class="note-label">İptal Nedeni:</span>
            <span class="note-content">{{ $orderNotes['cancellation_reason'] }}</span>
        </div>
        @elseif($order->cancellation_reason)
        <div class="note-item">
            <span class="note-label">İptal Nedeni:</span>
            <span class="note-content">{{ $order->cancellation_reason }}</span>
        </div>
        @endif
        
        @if(isset($orderNotes) && $orderNotes['seller_note'])
        <div class="note-item">
            <span class="note-label">Satıcı Notu:</span>
            <span class="note-content">{{ $orderNotes['seller_note'] }}</span>
        </div>
        @elseif($order->seller_note)
        <div class="note-item">
            <span class="note-label">Satıcı Notu:</span>
            <span class="note-content">{{ $order->seller_note }}</span>
        </div>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Ürün</th>
                <th style="width: 10%;">Fiyat</th>
                <th style="width: 10%;">Adet</th>
                <th style="width: 15%;">Toplam</th>
                <th style="width: 15%;">Durum</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sellerItems as $index => $item)
            <tr class="{{ $item->is_cancelled ? 'cancelled' : '' }}">
                <td>{{ $index + 1 }}</td>
                <td>
                    <div>{{ $item->product_name }}</div>
                    @if($item->size)
                        <small>Beden: {{ $item->size }}</small>
                    @endif
                    @if($item->is_cancelled && isset($item->cancel_reason))
                        <div class="cancel-reason">İptal Nedeni: {{ $item->cancel_reason }}</div>
                    @elseif($item->is_cancelled)
                        <div class="cancel-reason">İptal Edildi</div>
                    @endif
                </td>
                <td>₺{{ number_format($item->price, 2) }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">₺{{ number_format($item->subtotal, 2) }}</td>
                <td>
                    @if($item->is_cancelled)
                        <span class="status-badge status-cancelled">İptal</span>
                        @if(isset($item->cancelled_at))
                            <br><small>{{ \Carbon\Carbon::parse($item->cancelled_at)->format('d.m.Y H:i') }}</small>
                        @endif
                    @else
                        <span class="status-badge status-paid">Aktif</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Ara Toplam:</span>
            <span>₺{{ number_format($sellerTotal, 2) }}</span>
        </div>
        @if($cancelledTotal > 0)
        <div class="total-row">
            <span>İptal Edilen:</span>
            <span>-₺{{ number_format($cancelledTotal, 2) }}</span>
        </div>
        @endif
        <div class="total-row grand-total">
            <span>GENEL TOPLAM:</span>
            <span>₺{{ number_format($currentTotal, 2) }}</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="notes">
        <p><strong>Not:</strong> Bu fatura sadece {{ $seller->name }} satıcısına ait ürünleri içerir ve elektronik ortamda oluşturulmuştur.</p>
        
        @if(isset($trackingInfo) && $trackingInfo)
        <p><strong>Kargo Takip Numarası:</strong> {{ $trackingInfo['number'] }}</p>
        @elseif($order->tracking_number)
        <p><strong>Kargo Takip Numarası:</strong> {{ $order->tracking_number }}</p>
        @endif
        
        @if(isset($orderNotes) && $orderNotes['customer_note'])
        <p><strong>Müşteri Notu:</strong> {{ $orderNotes['customer_note'] }}</p>
        @elseif($order->notes)
        <p><strong>Müşteri Notu:</strong> {{ $order->notes }}</p>
        @endif
        
        @if(isset($orderNotes) && $orderNotes['status_note'])
        <p><strong>Sipariş Durum Notu:</strong> {{ $orderNotes['status_note'] }}</p>
        @elseif($order->status_note)
        <p><strong>Sipariş Durum Notu:</strong> {{ $order->status_note }}</p>
        @endif
        
        @if(isset($orderNotes) && $orderNotes['cancellation_reason'])
        <p><strong>İptal Nedeni:</strong> {{ $orderNotes['cancellation_reason'] }}</p>
        @elseif($order->cancellation_reason)
        <p><strong>İptal Nedeni:</strong> {{ $order->cancellation_reason }}</p>
        @endif
        
        @if(isset($orderNotes) && $orderNotes['seller_note'])
        <p><strong>Satıcı Notu:</strong> {{ $orderNotes['seller_note'] }}</p>
        @elseif($order->seller_note)
        <p><strong>Satıcı Notu:</strong> {{ $order->seller_note }}</p>
        @endif
    </div>
</body>
</html> 