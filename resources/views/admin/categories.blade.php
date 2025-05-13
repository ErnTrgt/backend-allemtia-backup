@extends('layouts.layout')

@section('title', 'Manage Categories and Subcategories')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <!-- Add Category -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Add Category</h4>
                </div>
                <form action="{{ route('admin.storeCategory') }}" method="POST">
                    @csrf
                    <div class="pd-20">
                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" id="category_name" name="name" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>

            <!-- Add Subcategory -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Add Subcategory</h4>
                </div>
                <form action="{{ route('admin.storeSubcategory') }}" method="POST">
                    @csrf
                    <div class="pd-20">
                        <div class="form-group">
                            <label for="category_id">Parent Category</label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="subcategory_name">Subcategory Name</label>
                            <input type="text" id="subcategory_name" name="name" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Subcategory</button>
                    </div>
                </form>
            </div>

            <!-- Categories and Subcategories Table -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Categories and Subcategories</h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Subcategories</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>
                                        @if ($category->subcategories->isEmpty())
                                            <span class="text-muted">No Subcategories</span>
                                        @else
                                            <ul>
                                                @foreach ($category->subcategories as $subcategory)
                                                    <li>{{ $subcategory->name }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#editModal{{ $category->id }}">Edit</button>
                                        <form action="{{ route('admin.deleteCategory', $category->id) }}" method="POST"
                                            style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $category->id }}" tabindex="-1"
                                    aria-labelledby="editModalLabel{{ $category->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel{{ $category->id }}">
                                                    Edit Category and Subcategories
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('admin.updateCategory', $category->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="category_name_{{ $category->id }}">Category
                                                            Name</label>
                                                        <input type="text" id="category_name_{{ $category->id }}"
                                                            name="name" class="form-control"
                                                            value="{{ $category->name }}" required>
                                                    </div>
                                                    <h5>Subcategories</h5>
                                                    @if ($category->subcategories->isEmpty())
                                                        <p class="text-muted">No Subcategories</p>
                                                    @else
                                                        @foreach ($category->subcategories as $subcategory)
                                                            <div class="form-group">
                                                                <label
                                                                    for="subcategory_name_{{ $subcategory->id }}">Subcategory
                                                                    Name</label>
                                                                <input type="text"
                                                                    id="subcategory_name_{{ $subcategory->id }}"
                                                                    name="subcategories[{{ $subcategory->id }}]"
                                                                    class="form-control" value="{{ $subcategory->name }}"
                                                                    required>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Edit Modal -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
