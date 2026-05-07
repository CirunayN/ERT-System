@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Profile Info Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-circle me-2 text-sg-primary"></i>Profile Information</h6>
            </div>
            <div class="card-body p-4">
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')
                    @if (session('status') === 'profile-updated')
                        <div class="alert alert-success py-2 small">Profile information updated successfully.</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" placeholder="e.g. 09123456789">
                        <small class="text-muted">Will be used to auto-fill incident reports.</small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-sg">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Update Form -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2 text-sg-primary"></i>Update Password</h6>
            </div>
            <div class="card-body p-4">
                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')
                    @if (session('status') === 'password-updated')
                        <div class="alert alert-success py-2 small">Password updated successfully.</div>
                    @endif
                    @if ($errors->updatePassword->any())
                        <div class="alert alert-danger py-2 small">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->updatePassword->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button class="btn btn-sg">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
