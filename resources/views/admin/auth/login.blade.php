<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Giriş - ALLEMTIA</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Confetti Canvas -->
    <canvas id="confetti-canvas" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;"></canvas>
    
    <style>
        :root {
            --primary-red: #A90000;
            --secondary-red: #C1121F;
            --light-blue: #F0F8FF;
            --primary-blue: #0051BB;
            --secondary-blue: #3FA1DD;
            --white: #FFFFFF;
            --gray-50: #F9FAFB;
            --gray-100: #F3F4F6;
            --gray-200: #E5E7EB;
            --gray-300: #D1D5DB;
            --gray-400: #9CA3AF;
            --gray-500: #6B7280;
            --gray-600: #4B5563;
            --gray-700: #374151;
            --gray-800: #1F2937;
            --gray-900: #111827;
            
            --spacing-xs: 4px;
            --spacing-sm: 8px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;
            --spacing-2xl: 48px;
            --spacing-3xl: 64px;
            
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.15);
            --shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.2);
            
            --elastic-out: cubic-bezier(0.68, -0.55, 0.265, 1.55);
            --elastic-in-out: cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--light-blue);
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }
        
        /* Aurora Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            background: var(--light-blue);
        }
        
        .aurora-layer {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0.5;
        }
        
        .aurora-1 {
            background: radial-gradient(ellipse at top left, rgba(169, 0, 0, 0.3) 0%, transparent 50%);
            animation: aurora1 15s ease-in-out infinite;
        }
        
        .aurora-2 {
            background: radial-gradient(ellipse at bottom right, rgba(0, 81, 187, 0.3) 0%, transparent 50%);
            animation: aurora2 20s ease-in-out infinite;
        }
        
        .aurora-3 {
            background: radial-gradient(ellipse at center, rgba(63, 161, 221, 0.2) 0%, transparent 60%);
            animation: aurora3 25s ease-in-out infinite;
        }
        
        @keyframes aurora1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(10%, -10%) scale(1.2); }
            50% { transform: translate(-10%, 10%) scale(0.9); }
            75% { transform: translate(5%, 5%) scale(1.1); }
        }
        
        @keyframes aurora2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(-10%, 10%) scale(1.3); }
            66% { transform: translate(10%, -10%) scale(0.8); }
        }
        
        @keyframes aurora3 {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(0, 0) rotate(180deg); }
        }
        
        /* Login Container */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
        }
        
        .login-card {
            width: 100%;
            max-width: 450px;
            position: relative;
            animation: cardEntrance 0.8s var(--elastic-out);
        }
        
        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* Multi-layer Glass Effect */
        .glass-layer {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            z-index: 1;
        }
        
        .glass-layer-2 {
            inset: -10px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow-xl);
            z-index: 0;
        }
        
        .glass-content {
            position: relative;
            z-index: 2;
            padding: var(--spacing-2xl);
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .glass-content::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent 30%,
                rgba(255, 255, 255, 0.1) 50%,
                transparent 70%
            );
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
            position: relative;
            z-index: 1;
        }
        
        .logo-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 170px;
            height: 170px;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }
        
        .logo-wrapper:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-lg);
            animation: elasticPulse 0.6s var(--elastic-out);
        }
        
        @keyframes elasticPulse {
            0% { transform: scale(1); }
            30% { transform: scale(1.15); }
            60% { transform: scale(0.95); }
            100% { transform: scale(1.05); }
        }
        
        .logo-wrapper img {
            width: 120px;
            height: auto;
        }
        
        .login-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: var(--spacing-sm);
        }
        
        .login-subtitle {
            font-size: 16px;
            color: var(--gray-600);
            font-weight: 400;
        }
        
        /* Form Styles */
        .form-section {
            position: relative;
            z-index: 1;
        }
        
        .form-group {
            margin-bottom: var(--spacing-lg);
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: var(--spacing-sm);
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: var(--spacing-md);
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-500);
            font-size: 18px;
            transition: color 0.3s ease;
        }
        
        .form-control {
            width: 100%;
            padding: var(--spacing-md) var(--spacing-md) var(--spacing-md) var(--spacing-2xl);
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: var(--radius-sm);
            font-size: 16px;
            color: var(--gray-800);
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.7);
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(169, 0, 0, 0.1);
        }
        
        .form-control:focus ~ .input-icon {
            color: var(--primary-red);
        }
        
        .form-control::placeholder {
            color: var(--gray-400);
        }
        
        /* Remember Me */
        .form-check {
            margin-bottom: var(--spacing-lg);
        }
        
        .form-check-input {
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
        }
        
        .form-check-input:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(169, 0, 0, 0.1);
        }
        
        .form-check-label {
            margin-left: var(--spacing-sm);
            color: var(--gray-700);
            font-weight: 400;
            cursor: pointer;
        }
        
        /* Submit Button */
        .btn-primary {
            width: 100%;
            padding: var(--spacing-md);
            background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
            border: none;
            border-radius: var(--radius-sm);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-primary:active::before {
            width: 300px;
            height: 300px;
        }
        
        /* Loading State */
        .btn-loading {
            position: relative;
            color: transparent;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid white;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spinner 0.8s linear infinite;
        }
        
        @keyframes spinner {
            to { transform: rotate(360deg); }
        }
        
        
        /* Alert Messages */
        .alert {
            padding: var(--spacing-md);
            border-radius: var(--radius-sm);
            margin-bottom: var(--spacing-lg);
            font-size: 14px;
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
        }
        
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #166534;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #991b1b;
        }
        
        /* Responsive */
        @media (max-width: 640px) {
            .login-card {
                padding: var(--spacing-xl);
            }
            
            .login-title {
                font-size: 24px;
            }
            
            .gradient-sphere {
                filter: blur(100px);
            }
        }
    </style>
</head>
<body>
    <!-- Aurora Background -->
    <div class="animated-bg">
        <div class="aurora-layer aurora-1"></div>
        <div class="aurora-layer aurora-2"></div>
        <div class="aurora-layer aurora-3"></div>
    </div>
    
    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <div class="glass-layer glass-layer-2"></div>
            <div class="glass-layer"></div>
            <div class="glass-content">
                <!-- Logo Section -->
                <div class="logo-section">
                <div class="logo-wrapper">
                    <img src="{{ asset('admin/src/images/emtialogo.png') }}" alt="ALLEMTIA Logo">
                </div>
                <h1 class="login-title">Yönetim Paneli</h1>
                <p class="login-subtitle">Hesabınıza giriş yapın</p>
            </div>
            
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
            
            <!-- Login Form -->
            <form action="{{ route('admin.login.submit') }}" method="POST" class="form-section" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="email">E-posta Adresi</label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope input-icon"></i>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            placeholder="ornek@email.com"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Şifre</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                    </div>
                </div>
                
                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="remember" 
                        name="remember"
                    >
                    <label class="form-check-label" for="remember">
                        Beni hatırla
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span class="btn-text">Giriş Yap</span>
                </button>
            </form>
            
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Confetti Animation
        class Confetti {
            constructor() {
                this.canvas = document.getElementById('confetti-canvas');
                this.ctx = this.canvas.getContext('2d');
                this.pieces = [];
                this.numberOfPieces = 200;
                this.colors = ['#A90000', '#C1121F', '#0051BB', '#3FA1DD', '#FFD700', '#FF69B4'];
                this.active = false;
            }
            
            init() {
                this.canvas.width = window.innerWidth;
                this.canvas.height = window.innerHeight;
                
                for (let i = 0; i < this.numberOfPieces; i++) {
                    this.pieces.push({
                        x: Math.random() * this.canvas.width,
                        y: -20,
                        w: Math.random() * 10 + 5,
                        h: Math.random() * 5 + 3,
                        color: this.colors[Math.floor(Math.random() * this.colors.length)],
                        speed: Math.random() * 3 + 2,
                        rotation: Math.random() * 360,
                        rotationSpeed: Math.random() * 10 - 5
                    });
                }
                
                this.active = true;
                this.animate();
            }
            
            animate() {
                if (!this.active) return;
                
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                
                this.pieces.forEach((piece, index) => {
                    piece.y += piece.speed;
                    piece.rotation += piece.rotationSpeed;
                    
                    this.ctx.save();
                    this.ctx.translate(piece.x + piece.w / 2, piece.y + piece.h / 2);
                    this.ctx.rotate(piece.rotation * Math.PI / 180);
                    this.ctx.fillStyle = piece.color;
                    this.ctx.fillRect(-piece.w / 2, -piece.h / 2, piece.w, piece.h);
                    this.ctx.restore();
                    
                    if (piece.y > this.canvas.height) {
                        this.pieces.splice(index, 1);
                    }
                });
                
                if (this.pieces.length > 0) {
                    requestAnimationFrame(() => this.animate());
                } else {
                    this.active = false;
                    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                }
            }
            
            start() {
                if (!this.active) {
                    this.pieces = [];
                    this.init();
                }
            }
        }
        
        const confetti = new Confetti();
        
        // Form Submit Handler
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        });
        
        // Check if login was successful (for confetti)
        @if(session('login_success'))
            window.addEventListener('load', () => {
                confetti.start();
                const loginCard = document.querySelector('.glass-content');
                if (loginCard) {
                    loginCard.style.animation = 'successPulse 0.6s var(--elastic-out)';
                }
            });
        @endif
        
        // Success Pulse Animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes successPulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); box-shadow: 0 0 40px rgba(169, 0, 0, 0.3); }
                100% { transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Enhanced Input Focus Effects with Elastic Animation
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                const icon = this.parentElement.querySelector('.input-icon');
                icon.style.transform = 'translateY(-50%) scale(1.2)';
                icon.style.color = 'var(--primary-red)';
                icon.style.transition = 'all 0.5s var(--elastic-out)';
                
                // Add glow effect to input
                this.style.boxShadow = '0 0 20px rgba(169, 0, 0, 0.2)';
            });
            
            input.addEventListener('blur', function() {
                const icon = this.parentElement.querySelector('.input-icon');
                icon.style.transform = 'translateY(-50%) scale(1)';
                icon.style.color = 'var(--gray-500)';
                
                // Remove glow effect
                this.style.boxShadow = 'none';
            });
        });
        
        // Window resize handler for confetti
        window.addEventListener('resize', () => {
            if (confetti.canvas) {
                confetti.canvas.width = window.innerWidth;
                confetti.canvas.height = window.innerHeight;
            }
        });
    </script>
</body>
</html>