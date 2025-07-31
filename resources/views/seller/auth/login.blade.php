<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="ALLEMTIA Partner Portal - Türkiye'nin lider B2B metal ticareti ve e-ticaret platformu">
    <meta name="theme-color" content="#A90000">
    <title>Partner Portal | ALLEMTIA</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('admin/src/images/favicon-32x32.png') }}">
    
    <!-- DM Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-red: #A90000;
            --secondary-red: #C1121F;
            --light-bg: #F0F8FF;
            --primary-blue: #0051BB;
            --secondary-blue: #3FA1DD;
            --white: #FFFFFF;
            --black: #000000;
            --gray-50: #FAFAFA;
            --gray-100: #F5F5F5;
            --gray-200: #E5E5E5;
            --gray-300: #D4D4D4;
            --gray-400: #A3A3A3;
            --gray-500: #737373;
            --gray-600: #525252;
            --gray-700: #404040;
            --gray-800: #262626;
            --gray-900: #171717;
            
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
            
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            
            --transition-base: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-smooth: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-bounce: all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 25%, #16213e 50%, #0f0f0f 100%);
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Ultra Modern Multi-Layer Background */
        .background-container {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: linear-gradient(125deg, #000428 0%, #001845 25%, #002366 50%, #004e92 100%);
        }

        /* Additional glass layers */
        .glass-layer-1 {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 30%, rgba(169, 0, 0, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 70%, rgba(0, 81, 187, 0.12) 0%, transparent 50%);
            backdrop-filter: blur(5px);
            animation: glassFloat1 15s ease-in-out infinite;
        }

        .glass-layer-2 {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 60% 20%, rgba(193, 18, 31, 0.1) 0%, transparent 60%),
                        radial-gradient(circle at 30% 80%, rgba(63, 161, 221, 0.08) 0%, transparent 55%);
            backdrop-filter: blur(8px);
            animation: glassFloat2 18s ease-in-out infinite reverse;
        }

        @keyframes glassFloat1 {
            0%, 100% { transform: translateX(0) translateY(0) scale(1); opacity: 0.6; }
            33% { transform: translateX(-20px) translateY(-15px) scale(1.05); opacity: 0.8; }
            66% { transform: translateX(15px) translateY(10px) scale(0.95); opacity: 0.7; }
        }

        @keyframes glassFloat2 {
            0%, 100% { transform: translateX(0) translateY(0) scale(1.02); opacity: 0.5; }
            50% { transform: translateX(25px) translateY(-20px) scale(0.98); opacity: 0.9; }
        }

        /* Animated Gradient Mesh */
        .gradient-mesh {
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background: conic-gradient(
                from 180deg at 50% 50%,
                var(--primary-red) 0deg,
                var(--secondary-red) 60deg,
                var(--primary-blue) 120deg,
                var(--secondary-blue) 180deg,
                var(--primary-red) 240deg,
                var(--secondary-red) 300deg,
                var(--primary-red) 360deg
            );
            filter: blur(100px);
            opacity: 0.3;
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Floating Particles */
        .particles {
            position: absolute;
            inset: 0;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            animation: float-up 10s linear infinite;
        }

        @keyframes float-up {
            from {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            to {
                transform: translateY(-100vh) rotate(720deg);
                opacity: 0;
            }
        }

        /* Glass Grid Overlay */
        .glass-grid {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 100px 100px;
            mask-image: radial-gradient(ellipse at center, transparent 0%, black 100%);
        }

        /* Main Container */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            padding: 20px;
        }

        /* Ultimate Premium Glass Card Container */
        .glass-card {
            width: 100%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 35px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 
                0 15px 50px 0 rgba(31, 38, 135, 0.6),
                0 5px 25px 0 rgba(255, 255, 255, 0.1),
                inset 0 0 0 1px rgba(255, 255, 255, 0.12),
                inset 0 2px 20px 0 rgba(255, 255, 255, 0.05);
            overflow: hidden;
            display: flex;
            min-height: 750px;
            position: relative;
            transform-style: preserve-3d;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.12) 0%, 
                rgba(255, 255, 255, 0.06) 25%,
                rgba(255, 255, 255, 0.02) 50%,
                rgba(255, 255, 255, 0.06) 75%,
                rgba(255, 255, 255, 0.12) 100%);
            pointer-events: none;
            z-index: 1;
        }

        .glass-card::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, 
                rgba(169, 0, 0, 0.4) 0%,
                rgba(63, 161, 221, 0.4) 25%,
                rgba(0, 81, 187, 0.4) 50%,
                rgba(193, 18, 31, 0.4) 75%,
                rgba(169, 0, 0, 0.4) 100%);
            border-radius: 37px;
            z-index: -1;
            animation: premiumBorderGlow 10s ease-in-out infinite;
            filter: blur(2px);
        }

        @keyframes premiumBorderGlow {
            0%, 100% { opacity: 0.4; transform: scale(1) rotate(0deg); }
            33% { opacity: 0.8; transform: scale(1.02) rotate(120deg); }
            66% { opacity: 0.6; transform: scale(0.98) rotate(240deg); }
        }

        /* Ultra Premium Left Panel - Multi-Layer Glass */
        .info-panel {
            flex: 1;
            padding: 60px;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.04) 0%, 
                rgba(255, 255, 255, 0.02) 50%,
                rgba(255, 255, 255, 0.04) 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .info-panel::before {
            content: '';
            position: absolute;
            top: -60%;
            left: -60%;
            width: 220%;
            height: 220%;
            background: radial-gradient(circle at 30% 50%, rgba(169, 0, 0, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 70% 30%, rgba(0, 81, 187, 0.12) 0%, transparent 60%);
            animation: premiumPulse 8s ease-in-out infinite;
        }

        .info-panel::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            pointer-events: none;
            z-index: 1;
        }

        @keyframes premiumPulse {
            0%, 100% { transform: scale(1) rotate(0deg); opacity: 0.4; }
            33% { transform: scale(1.2) rotate(120deg); opacity: 0.8; }
            66% { transform: scale(0.9) rotate(240deg); opacity: 0.6; }
        }

        .info-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(169, 0, 0, 0.1) 0%, transparent 50%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        /* Ultra Premium Logo Container */
        .logo-wrapper {
            position: relative;
            margin-bottom: 48px;
            text-align: center;
            z-index: 3;
        }

        .logo-glass {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 24px 48px;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.12) 0%, 
                rgba(255, 255, 255, 0.08) 50%,
                rgba(255, 255, 255, 0.12) 100%);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 8px 32px rgba(31, 38, 135, 0.3),
                inset 0 2px 10px rgba(255, 255, 255, 0.15);
        }

        .logo-glass::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            transform: translateX(-100%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .logo-img {
            height: 50px;
            filter: brightness(0) invert(1);
            position: relative;
            z-index: 1;
        }

        /* Glass Typography */
        .glass-title {
            font-size: 48px;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, rgba(255, 255, 255, 0.7) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 24px;
            letter-spacing: -1px;
            text-align: center;
        }

        .glass-subtitle {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            margin-bottom: 48px;
            line-height: 1.6;
        }

        /* Ultra Premium Glass Features */
        .glass-features {
            display: grid;
            gap: 20px;
            margin-bottom: 48px;
            z-index: 2;
            position: relative;
        }

        .glass-feature {
            padding: 20px;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.08) 0%, 
                rgba(255, 255, 255, 0.04) 50%,
                rgba(255, 255, 255, 0.08) 100%);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 4px 20px rgba(31, 38, 135, 0.15),
                inset 0 1px 6px rgba(255, 255, 255, 0.1);
        }

        .glass-feature::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.1), 
                transparent);
            transition: left 0.6s ease;
        }

        .glass-feature:hover::before {
            left: 100%;
        }

        .glass-feature:hover {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.12) 0%, 
                rgba(255, 255, 255, 0.08) 50%,
                rgba(255, 255, 255, 0.12) 100%);
            transform: translateX(15px) translateY(-3px) scale(1.02);
            box-shadow: 
                0 12px 40px rgba(31, 38, 135, 0.4),
                0 6px 20px rgba(0, 0, 0, 0.1),
                inset 0 2px 8px rgba(255, 255, 255, 0.2);
        }

        .feature-icon-wrapper {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-icon {
            font-size: 24px;
            color: white;
        }

        .feature-content h4 {
            font-size: 16px;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
        }

        .feature-content p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
        }

        /* Ultra Premium Testimonial Slider */
        .testimonial-wrapper {
            margin-top: auto;
            padding: 24px;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.08) 0%, 
                rgba(255, 255, 255, 0.04) 50%,
                rgba(255, 255, 255, 0.08) 100%);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 8px 32px rgba(31, 38, 135, 0.2),
                inset 0 1px 8px rgba(255, 255, 255, 0.1);
        }

        .testimonial-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                rgba(255, 255, 255, 0.1) 0%, 
                transparent 50%, 
                rgba(255, 255, 255, 0.1) 100%);
            pointer-events: none;
        }

        .swiper {
            width: 100%;
            height: 120px;
        }

        .testimonial-slide {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .testimonial-text {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            font-style: italic;
            margin-bottom: 12px;
        }

        .testimonial-author {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 600;
        }

        /* Ultra Premium Right Panel - Enhanced Glass Form */
        .form-panel {
            flex: 0 0 480px;
            padding: 60px;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.98) 0%, 
                rgba(255, 255, 255, 0.95) 50%,
                rgba(255, 255, 255, 0.98) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            backdrop-filter: blur(15px);
        }

        .form-panel::before {
            content: '';
            position: absolute;
            top: 30px;
            right: 30px;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(63, 161, 221, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: formGlassFloat 10s ease-in-out infinite;
            filter: blur(3px);
        }

        .form-panel::after {
            content: '';
            position: absolute;
            bottom: 40px;
            left: 40px;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(169, 0, 0, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: formGlassFloat2 8s ease-in-out infinite reverse;
            filter: blur(2px);
        }

        @keyframes formGlassFloat {
            0%, 100% { transform: translateY(0px) scale(1); opacity: 0.6; }
            50% { transform: translateY(-20px) scale(1.3); opacity: 1; }
        }

        @keyframes formGlassFloat2 {
            0%, 100% { transform: translateY(0px) scale(1); opacity: 0.4; }
            50% { transform: translateY(15px) scale(0.8); opacity: 0.8; }
        }

        /* Glass Form Header */
        .form-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .form-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px 0 rgba(169, 0, 0, 0.3);
        }

        .form-logo i {
            font-size: 28px;
            color: white;
        }

        .form-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 8px;
        }

        .form-subtitle {
            font-size: 16px;
            color: var(--gray-600);
        }

        /* Ultra Premium Glass Input Groups */
        .glass-input-group {
            margin-bottom: 24px;
            position: relative;
        }

        .glass-input-group::before {
            content: '';
            position: absolute;
            top: -5px;
            left: -5px;
            right: -5px;
            bottom: -5px;
            background: linear-gradient(45deg, 
                rgba(169, 0, 0, 0.05) 0%,
                rgba(63, 161, 221, 0.05) 50%,
                rgba(0, 81, 187, 0.05) 100%);
            border-radius: 15px;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .glass-input-group:focus-within::before {
            opacity: 1;
        }

        .glass-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .glass-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, 
                rgba(248, 250, 252, 0.9) 0%, 
                rgba(241, 245, 249, 0.95) 100%);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            transition: var(--transition-base);
            overflow: hidden;
            box-shadow: 
                0 4px 15px rgba(0, 0, 0, 0.05),
                inset 0 1px 5px rgba(255, 255, 255, 0.8);
        }

        .glass-input-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, transparent, rgba(0, 81, 187, 0.1), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .glass-input-wrapper:hover::before,
        .glass-input-wrapper:focus-within::before {
            transform: translateX(100%);
        }

        .glass-input-wrapper:hover {
            border-color: var(--gray-200);
        }

        .glass-input-wrapper:focus-within {
            border-color: var(--primary-blue);
            box-shadow: 
                0 0 0 4px rgba(0, 81, 187, 0.1),
                0 8px 25px rgba(0, 81, 187, 0.15),
                inset 0 2px 10px rgba(255, 255, 255, 0.9);
            transform: translateY(-2px) scale(1.02);
        }

        .glass-input {
            flex: 1;
            height: 56px;
            padding: 0 20px;
            font-size: 16px;
            font-weight: 500;
            color: var(--gray-900);
            background: transparent;
            border: none;
            outline: none;
            z-index: 1;
        }

        .glass-input-icon {
            padding: 0 20px;
            color: var(--gray-400);
            font-size: 20px;
            transition: var(--transition-base);
        }

        .glass-input-wrapper:focus-within .glass-input-icon {
            color: var(--primary-blue);
        }

        .password-toggle {
            background: none;
            border: none;
            padding: 0 20px;
            color: var(--gray-400);
            cursor: pointer;
            transition: var(--transition-base);
        }

        .password-toggle:hover {
            color: var(--gray-600);
        }

        /* Glass Checkbox */
        .glass-checkbox-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .glass-checkbox {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .glass-checkbox input {
            position: absolute;
            opacity: 0;
        }

        .glass-checkbox-box {
            width: 22px;
            height: 22px;
            background: var(--gray-100);
            border: 2px solid var(--gray-300);
            border-radius: 6px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-base);
        }

        .glass-checkbox input:checked ~ .glass-checkbox-box {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .glass-checkbox-box i {
            font-size: 14px;
            color: white;
            opacity: 0;
            transform: scale(0);
            transition: var(--transition-bounce);
        }

        .glass-checkbox input:checked ~ .glass-checkbox-box i {
            opacity: 1;
            transform: scale(1);
        }

        .glass-checkbox-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-700);
            user-select: none;
        }

        .forgot-link {
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-blue);
            text-decoration: none;
            position: relative;
            transition: var(--transition-base);
        }

        .forgot-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-blue);
            transition: width 0.3s;
        }

        .forgot-link:hover::after {
            width: 100%;
        }

        /* Ultra Premium Glass Submit Button */
        .glass-submit-btn {
            width: 100%;
            height: 56px;
            background: linear-gradient(135deg, 
                var(--primary-red) 0%, 
                var(--secondary-red) 50%, 
                var(--primary-red) 100%);
            border: none;
            border-radius: 15px;
            color: white;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: var(--transition-base);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
            box-shadow: 
                0 8px 25px rgba(169, 0, 0, 0.3),
                0 4px 15px rgba(0, 0, 0, 0.1),
                inset 0 2px 10px rgba(255, 255, 255, 0.2);
        }

        .glass-submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.4), 
                transparent);
            transition: left 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .glass-submit-btn:hover::before {
            left: 100%;
        }

        .glass-submit-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .glass-submit-btn:hover::before {
            width: 600px;
            height: 600px;
        }

        .glass-submit-btn:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 
                0 15px 40px rgba(169, 0, 0, 0.5),
                0 8px 25px rgba(0, 0, 0, 0.15),
                inset 0 3px 15px rgba(255, 255, 255, 0.3);
        }

        .glass-submit-btn:active {
            transform: translateY(0);
        }

        .glass-submit-btn span,
        .glass-submit-btn i {
            position: relative;
            z-index: 1;
        }

        .glass-submit-btn.loading {
            pointer-events: none;
            opacity: 0.9;
        }

        /* Glass Divider */
        .glass-divider {
            display: flex;
            align-items: center;
            margin: 32px 0;
            position: relative;
        }

        .glass-divider::before,
        .glass-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gray-300), transparent);
        }

        .glass-divider span {
            padding: 0 20px;
            font-size: 14px;
            color: var(--gray-500);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Alternative Login */
        .alt-login-wrapper {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
        }

        .alt-login-btn {
            flex: 1;
            height: 48px;
            background: var(--gray-50);
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-700);
            cursor: pointer;
            transition: var(--transition-base);
        }

        .alt-login-btn:hover {
            background: white;
            border-color: var(--gray-300);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Register Link */
        .register-wrapper {
            text-align: center;
            padding-top: 24px;
            border-top: 1px solid var(--gray-200);
        }

        .register-text {
            font-size: 14px;
            color: var(--gray-600);
            margin-bottom: 8px;
        }

        .register-link {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-blue);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition-base);
        }

        .register-link:hover {
            gap: 12px;
        }

        /* Glass Alert */
        .glass-alert {
            padding: 16px 20px;
            margin-bottom: 24px;
            border-radius: 12px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from { 
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glass-alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .glass-alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        .glass-alert-icon {
            font-size: 20px;
            flex-shrink: 0;
        }

        /* Loading Spinner */
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Trust Badges */
        .trust-badges {
            position: absolute;
            bottom: 40px;
            left: 60px;
            right: 60px;
            display: flex;
            justify-content: center;
            gap: 24px;
        }

        .trust-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--gray-600);
        }

        .trust-badge i {
            font-size: 16px;
            color: var(--success);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .glass-card {
                flex-direction: column;
                max-width: 600px;
            }
            
            .info-panel {
                display: none;
            }
            
            .form-panel {
                flex: 1;
                border-radius: 30px;
            }
        }

        @media (max-width: 576px) {
            .form-panel {
                padding: 40px 24px;
            }
            
            .form-title {
                font-size: 28px;
            }
            
            .trust-badges {
                flex-direction: column;
                align-items: center;
                gap: 12px;
                left: 24px;
                right: 24px;
                bottom: 24px;
            }
        }

        /* Premium Loading Overlay */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .loading-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .loading-content {
            text-align: center;
        }

        .loading-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, var(--primary-red), var(--secondary-red));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s ease-in-out infinite;
        }

        .loading-logo i {
            font-size: 40px;
            color: white;
        }

        .loading-text {
            font-size: 18px;
            color: white;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .loading-progress {
            width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            margin: 0 auto;
        }

        .loading-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-red), var(--secondary-red));
            width: 0;
            transition: width 2s ease-out;
        }

        .loading-overlay.active .loading-progress-bar {
            width: 90%;
        }
    </style>
</head>

<body>
    <!-- Ultra Premium Multi-Layer Background Container -->
    <div class="background-container">
        <div class="gradient-mesh"></div>
        <div class="glass-layer-1"></div>
        <div class="glass-layer-2"></div>
        <div class="particles" id="particles"></div>
        <div class="glass-grid"></div>
    </div>

    <!-- Main Login Container -->
    <div class="login-container">
        <div class="glass-card">
            <!-- Left Panel - Info Section -->
            <div class="info-panel">
                <!-- Logo -->
                <div class="logo-wrapper" data-aos="fade-up" data-aos-delay="100">
                    <div class="logo-glass">
                        <img src="{{ asset('admin/src/images/emtialogo.png') }}" alt="ALLEMTIA" class="logo-img">
                    </div>
                </div>

                <!-- Title -->
                <h1 class="glass-title" data-aos="fade-up" data-aos-delay="200">
                    Partner Portal
                </h1>
                <p class="glass-subtitle" data-aos="fade-up" data-aos-delay="300">
                    Türkiye'nin en büyük B2B e-ticaret ekosisteminde yerinizi alın
                </p>

                <!-- Glass Features -->
                <div class="glass-features" data-aos="fade-up" data-aos-delay="400">
                    <div class="glass-feature">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-graph-up-arrow feature-icon"></i>
                        </div>
                        <div class="feature-content">
                            <h4>%300 Büyüme</h4>
                            <p>Satıcılarımız ilk yılda ortalama büyüme oranı</p>
                        </div>
                    </div>
                    
                    <div class="glass-feature">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-shield-check feature-icon"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Güvenli Ticaret</h4>
                            <p>256-bit SSL şifreleme ve güvenli ödeme altyapısı</p>
                        </div>
                    </div>
                    
                    <div class="glass-feature">
                        <div class="feature-icon-wrapper">
                            <i class="bi bi-headset feature-icon"></i>
                        </div>
                        <div class="feature-content">
                            <h4>7/24 Destek</h4>
                            <p>Özel satıcı destek hattı ve canlı yardım</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial Slider -->
                <div class="testimonial-wrapper" data-aos="fade-up" data-aos-delay="500">
                    <div class="swiper testimonialSwiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="testimonial-slide">
                                    <p class="testimonial-text">"ALLEMTIA ile çalışmaya başladığımızdan beri satışlarımız 5 katına çıktı."</p>
                                    <p class="testimonial-author">— Ahmet Y., Metal Üreticisi</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="testimonial-slide">
                                    <p class="testimonial-text">"Profesyonel ekip ve güçlü altyapı sayesinde işlerimizi büyüttük."</p>
                                    <p class="testimonial-author">— Mehmet K., Çelik Tedarikçisi</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="testimonial-slide">
                                    <p class="testimonial-text">"B2B satışlarımızı dijitale taşıdık, verimliliğimiz arttı."</p>
                                    <p class="testimonial-author">— Ayşe D., Endüstriyel Malzeme</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Login Form -->
            <div class="form-panel">
                <!-- Form Header -->
                <div class="form-header" data-aos="fade-up">
                    <div class="form-logo">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h2 class="form-title">Hoş Geldiniz</h2>
                    <p class="form-subtitle">Hesabınıza giriş yapın</p>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="glass-alert glass-alert-success" data-aos="fade-down">
                        <i class="bi bi-check-circle-fill glass-alert-icon"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="glass-alert glass-alert-error" data-aos="fade-down">
                        <i class="bi bi-exclamation-circle-fill glass-alert-icon"></i>
                        <div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('seller.login.submit') }}" method="POST" id="loginForm" data-aos="fade-up" data-aos-delay="100">
                    @csrf
                    
                    <!-- Email Input -->
                    <div class="glass-input-group">
                        <label class="glass-label" for="email">E-posta Adresi</label>
                        <div class="glass-input-wrapper">
                            <i class="bi bi-envelope glass-input-icon"></i>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="glass-input" 
                                placeholder="ornek@sirket.com"
                                value="{{ old('email') }}"
                                required 
                                autocomplete="email"
                                autofocus
                            >
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="glass-input-group">
                        <label class="glass-label" for="password">Şifre</label>
                        <div class="glass-input-wrapper">
                            <i class="bi bi-lock glass-input-icon"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="glass-input" 
                                placeholder="••••••••"
                                required 
                                autocomplete="current-password"
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="glass-checkbox-wrapper">
                        <label class="glass-checkbox">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span class="glass-checkbox-box">
                                <i class="bi bi-check"></i>
                            </span>
                            <span class="glass-checkbox-label">Beni hatırla</span>
                        </label>
                        <a href="#" class="forgot-link">Şifremi unuttum</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="glass-submit-btn" id="submitBtn">
                        <span>GİRİŞ YAP</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>

                <!-- Divider -->
                <div class="glass-divider" data-aos="fade-up" data-aos-delay="200">
                    <span>veya</span>
                </div>

                <!-- Alternative Login -->
                <div class="alt-login-wrapper" data-aos="fade-up" data-aos-delay="300">
                    <button class="alt-login-btn">
                        <i class="bi bi-google"></i>
                        Google
                    </button>
                    <button class="alt-login-btn">
                        <i class="bi bi-microsoft"></i>
                        Microsoft
                    </button>
                </div>

                <!-- Register Link -->
                <div class="register-wrapper" data-aos="fade-up" data-aos-delay="400">
                    <p class="register-text">Henüz satıcı değil misiniz?</p>
                    <a href="#" class="register-link">
                        Hemen başvurun
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="trust-badges">
                    <div class="trust-badge">
                        <i class="bi bi-shield-check"></i>
                        <span>SSL Güvenli</span>
                    </div>
                    <div class="trust-badge">
                        <i class="bi bi-patch-check"></i>
                        <span>ISO 27001</span>
                    </div>
                    <div class="trust-badge">
                        <i class="bi bi-award"></i>
                        <span>PCI DSS</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-logo">
                <i class="bi bi-box-seam"></i>
            </div>
            <p class="loading-text">Giriş yapılıyor...</p>
            <div class="loading-progress">
                <div class="loading-progress-bar"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            easing: 'ease-out-cubic'
        });

        // Initialize Swiper
        var swiper = new Swiper(".testimonialSwiper", {
            spaceBetween: 30,
            centeredSlides: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });

        // Generate Particles
        function generateParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 10 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                particlesContainer.appendChild(particle);
            }
        }
        generateParticles();

        // Password Toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'bi bi-eye';
            }
        }

        // Form Submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('span');
            const btnIcon = submitBtn.querySelector('i');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            // Show loading state
            submitBtn.classList.add('loading');
            btnText.textContent = 'Giriş yapılıyor';
            btnIcon.className = 'spinner';
            
            // Show premium loading overlay
            setTimeout(() => {
                loadingOverlay.classList.add('active');
            }, 300);
        });

        // This is now handled above in the enhanced version

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.glass-alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Prevent resubmission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Enhanced 3D glass card hover effect
        const glassCard = document.querySelector('.glass-card');
        if (glassCard) {
            glassCard.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;
                
                this.style.transform = `perspective(1200px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(5px)`;
            });
            
            glassCard.addEventListener('mouseleave', function() {
                this.style.transform = 'perspective(1200px) rotateX(0deg) rotateY(0deg) translateZ(0px)';
            });
        }

        // Enhanced ripple effect for buttons
        document.querySelectorAll('.glass-submit-btn, .alt-login-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: radial-gradient(circle, rgba(255,255,255,0.6) 0%, transparent 70%);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                    pointer-events: none;
                `;
                
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 800);
            });
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Enhanced glass parallax effect on mouse move
        document.addEventListener('mousemove', function(e) {
            const x = e.clientX / window.innerWidth;
            const y = e.clientY / window.innerHeight;
            
            // Move glass layers
            const glassLayer1 = document.querySelector('.glass-layer-1');
            const glassLayer2 = document.querySelector('.glass-layer-2');
            
            if (glassLayer1) {
                glassLayer1.style.transform = `translate(${x * 10}px, ${y * 10}px)`;
            }
            if (glassLayer2) {
                glassLayer2.style.transform = `translate(${x * -8}px, ${y * -8}px)`;
            }
            
            // Enhanced particle movement
            const particles = document.querySelectorAll('.particle');
            particles.forEach((particle, index) => {
                const speed = (index + 1) * 0.3;
                const xPos = x * speed * 8;
                const yPos = y * speed * 8;
                
                particle.style.transform = `translate(${xPos}px, ${yPos}px) scale(${1 + x * 0.2})`;
            });
        });

        // Enhanced input field glass effects
        const glassInputs = document.querySelectorAll('.glass-input');
        glassInputs.forEach(input => {
            input.addEventListener('focus', function() {
                const wrapper = this.closest('.glass-input-wrapper');
                wrapper.style.transform = 'translateY(-3px) scale(1.02)';
                wrapper.style.filter = 'drop-shadow(0 8px 25px rgba(0, 81, 187, 0.2))';
            });
            
            input.addEventListener('blur', function() {
                const wrapper = this.closest('.glass-input-wrapper');
                wrapper.style.transform = 'translateY(0) scale(1)';
                wrapper.style.filter = 'none';
            });
            
            // Add typing effect
            input.addEventListener('input', function() {
                const wrapper = this.closest('.glass-input-wrapper');
                wrapper.style.background = this.value ? 
                    'linear-gradient(135deg, rgba(248, 250, 252, 0.95) 0%, rgba(241, 245, 249, 1) 100%)' :
                    'linear-gradient(135deg, rgba(248, 250, 252, 0.9) 0%, rgba(241, 245, 249, 0.95) 100%)';
            });
        });

        // Enhanced glass feature hover effects
        const glassFeatures = document.querySelectorAll('.glass-feature');
        glassFeatures.forEach(feature => {
            feature.addEventListener('mouseenter', function() {
                this.style.filter = 'drop-shadow(0 12px 40px rgba(31, 38, 135, 0.4))';
            });
            
            feature.addEventListener('mouseleave', function() {
                this.style.filter = 'none';
            });
        });
    </script>
</body>
</html>