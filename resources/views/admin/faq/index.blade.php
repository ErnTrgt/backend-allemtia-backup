@extends('layouts.layout')

@section('title', 'FAQ Management')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>FAQ Management</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">FAQs</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addFaqModal">
                        + Add New FAQ
                    </button>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">FAQ List</h4>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap dt-responsive" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Status</th>
                            <th class="datatable-nosort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faqs as $faq)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ Str::limit($faq->title, 60) }}</td>
                            <td>{{ Str::limit(strip_tags($faq->content), 100) }}</td>
                            <td>
                                <span class="badge {{ $faq->status ? 'badge-success' : 'badge-danger' }}">
                                    {{ $faq->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                        href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item" data-toggle="modal"
                                            data-target="#editFaqModal{{ $faq->id }}" href="#">
                                            <i class="dw dw-edit2"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); document.getElementById('toggle-faq-{{ $faq->id }}').submit();">
                                            <i class="dw {{ $faq->status ? 'dw-ban' : 'dw-check' }}"></i>
                                            {{ $faq->status ? 'Deactivate' : 'Activate' }}
                                        </a>
                                        <form id="toggle-faq-{{ $faq->id }}"
                                            action="{{ route('admin.faq.toggle', $faq->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="event.preventDefault(); document.getElementById('delete-faq-{{ $faq->id }}').submit();">
                                            <i class="dw dw-delete-3"></i> Delete
                                        </a>
                                        <form id="delete-faq-{{ $faq->id }}"
                                            action="{{ route('admin.faq.delete', $faq->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit FAQ Modal -->
                        <div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1"
                            role="dialog" aria-labelledby="editFaqModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit FAQ</h4>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-hidden="true">×</button>
                                    </div>
                                    <form action="{{ route('admin.faq.update', $faq->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Question</label>
                                                <input type="text" name="title" class="form-control"
                                                    value="{{ $faq->title }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Answer</label>
                                                <textarea name="content" rows="5"
                                                    class="form-control">{{ $faq->content }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Edit Modal End -->
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add FAQ Modal -->
<div class="modal fade" id="addFaqModal" tabindex="-1" role="dialog" aria-labelledby="addFaqModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add FAQ</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{ route('admin.faq.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Question</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Answer</label>
                        <textarea name="content" rows="5" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add FAQ</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
