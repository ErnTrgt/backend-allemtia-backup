@extends('layouts.layout')

@section('title', 'Seller Products')

@section('content')
    <div class="title pb-20">
        <h2 class="h3 mb-0">Products of {{ $seller->name }}</h2>
    </div>

    <div class="card-box mb-30">
        <div class="pd-20">
            <h4 class="text-blue h4">Ürünler</h4>
        </div>
        <div class="pb-20">
            <table class="data-table table stripe hover nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ürün Adı</th>
                        <th>Fiyat</th>
                        <th>Stok</th>
                        <th class="datatable-nosort">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($seller->products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $product->name }}</td>
                            <td>${{ $product->price }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary">Görüntüle</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
