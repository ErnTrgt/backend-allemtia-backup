@extends('layouts.layout')

@section('title', 'Orders')

@section('content')
    <div class="title pb-20">
        <h2 class="h3 mb-0">Orders</h2>
    </div>

    <div class="card-box mb-30">
        <div class="pd-20">
            <h4 class="text-blue h4">Orders List</h4>
        </div>
        <div class="pb-20">
            <table class="data-table table stripe hover nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="datatable-nosort">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->customer_name }}</td> <!-- Order modelinde customer_name varsa -->
                            <td>${{ $order->total }}</td>
                            <td>
                                <span class="badge {{ $order->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
