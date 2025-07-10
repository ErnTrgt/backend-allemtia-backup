<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header-container {
            width: 100%;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
            color: #666;
            position: relative;
        }
        .logo {
            float: left;
            width: 50%;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }
        .company-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .header-title {
            width: 100%;
            text-align: center;
            margin-top: 5px;
            clear: both;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <div class="logo">
            Allemtia E-Ticaret - Satıcı Faturası
        </div>
        <div class="company-info">
            {{ auth()->user()->name }} <br>
            {{ auth()->user()->email }}
        </div>
        <div class="header-title">
            SİPARİŞ #{{ isset($order) ? $order->order_number : '' }} - SATICI FATURASI
        </div>
    </div>
</body>
</html> 