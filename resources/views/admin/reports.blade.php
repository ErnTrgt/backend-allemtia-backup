@extends('layouts.admin')

@section('content')
    <div>
        <h1>Raporlar</h1>
        <p>Toplam Gelir: {{ $totalRevenue }}</p>
        <table>
            <tr>
                <th>Sipariş ID</th>
                <th>Kullanıcı</th>
                <th>Ürün</th>
                <th>Toplam Fiyat</th>
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
