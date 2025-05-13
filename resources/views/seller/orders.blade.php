@extends('seller.layout')
@section('title', 'Seller Orders')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>My Orders</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Orders Table</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th class="table-plus">Order ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th class="datatable-nosort">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="table-plus">{{ $order->id }}</td>
                                    <td>{{ $order->product->name }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>{{ $order->total_price }}</td>
                                    <td>{{ ucfirst($order->status) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
