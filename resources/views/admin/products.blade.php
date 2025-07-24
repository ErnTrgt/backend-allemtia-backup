@extends('layouts.layout')

@section('title', 'Tüm Ürünler')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Ürün Yönetimi</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Ana Sayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Ürünler</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                Satıcıya Göre Filtrele
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('admin.products') }}">Tümü</a>
                                @foreach ($sellers as $seller)
                                    <a class="dropdown-item"
                                        href="{{ route('admin.products', ['seller_id' => $seller->id]) }}">
                                        {{ $seller->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Ürün Listesi</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Resim</th>
                                <th>Ürün Adı</th>
                                <th>Fiyat</th>
                                <th>Stok</th>
                                <th>Durum</th>
                                <th>Satıcı</th>
                                <th class="datatable-nosort">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                                alt="Product Image" width="80" height="80">
                                        @else
                                            <span>Resim Yok</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>${{ $product->price }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <span class="badge {{ $product->status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $product->status ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.seller.products', $product->user->id) }}">
                                            {{ $product->user->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                                href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.product.details', $product->id) }}">
                                                    <i class="dw dw-eye"></i> Görüntüle
                                                </a>
                                                <a class="dropdown-item" data-toggle="modal"
                                                    data-target="#editProductModal{{ $product->id }}" href="#">
                                                    <i class="dw dw-edit2"></i> Düzenle
                                                </a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('toggle-status-{{ $product->id }}').submit();">
                                                    <i class="dw {{ $product->status ? 'dw-ban' : 'dw-check' }}"></i>
                                                    {{ $product->status ? 'Pasifleştir' : 'Aktifleştir' }}
                                                </a>
                                                <form id="toggle-status-{{ $product->id }}"
                                                    action="{{ route('admin.product.toggleStatus', $product->id) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('delete-product-{{ $product->id }}').submit();">
                                                    <i class="dw dw-delete-3"></i> Sil
                                                </a>
                                                <form id="delete-product-{{ $product->id }}"
                                                    action="{{ route('admin.product.delete', $product->id) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Product Modal -->
                                <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Ürün Düzenle</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-hidden="true">×</button>
                                            </div>
                                            <form action="{{ route('admin.product.update', $product->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="productName">Ürün Adı</label>
                                                        <input type="text" name="name" id="productName"
                                                            class="form-control" value="{{ $product->name }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="productPrice">Fiyat</label>
                                                        <input type="number" name="price" id="productPrice"
                                                            class="form-control" value="{{ $product->price }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="productStock">Stok</label>
                                                        <input type="number" name="stock" id="productStock"
                                                            class="form-control" value="{{ $product->stock }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="productDescription">Açıklama</label>
                                                        <textarea name="description" id="productDescription" class="form-control" rows="4">{{ $product->description }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="editProductImages">Ek Resimler</label>
                                                        <input type="file" name="images[]" id="editProductImages"
                                                            class="form-control-file" multiple>
                                                        @if ($product->images->isNotEmpty())
                                                            <div class="mt-2">
                                                                <strong>Mevcut Resimler:</strong>
                                                                @foreach ($product->images as $image)
                                                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                                                        alt="Existing Image" width="60"
                                                                        height="60" class="mr-2">
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Kapat</button>
                                                    <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Edit Product Modal End -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection