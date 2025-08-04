<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - ALLEMTIA</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('admin/src/images/favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Admin CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/admin-modern.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/layout.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar Component -->
        @include('admin.partials.sidebar')
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header Component -->
            @include('admin.partials.header')
            
            <!-- Page Content -->
            <div class="admin-content">
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
                
                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('admin/js/admin-modern.js') }}"></script>
    <script src="{{ asset('admin/js/layout.js') }}"></script>
    
    @stack('scripts')
</body>
</html>