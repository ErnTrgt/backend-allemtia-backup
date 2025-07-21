<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sepetinizdeki Ürünler Sizi Bekliyor!</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff9f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(255, 102, 0, 0.05);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #ffe6d5;
        }
        .logo-text {
            color: #ff6600;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-family: Arial, Helvetica, sans-serif;
            text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            font-size: 12px;
            color: #ff8533;
            border-top: 1px solid #ffe6d5;
        }
        h1 {
            color: #ff6600;
            font-size: 24px;
        }
        h2 {
            color: #ff6600;
            font-size: 20px;
        }
        .product {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ffe6d5;
            border-radius: 5px;
            display: flex;
            background-color: #ffffff;
        }
        .product img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 5px;
        }
        .product-info {
            flex: 1;
        }
        .product-name {
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .product-price {
            color: #e74c3c;
            font-weight: bold;
        }
        .product-quantity {
            color: #7f8c8d;
        }
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background-color: #ff6600;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #e65a00;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-text">ALLEMTIA</div>
            <h1>Sepetinizdeki Ürünler Sizi Bekliyor!</h1>
        </div>
        
        <div class="content">
            <p>Merhaba {{ $data['user']->name }},</p>
            
            <p>Sepetinizde {{ count($data['products']) }} adet ürün sizi bekliyor. Alışverişinizi tamamlamak için aşağıdaki ürünleri inceleyebilirsiniz.</p>
            
            <h2>{{ $data['seller']->name }} mağazasındaki ürünleriniz:</h2>
            
            @foreach($data['products'] as $product)
            <div class="product">
                @if($product['image'])
                <img src="{{ asset('storage/' . $product['image']) }}" alt="{{ $product['name'] }}">
                @else
                <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                    <span style="color: #aaa;">Görsel Yok</span>
                </div>
                @endif
                
                <div class="product-info">
                    <p class="product-name">{{ $product['name'] }}</p>
                    <p class="product-price">{{ number_format($product['price'], 2) }} ₺</p>
                    <p class="product-quantity">Adet: {{ $product['quantity'] }}</p>
                    <p class="product-subtotal">Toplam: {{ number_format($product['subtotal'], 2) }} ₺</p>
                </div>
            </div>
            @endforeach
            
            <div class="total">
                Toplam Tutar: {{ number_format($data['totalValue'], 2) }} ₺
            </div>
            
            <div class="center">
                <a href="{{ $data['cartUrl'] }}" class="btn">Sepete Git ve Alışverişi Tamamla</a>
            </div>
            
            <p>Sepetinizdeki ürünler sınırlı bir süre için rezerve edilmiştir. Stoklarda tükenme riski olan ürünler olabilir, bu yüzden alışverişinizi en kısa sürede tamamlamanızı öneririz.</p>
            
            <p>Herhangi bir sorunuz veya yardıma ihtiyacınız olursa, lütfen bizimle iletişime geçmekten çekinmeyin.</p>
            
            <p>Teşekkürler,<br>{{ $data['seller']->name }} Ekibi</p>
        </div>
        
        <div class="footer">
            <p>Bu e-posta {{ $data['seller']->name }} tarafından gönderilmiştir.</p>
            <p>© {{ date('Y') }} Tüm hakları saklıdır.</p>
            <p>
                <small>Bu e-postayı almak istemiyorsanız, <a href="#" style="color: #ff8533;">buraya tıklayarak</a> abonelikten çıkabilirsiniz.</small>
            </p>
        </div>
    </div>
</body>
</html> 