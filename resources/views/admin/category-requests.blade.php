@extends('layouts.layout')

@section('title', 'Category Requests')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Category Requests</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">All Category Requests</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Seller</th>
                                <th>Status</th>
                                <th>Requested At</th>
                                <th>Actions</th>
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
                                                    {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved"
                                                    {{ $request->status === 'approved' ? 'selected' : '' }}>Approve</option>
                                                <option value="rejected"
                                                    {{ $request->status === 'rejected' ? 'selected' : '' }}>Reject</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary mt-2">Update</button>
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
