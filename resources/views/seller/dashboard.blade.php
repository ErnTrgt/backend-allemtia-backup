@extends('seller.layout')

@section('title', 'Seller Dashboard')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Seller Dashboard</h2>
            </div>

            <div class="row">
                <!-- Ürün Sayısı -->
                <div class="col-xl-6 col-lg-6 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <a href="{{ route('seller.products') }}" class="d-flex flex-wrap text-decoration-none">
                            <div class="widget-data">
                                <div class="weight-700 font-24 text-dark">{{ $productCount }}</div>
                                <div class="font-14 text-secondary weight-500">Products</div>
                            </div>
                            <div class="widget-icon">
                                <div class="icon" data-color="#00eccf">
                                    <i class="icon-copy dw dw-box"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Sipariş Sayısı -->
                <div class="col-xl-6 col-lg-6 col-md-6 mb-20">
                    <div class="card-box height-100-p widget-style3">
                        <a href="{{ route('seller.orders') }}" class="d-flex flex-wrap text-decoration-none">
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
                                        <td>Pending Category Requests</td>
                                        <td>{{ $pendingRequests }}</td>
                                        <td>
                                            <a href="{{ url('/seller/category-requests?status=pending') }}"
                                                class="btn btn-primary btn-sm">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Approved Category Requests</td>
                                        <td>{{ $approvedRequests }}</td>
                                        <td>
                                            <a href="{{ url('/seller/category-requests?status=approved') }}"
                                                class="btn btn-primary btn-sm">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Rejected Category Requests</td>
                                        <td>{{ $rejectedRequests }}</td>
                                        <td>
                                            <a href="{{ url('/seller/category-requests?status=rejected') }}"
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

        </div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
