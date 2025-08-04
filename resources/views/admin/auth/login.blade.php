<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Giriş - ALLEMTIA</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('admin/src/images/favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Admin Modern CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/admin-modern.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/login.css') }}">
</head>
<body class="login-page">
    <!-- Animated Background -->
    <div class="animated-background">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
    </div>
    
    <!-- Login Container -->
    <div class="login-container">
        <div class="login-card">
            <!-- Glass Layers -->
            <div class="glass-layer-1"></div>
            <div class="glass-layer-2"></div>
            
            <!-- Glass Content -->
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
                    <div class="alert alert-success alert-glass animate-slideInUp">
                        <i class="bi bi-check-circle-fill"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-glass animate-slideInUp">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-glass animate-slideInUp">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif
                
                <!-- Login Form -->
                <form action="{{ route('admin.login.submit') }}" method="POST" class="login-form" id="loginForm" data-validate>
                    @csrf
                    
                    <div class="form-group">
                        <label class="form-label" for="email">E-posta Adresi</label>
                        <div class="input-wrapper">
                            <i class="bi bi-envelope input-icon"></i>
                            <input 
                                type="email" 
                                class="form-control-glass" 
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
                                class="form-control-glass" 
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
                    
                    <button type="submit" class="btn btn-login" id="submitBtn">
                        <span class="btn-text">Giriş Yap</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="{{ asset('admin/js/admin-modern.js') }}"></script>
    <script>
        // Form Submit Handler
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        });
        
        // Success Animation
        @if(session('login_success'))
            window.addEventListener('load', () => {
                AdminPanel.showToast('Giriş başarılı! Yönlendiriliyorsunuz...', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.dashboard") }}';
                }, 1500);
            });
        @endif
        
        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Input focus effects
        const inputs = document.querySelectorAll('.form-control-glass');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>