@extends('seller.layout')
@section('title', 'Edit Product')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Edit Product</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box mb-30">
                <form action="{{ route('seller.products.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="productName">Product Name</label>
                        <input type="text" name="name" id="productName" class="form-control"
                            value="{{ $product->name }}" required>
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
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </form>
            </div>
        </div>
    </div>
@endsection
