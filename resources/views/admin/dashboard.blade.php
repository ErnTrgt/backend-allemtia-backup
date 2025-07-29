@extends('layouts.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <!-- Page Header -->
        <div class="page-header mb-30">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="title">
                        <h4>Kontrol Paneli Genel Bakış</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Anasayfa</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Kontrol Paneli</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 text-right">
                    <div class="welcome-text">
                        <h6 class="mb-0">Hoşgeldiniz, <span class="text-primary">{{ Auth::user()->name }}</span>!</h6>
                        <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <!-- Total Users Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-30">
                <a href="{{ route('admin.users') }}" class="card-link">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="widget-data">
                                <div class="weight-700 font-30 text-dark counter">{{ $userCount }}</div>
                                <div class="font-14 text-secondary weight-500 mt-2">Toplam Kullanıcılar</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-primary" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-primary">
                                <div class="icon">
                                    <i class="icon-copy dw dw-user1 text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Admin Users Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-30">
                <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="card-link">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="widget-data">
                                <div class="weight-700 font-30 text-dark counter">{{ $adminCount }}</div>
                                <div class="font-14 text-secondary weight-500 mt-2">Yöneticiler</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-danger" role="progressbar" 
                                        style="width: {{ $userCount > 0 ? ($adminCount / $userCount * 100) : 0 }}%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-danger">
                                <div class="icon">
                                    <i class="icon-copy dw dw-user text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Sellers Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-30">
                <a href="{{ route('admin.users', ['role' => 'seller']) }}" class="card-link">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="widget-data">
                                <div class="weight-700 font-30 text-dark counter">{{ $sellerCount }}</div>
                                <div class="font-14 text-secondary weight-500 mt-2">Aktif Satıcılar</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-info" role="progressbar" 
                                        style="width: {{ $userCount > 0 ? ($sellerCount / $userCount * 100) : 0 }}%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-info">
                                <div class="icon">
                                    <i class="icon-copy dw dw-store text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Buyers Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-30">
                <a href="{{ route('admin.users', ['role' => 'buyer']) }}" class="card-link">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="widget-data">
                                <div class="weight-700 font-30 text-dark counter">{{ $buyerCount }}</div>
                                <div class="font-14 text-secondary weight-500 mt-2">Mutlu Müşteriler</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-success" role="progressbar" 
                                        style="width: {{ $userCount > 0 ? ($buyerCount / $userCount * 100) : 0 }}%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-success">
                                <div class="icon">
                                    <i class="icon-copy dw dw-user-2 text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Products Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-30">
                <a href="{{ route('admin.products') }}" class="card-link">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="widget-data">
                                <div class="weight-700 font-30 text-dark counter">{{ $productCount }}</div>
                                <div class="font-14 text-secondary weight-500 mt-2">Toplam Ürünler</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-warning" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-warning">
                                <div class="icon">
                                    <i class="icon-copy dw dw-shopping-bag text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Orders Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-30">
                <a href="{{ route('admin.orders') }}" class="card-link">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="widget-data">
                                <div class="weight-700 font-30 text-dark counter">{{ $orderCount }}</div>
                                <div class="font-14 text-secondary weight-500 mt-2">Toplam Siparişler</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-teal" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-teal">
                                <div class="icon">
                                    <i class="icon-copy dw dw-shopping-cart text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Categories Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-30">
                <a href="{{ route('admin.categories') }}" class="card-link">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="widget-data">
                                <div class="weight-700 font-30 text-dark counter">{{ $totalCategories }}</div>
                                <div class="font-14 text-secondary weight-500 mt-2">Kategoriler</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-purple" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-purple">
                                <div class="icon">
                                    <i class="icon-copy dw dw-list text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Subcategories Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-30">
                <a href="{{ route('admin.categories') }}" class="card-link">
                    <div class="card-box height-100-p widget-style3 hover-card">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="widget-data">
                                <div class="weight-700 font-30 text-dark counter">{{ $totalSubcategories }}</div>
                                <div class="font-14 text-secondary weight-500 mt-2">Alt Kategoriler</div>
                                <div class="progress mt-3 mb-1" style="height: 5px;">
                                    <div class="progress-bar bg-gradient-pink" role="progressbar" style="width: 100%;"></div>
                                </div>
                            </div>
                            <div class="widget-icon gradient-icon bg-gradient-pink">
                                <div class="icon">
                                    <i class="icon-copy dw dw-list2 text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Category Requests Section -->
        <div class="row">
            <div class="col-lg-12 mb-30">
                <div class="card-box pd-30">
                    <div class="d-flex justify-content-between align-items-center mb-30">
                        <h4 class="text-blue h4 mb-0">Kategori İstekleri Genel Bakış</h4>
                        <a href="{{ url('/admin/category-requests') }}" class="btn btn-sm btn-primary">
                            Tümünü Gör <i class="icon-copy dw dw-right-arrow1"></i>
                        </a>
                    </div>
                    
                    <!-- Request Stats -->
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-20">
                            <div class="request-stat-box pending">
                                <div class="icon-box">
                                    <i class="icon-copy dw dw-clock"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="mb-0">{{ $pendingRequests }}</h3>
                                    <span>Beklemede</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-20">
                            <div class="request-stat-box approved">
                                <div class="icon-box">
                                    <i class="icon-copy dw dw-checked"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="mb-0">{{ $approvedRequests }}</h3>
                                    <span>Onaylandı</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-20">
                            <div class="request-stat-box rejected">
                                <div class="icon-box">
                                    <i class="icon-copy dw dw-cancel"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="mb-0">{{ $rejectedRequests }}</h3>
                                    <span>Reddedildi</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-20">
                            <div class="request-stat-box total">
                                <div class="icon-box">
                                    <i class="icon-copy dw dw-analytics1"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="mb-0">{{ $totalRequests }}</h3>
                                    <span>Toplam</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories and Subcategories Table -->
        <div class="card-box mb-30">
            <div class="pd-20 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="text-blue h4 mb-0">Kategoriler Yönetimi</h4>
                    <p class="text-muted mb-0">Tüm kategorilerin ve alt kategorilerin genel bakışı</p>
                </div>
                <a href="{{ route('admin.categories') }}" class="btn btn-primary">
                    <i class="icon-copy dw dw-edit2"></i> Kategorileri Yönet
                </a>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th class="table-plus">Kategori</th>
                            <th>Alt Kategoriler</th>
                            <th>Toplam Ürünler</th>
                            <th class="datatable-nosort">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categoriesWithSubcategories as $category)
                            <tr>
                                <td class="table-plus">
                                    <div class="category-info">
                                        <div class="category-icon">
                                            <i class="icon-copy dw dw-folder1"></i>
                                        </div>
                                        <h6 class="mb-0">{{ $category->name }}</h6>
                                    </div>
                                </td>
                                <td>
                                    @if ($category->children->isEmpty())
                                        <span class="badge badge-light">Alt Kategoriler Yok</span>
                                    @else
                                        <div class="subcategory-list">
                                            @foreach ($category->children->take(3) as $child)
                                                <span class="badge badge-info">{{ $child->name }}</span>
                                            @endforeach
                                            @if($category->children->count() > 3)
                                                <span class="badge badge-secondary">+{{ $category->children->count() - 3 }} daha fazla</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-pill badge-primary">{{ $category->children->count() }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                            <i class="dw dw-more"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                            <a class="dropdown-item" href="{{ route('admin.categories') }}">
                                                <i class="dw dw-eye"></i> Detayları Gör
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.categories') }}">
                                                <i class="dw dw-edit2"></i> Düzenle
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        /* Card hover effects */
        .hover-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
        }
        
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .card-link {
            text-decoration: none !important;
        }
        
        /* Gradient backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(45deg, #1976d2, #2196f3);
        }
        
        .bg-gradient-danger {
            background: linear-gradient(45deg, #d32f2f, #f44336);
        }
        
        .bg-gradient-info {
            background: linear-gradient(45deg, #0288d1, #03a9f4);
        }
        
        .bg-gradient-success {
            background: linear-gradient(45deg, #388e3c, #4caf50);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(45deg, #f57c00, #ff9800);
        }
        
        .bg-gradient-teal {
            background: linear-gradient(45deg, #00796b, #009688);
        }
        
        .bg-gradient-purple {
            background: linear-gradient(45deg, #7b1fa2, #9c27b0);
        }
        
        .bg-gradient-pink {
            background: linear-gradient(45deg, #c2185b, #e91e63);
        }
        
        /* Widget icon styling */
        .gradient-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .gradient-icon .icon {
            font-size: 30px;
        }
        
        /* Counter animation */
        .counter {
            font-size: 36px;
            line-height: 1;
        }
        
        /* Request stat boxes */
        .request-stat-box {
            display: flex;
            align-items: center;
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .request-stat-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .request-stat-box.pending {
            border-left: 4px solid #ff9800;
        }
        
        .request-stat-box.approved {
            border-left: 4px solid #4caf50;
        }
        
        .request-stat-box.rejected {
            border-left: 4px solid #f44336;
        }
        
        .request-stat-box.total {
            border-left: 4px solid #2196f3;
        }
        
        .request-stat-box .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
        }
        
        .request-stat-box.pending .icon-box {
            background: rgba(255, 152, 0, 0.1);
            color: #ff9800;
        }
        
        .request-stat-box.approved .icon-box {
            background: rgba(76, 175, 80, 0.1);
            color: #4caf50;
        }
        
        .request-stat-box.rejected .icon-box {
            background: rgba(244, 67, 54, 0.1);
            color: #f44336;
        }
        
        .request-stat-box.total .icon-box {
            background: rgba(33, 150, 243, 0.1);
            color: #2196f3;
        }
        
        .stat-content h3 {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .stat-content span {
            color: #6b7280;
            font-size: 14px;
        }
        
        /* Category table styling */
        .category-info {
            display: flex;
            align-items: center;
        }
        
        .category-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(33, 150, 243, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #2196f3;
            font-size: 20px;
        }
        
        .subcategory-list {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .subcategory-list .badge {
            font-weight: 500;
            padding: 5px 10px;
        }
        
        /* Welcome text */
        .welcome-text {
            padding: 10px 0;
        }
        
        /* Page header styling */
        .page-header {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        
        /* Progress bar animation */
        .progress-bar {
            animation: progressAnimation 1s ease-out;
        }
        
        @keyframes progressAnimation {
            from {
                width: 0;
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .counter {
                font-size: 28px;
            }
            
            .gradient-icon {
                width: 60px;
                height: 60px;
            }
            
            .welcome-text {
                text-align: left !important;
                margin-top: 15px;
            }
        }
    </style>

    <script>
        // Counter animation - Fixed version
        document.addEventListener('DOMContentLoaded', function() {
            // Counter animation with safety checks
            const counters = document.querySelectorAll('.counter');
            
            counters.forEach(counter => {
                const target = parseInt(counter.innerText) || 0;
                
                // If target is 0 or NaN, skip animation
                if (!target || target === 0) {
                    counter.innerText = '0';
                    return;
                }
                
                const duration = 1000; // 1 second
                const increment = target / (duration / 16); // 60fps
                let current = 0;
                let animationId = null;
                
                const updateCounter = () => {
                    current += increment;
                    
                    if (current < target) {
                        counter.innerText = Math.floor(current);
                        animationId = requestAnimationFrame(updateCounter);
                    } else {
                        counter.innerText = target;
                        if (animationId) {
                            cancelAnimationFrame(animationId);
                        }
                    }
                };
                
                // Start animation
                requestAnimationFrame(updateCounter);
            });
        });
    </script>
@endsection