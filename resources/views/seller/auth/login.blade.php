<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Login - Allemtia</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* CSS Variables - Color Palette */
        :root {
            --color-dark: #0B090A;
            --color-primary: #2B2D42;
            --color-gray: #8D99AE;
            --color-light: #EDF2F4;
            --color-accent: #EF233C;
            --color-accent-dark: #D90429;
            --color-white: #FFFFFF;
            
            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #2B2D42 0%, #0B090A 100%);
            --gradient-accent: linear-gradient(135deg, #EF233C 0%, #D90429 100%);
            --gradient-light: linear-gradient(135deg, #FFFFFF 0%, #EDF2F4 100%);
            
            /* Transitions */
            --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Shadows */
            --shadow-sm: 0 2px 4px rgba(11, 9, 10, 0.05);
            --shadow-md: 0 4px 12px rgba(11, 9, 10, 0.1);
            --shadow-lg: 0 8px 24px rgba(11, 9, 10, 0.15);
            --shadow-xl: 0 16px 48px rgba(11, 9, 10, 0.2);
        }

        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-light);
            color: var(--color-primary);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Main Container */
        .login-container {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
        }

        /* Left Side - Visual */
        .login-visual {
            flex: 1.2;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-visual::before {
            content: '';
            position: absolute;
            width: 150%;
            height: 150%;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3Cpattern id='grid' width='20' height='20' patternUnits='userSpaceOnUse'%3E%3Ccircle cx='10' cy='10' r='1' fill='%23EDF2F4' opacity='0.1'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='100' height='100' fill='url(%23grid)'/%3E%3C/svg%3E");
            animation: backgroundMove 30s linear infinite;
        }

        @keyframes backgroundMove {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(10deg); }
        }

        .visual-content {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 3rem;
            max-width: 500px;
        }

        .visual-content h1 {
            color: var(--color-white);
            font-size: 3.5rem;
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin-bottom: 1.5rem;
            letter-spacing: -2px;
            line-height: 1.1;
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .visual-content p {
            color: var(--color-light);
            font-size: 1.125rem;
            font-weight: 400;
            line-height: 1.7;
            margin: 0 auto 3rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.2s forwards;
        }

        .visual-icon {
            width: 400px;
            height: 400px;
            margin: 0 auto;
            opacity: 0;
            transform: scale(0.9) translateY(20px);
            animation: scaleIn 1s cubic-bezier(0.16, 1, 0.3, 1) 0.4s forwards;
        }

        /* Right Side - Form */
        .login-form-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: var(--gradient-light);
            position: relative;
        }

        /* Background Decoration */
        .form-bg-decoration {
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: var(--gradient-accent);
            opacity: 0.03;
            filter: blur(100px);
            top: -200px;
            right: -200px;
            animation: float 8s ease-in-out infinite;
        }

        .form-bg-decoration-2 {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: var(--color-primary);
            opacity: 0.02;
            filter: blur(80px);
            bottom: -150px;
            left: -150px;
            animation: float 10s ease-in-out infinite reverse;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 3rem;
            border-radius: 24px;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(237, 242, 244, 0.8);
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 3rem;
            opacity: 0;
            transform: translateY(-20px);
            animation: fadeInDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .logo-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: var(--gradient-primary);
            border-radius: 20px;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .logo-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .logo-wrapper:hover::before {
            transform: translateX(100%);
        }

        .logo-wrapper img {
            height: 40px;
            width: auto;
            filter: brightness(0) invert(1);
            position: relative;
            z-index: 1;
        }

        .brand-name {
            font-size: 1.75rem;
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--color-primary);
            letter-spacing: -1px;
        }

        .brand-tagline {
            font-size: 0.875rem;
            color: var(--color-gray);
            margin-top: 0.25rem;
        }

        /* Form Header */
        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
            opacity: 0;
            transform: translateY(-10px);
            animation: fadeInDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards;
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-dark);
            margin-bottom: 0.5rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .form-header p {
            color: var(--color-gray);
            font-size: 0.9375rem;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.2s forwards;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.3s;
        }

        .form-label {
            display: block;
            margin-bottom: 0.625rem;
            font-weight: 600;
            color: var(--color-primary);
            font-size: 0.875rem;
            letter-spacing: -0.01em;
        }

        .form-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .form-input-icon {
            position: absolute;
            left: 1rem;
            color: var(--color-gray);
            display: flex;
            align-items: center;
            pointer-events: none;
            transition: color var(--transition-fast);
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            border: 2px solid transparent;
            background-color: var(--color-light);
            border-radius: 12px;
            font-size: 0.9375rem;
            font-family: 'Inter', sans-serif;
            transition: all var(--transition-fast);
            color: var(--color-dark);
            font-weight: 500;
        }

        .form-input:hover {
            background-color: #e2e8ea;
        }

        .form-input:focus {
            outline: none;
            background-color: var(--color-white);
            border-color: var(--color-primary);
            box-shadow: 0 0 0 4px rgba(43, 45, 66, 0.08);
        }

        .form-input:focus ~ .form-input-icon {
            color: var(--color-primary);
        }

        .form-input::placeholder {
            color: var(--color-gray);
            font-weight: 400;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            color: var(--color-gray);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 6px;
            transition: all var(--transition-fast);
            display: flex;
            align-items: center;
        }

        .password-toggle:hover {
            color: var(--color-primary);
            background-color: var(--color-light);
        }

        /* Form Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.4s forwards;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--color-primary);
            cursor: pointer;
            border-radius: 6px;
        }

        .checkbox-wrapper label {
            font-size: 0.875rem;
            color: var(--color-primary);
            cursor: pointer;
            user-select: none;
            font-weight: 500;
        }

        .forgot-link {
            font-size: 0.875rem;
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 600;
            transition: all var(--transition-fast);
            position: relative;
        }

        .forgot-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--color-accent);
            transition: width var(--transition-fast);
        }

        .forgot-link:hover::after {
            width: 100%;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--gradient-accent);
            color: var(--color-white);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-base);
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.5s forwards;
            box-shadow: 0 4px 14px rgba(239, 35, 60, 0.25);
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 35, 60, 0.35);
        }

        .submit-btn:hover::before {
            transform: translateX(100%);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn.loading {
            pointer-events: none;
            color: transparent;
        }

        .submit-btn.loading::after {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            top: 50%;
            left: 50%;
            margin-left: -12px;
            margin-top: -12px;
            border: 3px solid var(--color-white);
            border-top-color: transparent;
            border-radius: 50%;
            animation: spinner 0.8s linear infinite;
        }

        /* Divider */
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
            opacity: 0;
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.6s forwards;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: var(--color-light);
        }

        .divider span {
            background-color: white;
            padding: 0 1rem;
            position: relative;
            font-size: 0.875rem;
            color: var(--color-gray);
            font-weight: 500;
        }

        /* Alternative Login */
        .alt-login {
            text-align: center;
            opacity: 0;
            animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.7s forwards;
        }

        .alt-login p {
            font-size: 0.875rem;
            color: var(--color-gray);
        }

        .alt-login a {
            color: var(--color-primary);
            font-weight: 600;
            text-decoration: none;
            transition: color var(--transition-fast);
        }

        .alt-login a:hover {
            color: var(--color-accent);
        }

        /* Alerts */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            opacity: 0;
            transform: translateY(-10px) scale(0.95);
            animation: alertIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
            overflow: hidden;
        }

        @keyframes alertIn {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background-color: currentColor;
        }

        .alert-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
        }

        .alert-content {
            flex: 1;
            line-height: 1.5;
        }

        .alert-error {
            background-color: #fef2f2;
            color: var(--color-accent);
            border: 1px solid #fee2e2;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #dcfce7;
        }

        /* Animations */
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes spinner {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0) scale(1); 
            }
            50% { 
                transform: translateY(-30px) scale(1.05); 
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .login-visual {
                flex: 1;
            }
            
            .visual-content h1 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 1024px) {
            .login-visual {
                display: none;
            }

            .login-form-container {
                background: var(--gradient-primary);
                padding: 1rem;
            }

            .login-form-wrapper {
                max-width: 480px;
                box-shadow: none;
                border: none;
            }

            .form-bg-decoration,
            .form-bg-decoration-2 {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .login-form-wrapper {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }

            .visual-content h1 {
                font-size: 2rem;
            }

            .logo-section {
                margin-bottom: 2rem;
            }

            .logo-wrapper {
                width: 64px;
                height: 64px;
            }

            .brand-name {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Visual -->
        <div class="login-visual">
            <div class="visual-content">
                <h1>Satıcı Paneline<br>Hoş Geldiniz</h1>
                <p>İşletmenizi yönetin, müşterilerinize ulaşın ve satışlarınızı artırın. Güçlü araçlarımızla başarıya giden yolda size eşlik ediyoruz.</p>
                
                <!-- Modern SVG Illustration -->
                <svg class="visual-icon" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#EDF2F4;stop-opacity:0.2" />
                            <stop offset="100%" style="stop-color:#8D99AE;stop-opacity:0.3" />
                        </linearGradient>
                        <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#EF233C;stop-opacity:0.3" />
                            <stop offset="100%" style="stop-color:#D90429;stop-opacity:0.4" />
                        </linearGradient>
                    </defs>
                    
                    <!-- Background circles -->
                    <circle cx="200" cy="200" r="180" fill="url(#grad1)" opacity="0.3"/>
                    <circle cx="200" cy="200" r="140" fill="url(#grad1)" opacity="0.2"/>
                    
                    <!-- Store Icon -->
                    <g transform="translate(150, 120)">
                        <rect x="0" y="40" width="100" height="80" fill="#EDF2F4" rx="8"/>
                        <polygon points="50,0 100,40 0,40" fill="#8D99AE"/>
                        <rect x="15" y="60" width="30" height="40" fill="#8D99AE" rx="4"/>
                        <rect x="55" y="60" width="30" height="40" fill="#8D99AE" rx="4"/>
                        <circle cx="50" cy="30" r="8" fill="#EDF2F4"/>
                    </g>
                    
                    <!-- Shopping Bags -->
                    <g transform="translate(80, 220)">
                        <path d="M0 20 L0 50 Q0 55 5 55 L25 55 Q30 55 30 50 L30 20" fill="#EF233C" opacity="0.8"/>
                        <path d="M5 20 Q5 10 15 10 Q25 10 25 20" fill="none" stroke="#EDF2F4" stroke-width="3"/>
                    </g>
                    
                    <g transform="translate(290, 240)">
                        <path d="M0 15 L0 40 Q0 45 5 45 L20 45 Q25 45 25 40 L25 15" fill="#8D99AE" opacity="0.8"/>
                        <path d="M5 15 Q5 5 12.5 5 Q20 5 20 15" fill="none" stroke="#EDF2F4" stroke-width="2"/>
                    </g>
                    
                    <!-- Chart Line -->
                    <g transform="translate(120, 280)">
                        <path d="M0 40 L30 25 L60 30 L90 10 L120 20 L150 0" fill="none" stroke="#EDF2F4" stroke-width="4" stroke-linecap="round" opacity="0.6"/>
                        <circle cx="0" cy="40" r="5" fill="#EF233C"/>
                        <circle cx="30" cy="25" r="5" fill="#EF233C"/>
                        <circle cx="60" cy="30" r="5" fill="#EF233C"/>
                        <circle cx="90" cy="10" r="5" fill="#EF233C"/>
                        <circle cx="120" cy="20" r="5" fill="#EF233C"/>
                        <circle cx="150" cy="0" r="5" fill="#EF233C"/>
                    </g>
                </svg>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-form-container">
            <div class="form-bg-decoration"></div>
            <div class="form-bg-decoration-2"></div>
            
            <div class="login-form-wrapper">
                <!-- Logo Section -->
                <div class="logo-section">
                    <div class="logo-wrapper">
                        <img src="{{ asset('admin/src/images/emtialogo.png') }}" alt="Allemtia Logo">
                    </div>
                    <div class="brand-name">Allemtia</div>
                    <div class="brand-tagline">Satıcı Yönetim Paneli</div>
                </div>

                <!-- Form Header -->
                <div class="form-header">
                    <h2>Hesabınıza Giriş Yapın</h2>
                    <p>Devam etmek için bilgilerinizi girin</p>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success">
                        <svg class="alert-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm-2 15l-5-5 1.41-1.41L8 12.17l7.59-7.59L17 6l-9 9z" fill="currentColor"/>
                        </svg>
                        <div class="alert-content">{{ session('success') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        <svg class="alert-icon" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zm1 15H9v-2h2v2zm0-4H9V5h2v6z" fill="currentColor"/>
                        </svg>
                        <div class="alert-content">
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('seller.login.submit') }}" method="POST" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email" class="form-label">E-posta Adresi</label>
                        <div class="form-input-wrapper">
                            <div class="form-input-icon">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 5C3 3.89543 3.89543 3 5 3H15C16.1046 3 17 3.89543 17 5V15C17 16.1046 16.1046 17 15 17H5C3.89543 17 3 16.1046 3 15V5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M3 5L10 10L17 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-input" 
                                placeholder="isim@sirket.com"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Şifre</label>
                        <div class="form-input-wrapper">
                            <div class="form-input-icon">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="5" y="9" width="10" height="8" rx="1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7 9V6C7 4.34315 8.34315 3 10 3C11.6569 3 13 4.34315 13 6V9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="10" cy="13" r="1" fill="currentColor"/>
                                </svg>
                            </div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                            <div class="password-toggle" id="togglePassword">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 4C4.5 4 1 10 1 10s3.5 6 9 6 9-6 9-6-3.5-6-9-6z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="form-options">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Beni hatırla</label>
                        </div>
                        <a href="#" class="forgot-link">Şifremi unuttum?</a>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        Giriş Yap
                    </button>

                    <div class="divider">
                        <span>veya</span>
                    </div>

                    <div class="alt-login">
                        <p>Henüz hesabınız yok mu? <a href="#">Hemen başvurun</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            
            // Change icon
            if (type === 'text') {
                this.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.071 2.929a1 1 0 010 1.414l-14 14a1 1 0 01-1.414-1.414l14-14a1 1 0 011.414 0z" fill="currentColor"/>
                        <path d="M3.5 3.5C5.5 2 7.5 1 10 1c5.5 0 9 6 9 6s-.884 1.51-2.5 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M16.5 16.5C14.5 18 12.5 19 10 19c-5.5 0-9-6-9-6s.884-1.51 2.5-3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                `;
            } else {
                this.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 4C4.5 4 1 10 1 10s3.5 6 9 6 9-6 9-6-3.5-6-9-6z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                `;
            }
        });

        // Form Submit Loading State
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        
        loginForm.addEventListener('submit', function(e) {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
        });

        // Auto-hide alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px) scale(0.95)';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });

        // Add ripple effect to button
        submitBtn.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    </script>

    <style>
        /* Ripple Effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</body>
</html>