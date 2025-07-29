@extends('layouts.layout')

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
                </div>
            </div>
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Tüm Kategori İstekleri</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>Kategori Adı</th>
                                <th>Satıcı</th>
                                <th>Durum</th>
                                <th>İstek Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $request)
                                <tr>
                                    <td>{{ $request->name }}</td>
                                    <td>{{ $request->seller->name }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $request->status === 'approved' ? 'badge-success' : ($request->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <form action="{{ route('admin.category.requests.update', $request->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-control">
                                                <option value="pending"
                                                    {{ $request->status === 'pending' ? 'selected' : '' }}>Beklemede</option>
                                                <option value="approved"
                                                    {{ $request->status === 'approved' ? 'selected' : '' }}>Onaylandı</option>
                                                <option value="rejected"
                                                    {{ $request->status === 'rejected' ? 'selected' : '' }}>Reddedildi</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary mt-2">Güncelle</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
