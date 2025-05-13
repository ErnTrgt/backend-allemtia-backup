@extends('layouts.layout')

@section('title', 'About Page Content')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>About Page Content Management</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">About Page</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addAboutModal">
                        + Add New Section
                    </button>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">About Page Sections</h4>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap dt-responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Section Key</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Status</th>
                            <th>Image</th>
                            <th class="datatable-nosort">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sections as $section)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $section->section_key }}</td>
                            <td>{{ Str::limit($section->title, 40) }}</td>
                            <td>{{ Str::limit(strip_tags($section->content), 50) }}</td>
                            <td>
                                <span class="badge {{ $section->status ? 'badge-success' : 'badge-danger' }}">
                                    {{ $section->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                @if($section->image)
                                    <img src="{{ asset('storage/' . $section->image) }}" alt="Section Image" width="80">
                                @else
                                    <span>No Image</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item" href="{{ route('admin.about.edit', $section->id) }}">
                                            <i class="dw dw-edit2"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('toggle-status-{{ $section->id }}').submit();">
                                            <i class="dw {{ $section->status ? 'dw-ban' : 'dw-check' }}"></i>
                                            {{ $section->status ? 'Deactivate' : 'Activate' }}
                                        </a>
                                        <form id="toggle-status-{{ $section->id }}" action="{{ route('admin.about.toggleStatus', $section->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('delete-about-{{ $section->id }}').submit();">
                                            <i class="dw dw-delete-3"></i> Delete
                                        </a>
                                        <form id="delete-about-{{ $section->id }}" action="{{ route('admin.about.delete', $section->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
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

<!-- Add About Section Modal -->
<div class="modal fade" id="addAboutModal" tabindex="-1" role="dialog" aria-labelledby="addAboutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New About Section</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{ route('admin.about.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Section Key <small>(e.g. heading, area, features)</small></label>
                        <input type="text" name="section_key" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="content" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control-file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Section</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
