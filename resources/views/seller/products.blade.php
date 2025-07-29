@extends('seller.layout')
@section('title', 'Seller Products')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Ürünlerim</h4>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">Yeni Ürün Ekle</a>
                    </div>
                </div>
            </div>

            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Ürünler Tablosu</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th class="table-plus datatable-nosort">Görsel</th>
                                <th>Adı</th>
                                <th>Açıklama</th>
                                <th>Fiyat</th>
                                <th>Stok</th>
                                <th>Durum</th>
                                <th class="datatable-nosort">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td class="table-plus">
                                        @if ($product->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                                alt="Product Image" width="100" height="100">
                                        @else
                                            <span>Görsel Yok</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->description }}</td>
                                    <td>${{ $product->price }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <span class="badge {{ $product->status ? 'badge-success' : 'badge-danger' }}">
                                            {{ $product->status ? 'Aktif' : 'Deaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                                href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                                <a class="dropdown-item"
                                                    href="{{ route('seller.products.details', $product->id) }}">
                                                    <i class="dw dw-eye"></i> Görüntüle
                                                </a>
                                                <a class="dropdown-item" data-toggle="modal"
                                                    data-target="#editProductModal{{ $product->id }}" href="#"><i
                                                        class="dw dw-edit2"></i> Düzenle</a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('toggle-status-{{ $product->id }}').submit();">
                                                    <i class="dw {{ $product->status ? 'dw-ban' : 'dw-check' }}"></i>
                                                    {{ $product->status ? 'Deaktif Et' : 'Aktif Et' }}
                                                </a>
                                                <form id="toggle-status-{{ $product->id }}"
                                                    action="{{ route('seller.products.toggleStatus', $product->id) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
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
                                                <h4 class="modal-title" id="editProductModalLabel">Ürünü Düzenle</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-hidden="true">×</button>
                                            </div>
                                            <form action="{{ route('seller.products.update', $product->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="productName">Ürün Adı</label>
                                                        <input type="text" name="name" id="productName"
                                                            class="form-control" value="{{ $product->name }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="category{{ $product->id }}">Kategori</label>
                                                        <select name="category_id" id="category{{ $product->id }}"
                                                            class="form-control" required>
                                                            <option value="">Kategori Seçin</option>
                                                            @foreach ($categoryTree as $item)
                                                                @php
                                                                    $prefix = str_repeat('-- ', $item['level']);
                                                                @endphp
                                                                <option value="{{ $item['category']->id }}"
                                                                    {{ $product->category_id === $item['category']->id ? 'selected' : '' }}>
                                                                    {{ $prefix }}{{ $item['category']->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="productPrice">Fiyat</label>
                                                        <input type="number" name="price" id="productPrice"
                                                            class="form-control" value="{{ $product->price }}"
                                                            step="0.01" required>
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
                                                        <label for="editProductImages">Ekstra Görsel</label>
                                                        <input type="file" name="images[]" id="editProductImages"
                                                            class="form-control-file" multiple>
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
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Ürün bulunamadı.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addProductModalLabel">Yeni Ürün Ekle</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="productName">Ürün Adı</label>
                            <input type="text" name="name" id="productName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Kategori</label>
                            <select name="category_id" id="category" class="form-control" required>
                                <option value="">Kategori Seçin</option>
                                @foreach ($categoryTree as $item)
                                    @php
                                        $prefix = str_repeat('-- ', $item['level']);
                                    @endphp
                                    <option value="{{ $item['category']->id }}">{{ $prefix }}{{ $item['category']->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="productPrice">Fiyat</label>
                            <input type="number" name="price" id="productPrice" class="form-control" step="0.01"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="productStock">Stok</label>
                            <input type="number" name="stock" id="productStock" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="productDescription">Açıklama</label>
                            <textarea name="description" id="productDescription" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="productImages">Ürün Görsel</label>
                            <input type="file" name="images[]" id="productImages" class="form-control-file" multiple>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Değişiklikleri Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
