@extends('layouts.layout')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="title pb-20">
        <h2 class="h3 mb-0">Admin Dashboard</h2>
    </div>

    <div class="row">
        <!-- Tüm Kullanıcılar -->
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <a href="{{ route('admin.users') }}" class="d-flex flex-wrap text-decoration-none">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">{{ $userCount }}</div>
                        <div class="font-14 text-secondary weight-500">Users</div>
                    </div>
                    <div class="widget-icon">
                        <div class="icon" data-color="#00eccf">
                            <i class="icon-copy dw dw-user1"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Admin Kullanıcılar -->
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <a href="{{ route('admin.users', ['role' => 'admin']) }}" class="d-flex flex-wrap text-decoration-none">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">{{ $adminCount }}</div>
                        <div class="font-14 text-secondary weight-500">Admins</div>
                    </div>
                    <div class="widget-icon">
                        <div class="icon" data-color="#f44336">
                            <i class="icon-copy dw dw-user"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Satıcılar -->
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <a href="{{ route('admin.users', ['role' => 'seller']) }}" class="d-flex flex-wrap text-decoration-none">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">{{ $sellerCount }}</div>
                        <div class="font-14 text-secondary weight-500">Sellers</div>
                    </div>
                    <div class="widget-icon">
                        <div class="icon" data-color="#3f51b5">
                            <i class="icon-copy dw dw-store"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Alıcılar -->
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <a href="{{ route('admin.users', ['role' => 'buyer']) }}" class="d-flex flex-wrap text-decoration-none">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">{{ $buyerCount }}</div>
                        <div class="font-14 text-secondary weight-500">Clients</div>
                    </div>
                    <div class="widget-icon">
                        <div class="icon" data-color="#4caf50">
                            <i class="icon-copy dw dw-user-2"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Ürünler -->
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <a href="{{ route('admin.products') }}" class="d-flex flex-wrap text-decoration-none">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">{{ $productCount }}</div>
                        <div class="font-14 text-secondary weight-500">Products</div>
                    </div>
                    <div class="widget-icon">
                        <div class="icon" data-color="#f39c12">
                            <i class="icon-copy dw dw-shopping-bag"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Siparişler -->
        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
            <div class="card-box height-100-p widget-style3">
                <a href="{{ route('admin.orders') }}" class="d-flex flex-wrap text-decoration-none">
                    <div class="widget-data">
                        <div class="weight-700 font-24 text-dark">{{ $orderCount }}</div>
                        <div class="font-14 text-secondary weight-500">Orders</div>
                    </div>
                    <div class="widget-icon">
                        <div class="icon" data-color="#4caf50">
                            <i class="icon-copy dw dw-shopping-cart"></i>
                        </div>
                    </div>
                </a>

            </div>
        </div>




    </div>
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Category Requests Summary</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Category Requests</td>
                                <td>{{ $totalRequests }}</td>
                                <td>
                                    <a href="{{ url('/admin/category-requests') }}" class="btn btn-primary btn-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Pending Requests</td>
                                <td>{{ $pendingRequests }}</td>
                                <td>
                                    <a href="{{ url('/admin/category-requests?status=pending') }}"
                                        class="btn btn-primary btn-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Approved Requests</td>
                                <td>{{ $approvedRequests }}</td>
                                <td>
                                    <a href="{{ url('/admin/category-requests?status=approved') }}"
                                        class="btn btn-primary btn-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>Rejected Requests</td>
                                <td>{{ $rejectedRequests }}</td>
                                <td>
                                    <a href="{{ url('/admin/category-requests?status=rejected') }}"
                                        class="btn btn-primary btn-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 mb-30">
                    <div class="card-box">
                        <h5>Total Categories</h5>
                        <h4>{{ $totalCategories }}</h4>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 mb-30">
                    <div class="card-box">
                        <h5>Total Subcategories</h5>
                        <h4>{{ $totalSubcategories }}</h4>
                    </div>
                </div>
            </div>

            <!-- Categories and Subcategories Table -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Categories and Subcategories</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Subcategories</th>
                                <th>Actions</th> <!-- Yeni sütun -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categoriesWithSubcategories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        @if ($category->subcategories->isEmpty())
                                            <span class="text-muted">No Subcategories</span>
                                        @else
                                            <ul>
                                                @foreach ($category->subcategories as $subcategory)
                                                    <li>{{ $subcategory->name }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.categories') }}" class="btn btn-primary btn-sm">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@endsection
