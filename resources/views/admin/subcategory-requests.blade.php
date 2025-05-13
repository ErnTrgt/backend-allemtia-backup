@extends('layouts.layout')

@section('title', 'Subcategory Requests')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Subcategory Requests</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>Seller</th>
                                <th>Category</th>
                                <th>Subcategory Name</th>
                                <th>Status</th>
                                <th>Requested At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subcategoryRequests as $request)
                                <tr>
                                    <td>{{ $request->seller->name }}</td>
                                    <td>{{ $request->category->name }}</td>
                                    <td>{{ $request->subcategory_name }}</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <form action="{{ route('admin.updateSubcategoryRequestStatus', $request->id) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-control" onchange="this.form.submit()">
                                                <option value="pending"
                                                    {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved"
                                                    {{ $request->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected"
                                                    {{ $request->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
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
