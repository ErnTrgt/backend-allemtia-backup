@extends('layouts.layout')

@section('title', 'Admin Profile')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Admin Profile</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Profile</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="row">
                <!-- Profile Info Section -->
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                        <div class="profile-photo">
                            <img src="/admin/vendors/images/photo1.jpg" alt="" class="avatar-photo" />
                        </div>
                        <h5 class="text-center h5 mb-0">{{ $admin->name }}</h5>
                        <p class="text-center text-muted font-14">Administrator</p>
                        <div class="profile-info">
                            <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                            <ul>
                                <li><span>Email Address:</span> {{ $admin->email }}</li>
                                <li><span>Phone Number:</span> {{ $admin->phone }}</li>
                                <li><span>Country:</span> {{ $admin->country }}</li>
                                <li><span>State:</span> {{ $admin->state }}</li>
                                <li><span>Address:</span> {{ $admin->address }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Profile Update Section -->
                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
                    <div class="card-box height-100-p overflow-hidden">
                        <div class="pd-20">
                            <h5 class="text-blue h5 mb-20">Edit Your Personal Information</h5>

                            <form action="{{ route('admin.profile.update') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ $admin->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ $admin->email }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $admin->phone }}">
                                </div>
                                <div class="form-group">
                                    <label>Country</label>
                                    <input type="text" class="form-control" name="country" value="{{ $admin->country }}">
                                </div>
                                <div class="form-group">
                                    <label>State/Province/Region</label>
                                    <input type="text" class="form-control" name="state" value="{{ $admin->state }}">
                                </div>
                                <div class="form-group">
                                    <label>Postal Code</label>
                                    <input type="text" class="form-control" name="postal_code" value="{{ $admin->postal_code }}">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control" name="address">{{ $admin->address }}</textarea>
                                </div>
                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
