@extends('seller.layout')

@section('title', 'Category Requests')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Kategori İstekleri</h4>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addCategoryRequestModal">Yeni
                            Kategori İstek</button>
                    </div>
                </div>
            </div>
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Kategori İsteklerim</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>Kategori Adı</th>
                                <th>Durum</th>
                                <th>İstek Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $request)
                                <tr>
                                    <td>{{ $request->name }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $request->status === 'approved' ? 'badge-success' : ($request->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Request Modal -->
    <div class="modal fade" id="addCategoryRequestModal" tabindex="-1" role="dialog"
        aria-labelledby="addCategoryRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addCategoryRequestModalLabel">Yeni Kategori İstek</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form action="{{ route('seller.category.requests.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="categoryName">Kategori Adı</label>
                            <input type="text" name="name" id="categoryName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">İstek Gönder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
