@extends('seller.layout')
@section('title', 'Change Password')

@section('content')
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="title pb-20">
                <h2 class="h3 mb-0">Change Password</h2>
            </div>

            <!-- Şifre Değiştirme Formu -->
            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4">Update Your Password</h4>
                </div>
                <div class="pb-20">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('seller.password.update') }}">
                        @csrf
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" id="current_password"
                                required>
                            @error('current_password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" name="new_password"
                                class="form-control @error('new_password') is-invalid @enderror" id="new_password" required>
                            @error('new_password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="form-control"
                                id="new_password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
