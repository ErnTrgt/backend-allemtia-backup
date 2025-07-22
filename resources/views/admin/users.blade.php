@extends('layouts.layout')

@section('title', 'Users Management')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>Users Management</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Users Management
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <div class="col-md-6 col-sm-12 text-right">
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                Filter By Role
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('admin.users') }}">All</a>
                                <a class="dropdown-item" href="{{ route('admin.users', ['role' => 'admin']) }}">Admins</a>
                                <a class="dropdown-item" href="{{ route('admin.users', ['role' => 'seller']) }}">Sellers</a>
                                <a class="dropdown-item" href="{{ route('admin.users', ['role' => 'buyer']) }}">Buyers</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Users Table</h4>
                    <p class="mb-0">
                        Manage users and their roles. For more options,
                        <a class="text-primary" href="https://datatables.net/" target="_blank">Click Here</a>.
                    <div class="col-md-12 col-sm-12 text-right">
                        <button class="btn btn-success" data-toggle="modal" data-target="#addUserModal">Add User</button>
                    </div>
                    </p>

                </div>

                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap">
                        <thead>
                            <tr>
                                <th class="table-plus datatable-nosort">Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Added At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="table-plus">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>

                                    <td>{{ ucfirst($user->role) }}</td>
                                    <td>
                                        <span
                                            class="badge
                                            {{ $user->status === 'approved' ? 'badge-success' : ($user->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle"
                                                href="#" role="button" data-toggle="dropdown">
                                                <i class="dw dw-more"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                                <!-- View -->
                                                {{-- <a class="dropdown-item" href="#"><i class="dw dw-eye"></i> View</a> --}}

                                                <!-- Edit -->
                                                <a class="dropdown-item" href="#" data-toggle="modal"
                                                    data-target="#editUserModal{{ $user->id }}">
                                                    <i class="dw dw-edit2"></i> Edit
                                                </a>


                                                <!-- Change Status -->
                                                <div class="dropdown-divider"></div>
                                                <h6 style="text-align: center ">Change Status</h6>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('status-pending-{{ $user->id }}').submit();">
                                                    <i class="icon-copy ion-clock"></i> Pending
                                                </a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('status-approved-{{ $user->id }}').submit();">
                                                    <i class="dw dw-check"></i> Approve
                                                </a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('status-rejected-{{ $user->id }}').submit();">
                                                    <i class="dw dw-ban"></i> Reject
                                                </a>

                                                <!-- Forms for Status Change -->
                                                <form id="status-pending-{{ $user->id }}"
                                                    action="{{ route('admin.users.changeStatus', ['id' => $user->id, 'status' => 'pending']) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                <form id="status-approved-{{ $user->id }}"
                                                    action="{{ route('admin.users.changeStatus', ['id' => $user->id, 'status' => 'approved']) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                                <form id="status-rejected-{{ $user->id }}"
                                                    action="{{ route('admin.users.changeStatus', ['id' => $user->id, 'status' => 'rejected']) }}"
                                                    method="POST" style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>

                                                <!-- Delete -->
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#"
                                                    onclick="event.preventDefault(); document.getElementById('delete-user-{{ $user->id }}').submit();">
                                                    <i class="dw dw-delete-3"></i> Delete
                                                </a>
                                                <form id="delete-user-{{ $user->id }}"
                                                    action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
            <!-- Users Table End -->

        </div>

    </div>


    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="userName">Name</label>
                            <input type="text" name="name" id="userName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="userEmail">Email</label>
                            <input type="email" name="email" id="userEmail" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="userPhone">Phone Number</label>
                            <input type="tel" name="phone" id="userPhone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="userPassword">Password</label>
                            <input type="password" name="password" id="userPassword" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="userRole">Role</label>
                            <select name="role" id="userRole" class="form-control" required>
                                <option value="admin">Admin</option>
                                <option value="seller">Seller</option>
                                <option value="buyer">Buyer</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Add User Modal -->

    <!-- Edit Modal -->
    @foreach ($users as $user)
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" role="dialog"
            aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">
                                Edit User
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="userName{{ $user->id }}">Name</label>
                                <input type="text" name="name" id="userName{{ $user->id }}"
                                    class="form-control" value="{{ $user->name }}" required>
                            </div>
                            <div class="form-group">
                                <label for="userEmail{{ $user->id }}">Email</label>
                                <input type="email" name="email" id="userEmail{{ $user->id }}"
                                    class="form-control" value="{{ $user->email }}" required>
                            </div>
                            <div class="form-group">
                                <label for="userPhone{{ $user->id }}">Phone</label>
                                <input type="text" name="phone" id="userPhone{{ $user->id }}"
                                    class="form-control" value="{{ $user->phone }}">
                            </div>
                            <div class="form-group">
                                <label for="userRole{{ $user->id }}">Role</label>
                                <select name="role" id="userRole{{ $user->id }}" class="form-control" required>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin
                                    </option>
                                    <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>Seller
                                    </option>
                                    <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="userStatus{{ $user->id }}">Status</label>
                                <select name="status" id="userStatus{{ $user->id }}" class="form-control" required>
                                    <option value="pending" {{ $user->status === 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="approved" {{ $user->status === 'approved' ? 'selected' : '' }}>
                                        Approved</option>
                                    <option value="rejected" {{ $user->status === 'rejected' ? 'selected' : '' }}>
                                        Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- End Edit Modal -->



@endsection
