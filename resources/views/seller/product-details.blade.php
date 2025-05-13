@extends('seller.layout')

@section('title', 'Product Details')

@section('content')

    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Product Detail</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('seller.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('seller.products') }}">Products</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Product Detail
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="product-wrap">
                <div class="product-detail-wrap mb-30">
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="product-slider slider-arrow">
                                @foreach ($product->images as $image)
                                    <div class="product-slide">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product Image">
                                    </div>
                                @endforeach
                            </div>
                            <div class="product-slider-nav">
                                @foreach ($product->images as $image)
                                    <div class="product-slide-nav">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Thumbnail">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="product-detail-desc pd-20 card-box height-100-p">
                                <h4 class="mb-20 pt-20">{{ $product->name }}</h4>
                                <p>{{ $product->description }}</p>
                                <div class="price">
                                    <ins>${{ $product->price }}</ins>
                                </div>
                                <div class="stock">
                                    <p>Stock: {{ $product->stock }}</p>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <!-- Düzenle Butonu -->
                                        <a href="#" class="btn btn-primary btn-block" data-toggle="modal"
                                            data-target="#editProductModal">
                                            Edit
                                        </a>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <!-- Aktif/Deaktif Butonu -->
                                        <a href="#" class="btn btn-outline-primary btn-block"
                                            onclick="event.preventDefault(); document.getElementById('toggle-status-form').submit();">
                                            {{ $product->status ? 'Deactivate' : 'Activate' }}
                                        </a>
                                        <form id="toggle-status-form"
                                            action="{{ route('seller.products.toggleStatus', $product->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-20">Recent Products</h4>
                <div class="product-list">
                    <ul class="row">
                        @foreach ($recentProducts as $recentProduct)
                            <li class="col-lg-4 col-md-6 col-sm-12">
                                <div class="product-box">
                                    <div class="producct-img">
                                        @if ($recentProduct->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $recentProduct->images->first()->image_path) }}"
                                                alt="{{ $recentProduct->name }}" />
                                        @else
                                            <img src="{{ asset('default-image-path.jpg') }}" alt="Default Image" />
                                        @endif
                                    </div>
                                    <div class="product-caption">
                                        <h4><a
                                                href="{{ route('seller.products.details', $recentProduct->id) }}">{{ $recentProduct->name }}</a>
                                        </h4>
                                        <div class="price">
                                            <ins>${{ $recentProduct->price }}</ins>
                                        </div>
                                        <a href="{{ route('seller.products.details', $recentProduct->id) }}"
                                            class="btn btn-outline-primary">Read More</a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editProductModalLabel">Edit Product</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form action="{{ route('seller.products.update', $product->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="productName">Product Name</label>
                            <input type="text" name="name" id="productName" class="form-control"
                                value="{{ $product->name }}" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select name="category_id" id="category" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @if ($category->children->count())
                                        @foreach ($category->children as $child)
                                            <option value="{{ $child->id }}"
                                                {{ $product->category_id == $child->id ? 'selected' : '' }}>
                                                -- {{ $child->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="productPrice">Price</label>
                            <input type="number" name="price" id="productPrice" class="form-control"
                                value="{{ $product->price }}" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="productStock">Stock</label>
                            <input type="number" name="stock" id="productStock" class="form-control"
                                value="{{ $product->stock }}" required>
                        </div>
                        <div class="form-group">
                            <label for="productDescription">Description</label>
                            <textarea name="description" id="productDescription" class="form-control" rows="4">{{ $product->description }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="editProductImages">Additional Images</label>
                            <input type="file" name="images[]" id="editProductImages" class="form-control-file"
                                multiple>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
