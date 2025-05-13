@extends('layouts.admin')

@section('content')
    <div>
        <h1>Reports</h1>
        <p>Total Revenue: {{ $totalRevenue }}</p>
        <table>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Product</th>
                <th>Total Price</th>
            </tr>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td>{{ $order->product->name }}</td>
                    <td>{{ $order->total_price }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
