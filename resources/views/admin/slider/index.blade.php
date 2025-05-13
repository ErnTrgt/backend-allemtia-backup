@extends('layouts.layout')

@section('title', 'Slider Management')

@section('content')
<div class="pd-ltr-20 xs-pd-20-10">
    <div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Slider Management</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sliders</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#addSliderModal">
                        + Add New Slider
                    </button>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">Slider List</h4>
            </div>
            <div class="pb-20">
                <table class="data-table table stripe hover nowrap dt-responsive" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tag 1</th>
                            <th>Tag 2</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th class="datatable-nosort">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sliders as $slider)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $slider->tag_one }}</td>
                            <td>{{ $slider->tag_two }}</td>
                            <td>{{ Str::limit(strip_tags($slider->description), 50) }}</td>
                            <td>
                                @if($slider->image)
                                    <img src="{{ asset('storage/' . $slider->image) }}" alt="slider image" width="80">
                                @else
                                    <span>No Image</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $slider->status ? 'badge-success' : 'badge-danger' }}">
                                    {{ $slider->status ? 'Active' : 'Inactive' }}
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
                                            data-target="#editSliderModal{{ $slider->id }}" href="#">
                                            <i class="dw dw-edit2"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); document.getElementById('toggle-slider-{{ $slider->id }}').submit();">
                                            <i class="dw {{ $slider->status ? 'dw-ban' : 'dw-check' }}"></i>
                                            {{ $slider->status ? 'Deactivate' : 'Activate' }}
                                        </a>
                                        <form id="toggle-slider-{{ $slider->id }}"
                                            action="{{ route('admin.slider.toggle', $slider->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('PUT')
                                        </form>
                                        <a class="dropdown-item text-danger" href="#"
                                            onclick="event.preventDefault(); document.getElementById('delete-slider-{{ $slider->id }}').submit();">
                                            <i class="dw dw-delete-3"></i> Delete
                                        </a>
                                        <form id="delete-slider-{{ $slider->id }}"
                                            action="{{ route('admin.slider.delete', $slider->id) }}"
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editSliderModal{{ $slider->id }}" tabindex="-1"
                            role="dialog" aria-labelledby="editSliderModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Slider</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <form action="{{ route('admin.slider.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Tag One</label>
                                                <input type="text" name="tag_one" class="form-control" value="{{ $slider->tag_one }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Tag Two</label>
                                                <input type="text" name="tag_two" class="form-control" value="{{ $slider->tag_two }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="description" rows="4" class="form-control">{{ $slider->description }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>Image</label>
                                                <input type="file" name="image" class="form-control-file">
                                                @if($slider->image)
                                                    <img src="{{ asset('storage/' . $slider->image) }}" alt="slider" width="60" class="mt-2">
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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

<!-- Add Modal -->
<div class="modal fade" id="addSliderModal" tabindex="-1" role="dialog" aria-labelledby="addSliderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Slider</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{ route('admin.slider.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tag One</label>
                        <input type="text" name="tag_one" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Tag Two</label>
                        <input type="text" name="tag_two" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control-file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
