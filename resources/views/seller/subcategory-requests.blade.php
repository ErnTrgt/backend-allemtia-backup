@extends('seller.layout')

@section('title', 'Subcategory Requests')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Subcategory Requests</h4>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addRequestModal">Request New
                        Subcategory</button>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Subcategory Name</th>
                                <th>Status</th>
                                <th>Requested At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subcategoryRequests as $request)
                                <tr>
                                    <td>{{ $request->category->name }}</td>
                                    <td>{{ $request->subcategory_name }}</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No subcategory requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Subcategory Request Modal -->
    <div class="modal fade" id="addRequestModal" tabindex="-1" role="dialog" aria-labelledby="addRequestModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addRequestModalLabel">Request New Subcategory</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form action="{{ route('seller.storeSubcategoryRequest') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select name="category_id" id="category" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach ($categoryTree as $item)
                                    @php
                                        $prefix = str_repeat('-- ', $item['level']);
                                    @endphp
                                    <option value="{{ $item['category']->id }}">{{ $prefix }}{{ $item['category']->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="subcategory_name">Subcategory Name</label>
                            <input type="text" name="subcategory_name" id="subcategory_name" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
