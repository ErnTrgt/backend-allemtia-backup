<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            text-align: center;
            padding: 5px 0;
            color: #555;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .footer-info {
            margin-bottom: 5px;
        }
        .footer-notes {
            font-style: italic;
            font-size: 9px;
            color: #777;
        }
        .tracking-info {
            margin-top: 5px;
            font-weight: bold;
            color: #333;
        }
        .cancelled-info {
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="footer">
        <div class="footer-info">
            Allemtia E-Ticaret | {{ date('d.m.Y H:i') }}
        </div>
        
        <div class="footer-notes">
            Bu belge elektronik ortamda oluşturulmuştur ve yasal bir belgedir.
            @if(isset($order) && $order->tracking_number)
            <div class="tracking-info">
                Kargo Takip: {{ $order->tracking_number }}
            </div>
            @endif
            
            @if(isset($order) && $order->is_partially_cancelled)
            <div class="cancelled-info">
                Bu sipariş kısmen iptal edilmiştir.
            </div>
            @elseif(isset($order) && $order->status === 'cancelled')
            <div class="cancelled-info">
                Bu sipariş tamamen iptal edilmiştir.
            </div>
            @endif
        </div>
        
        <div>
            Sayfa {PAGE_NUM} / {PAGE_COUNT}
        </div>
    </div>
</body>
</html> 