<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $maintenance->title ?? 'Site Bakımda' }} - Allemtia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated background */
        .bg-animation {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
        }

        .bg-animation span {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            animation: move 25s linear infinite;
            bottom: -150px;
        }

        .bg-animation span:nth-child(1) {
            left: 25%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }

        .bg-animation span:nth-child(2) {
            left: 10%;
            width: 20px;
            height: 20px;
            animation-delay: 2s;
            animation-duration: 12s;
        }

        .bg-animation span:nth-child(3) {
            left: 70%;
            width: 20px;
            height: 20px;
            animation-delay: 4s;
        }

        .bg-animation span:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .bg-animation span:nth-child(5) {
            left: 65%;
            width: 20px;
            height: 20px;
            animation-delay: 0s;
        }

        .bg-animation span:nth-child(6) {
            left: 75%;
            width: 110px;
            height: 110px;
            animation-delay: 3s;
        }

        .bg-animation span:nth-child(7) {
            left: 35%;
            width: 150px;
            height: 150px;
            animation-delay: 7s;
        }

        .bg-animation span:nth-child(8) {
            left: 50%;
            width: 25px;
            height: 25px;
            animation-delay: 15s;
            animation-duration: 45s;
        }

        .bg-animation span:nth-child(9) {
            left: 20%;
            width: 15px;
            height: 15px;
            animation-delay: 2s;
            animation-duration: 35s;
        }

        .bg-animation span:nth-child(10) {
            left: 85%;
            width: 150px;
            height: 150px;
            animation-delay: 0s;
            animation-duration: 11s;
        }

        @keyframes move {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
                border-radius: 0;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
                border-radius: 50%;
            }
        }

        .maintenance-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 60px 40px;
            max-width: 600px;
            width: 90%;
            text-align: center;
            position: relative;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .maintenance-icon {
            font-size: 80px;
            color: #667eea;
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .maintenance-title {
            font-size: 36px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }

        .maintenance-message {
            font-size: 18px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .countdown-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .countdown-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            min-width: 80px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .countdown-value {
            font-size: 28px;
            font-weight: 700;
            display: block;
        }

        .countdown-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .contact-info {
            margin-top: 40px;
            padding-top: 40px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .contact-title {
            font-size: 16px;
            color: #999;
            margin-bottom: 15px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-link {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 50%;
            color: #667eea;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
        }

        .progress-bar-container {
            margin: 30px 0;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 3px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 10px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            width: 0%;
            animation: progressAnimation 3s ease-out forwards;
        }

        @keyframes progressAnimation {
            to {
                width: 65%;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .maintenance-container {
                padding: 40px 20px;
            }

            .maintenance-title {
                font-size: 28px;
            }

            .maintenance-message {
                font-size: 16px;
            }

            .countdown-container {
                flex-wrap: wrap;
                gap: 10px;
            }

            .countdown-item {
                min-width: 70px;
                padding: 10px 15px;
            }

            .countdown-value {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated background elements -->
    <div class="bg-animation">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="maintenance-container">
        <div class="maintenance-icon">
            <i class="bi bi-tools"></i>
        </div>

        <h1 class="maintenance-title">{{ $maintenance->title ?? 'Site Bakımda' }}</h1>
        
        <p class="maintenance-message">
            {{ $maintenance->message ?? 'Sitemiz şu anda bakım çalışması nedeniyle geçici olarak hizmet verememektedir. En kısa sürede geri döneceğiz. Anlayışınız için teşekkür ederiz.' }}
        </p>

        @if($maintenance && $maintenance->estimated_end_time && $maintenance->estimated_end_time->isFuture())
            <div class="countdown-container" id="countdown">
                <div class="countdown-item">
                    <span class="countdown-value" id="days">00</span>
                    <span class="countdown-label">Gün</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="hours">00</span>
                    <span class="countdown-label">Saat</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="minutes">00</span>
                    <span class="countdown-label">Dakika</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-value" id="seconds">00</span>
                    <span class="countdown-label">Saniye</span>
                </div>
            </div>
        @endif

        <div class="progress-bar-container">
            <div class="progress-bar-fill"></div>
        </div>

        <div class="contact-info">
            <p class="contact-title">Bizi sosyal medyada takip edin</p>
            <div class="social-links">
                <a href="#" class="social-link" title="Facebook">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" class="social-link" title="Twitter">
                    <i class="bi bi-twitter"></i>
                </a>
                <a href="#" class="social-link" title="Instagram">
                    <i class="bi bi-instagram"></i>
                </a>
                <a href="#" class="social-link" title="LinkedIn">
                    <i class="bi bi-linkedin"></i>
                </a>
            </div>
        </div>
    </div>

    @if($maintenance && $maintenance->estimated_end_time && $maintenance->estimated_end_time->isFuture())
    <script>
        // Countdown timer
        const countdownDate = new Date("{{ $maintenance->estimated_end_time->format('Y-m-d H:i:s') }}").getTime();
        
        const countdown = setInterval(function() {
            const now = new Date().getTime();
            const distance = countdownDate - now;
            
            if (distance < 0) {
                clearInterval(countdown);
                document.getElementById("countdown").innerHTML = "<p>Bakım çalışması tamamlandı. Sayfa yenileniyor...</p>";
                setTimeout(() => {
                    location.reload();
                }, 3000);
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById("days").innerHTML = String(days).padStart(2, '0');
            document.getElementById("hours").innerHTML = String(hours).padStart(2, '0');
            document.getElementById("minutes").innerHTML = String(minutes).padStart(2, '0');
            document.getElementById("seconds").innerHTML = String(seconds).padStart(2, '0');
        }, 1000);
    </script>
    @endif
</body>
</html>