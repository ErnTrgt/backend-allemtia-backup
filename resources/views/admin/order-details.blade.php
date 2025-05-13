@extends('layouts.layout')

@section('title', 'Order Details')

@section('content')
    <div class="title pb-20">
        <h2 class="h3 mb-0">Order Details</h2>
    </div>

    <div class="card-box">
        <h4>Order #{{ $order->id }}</h4>
        <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
        <p><strong>Total:</strong> ${{ $order->total }}</p>
        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y') }}</p>
        <hr>
        <h5>Order Items</h5>
        <ul>
            @foreach ($order->items as $item)
                <li>{{ $item->product_name }} - ${{ $item->price }} x {{ $item->quantity }}</li>
            @endforeach
        </ul>
    </div>
@endsection
