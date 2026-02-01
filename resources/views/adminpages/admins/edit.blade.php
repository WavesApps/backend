@extends('adminpages.layouts.app')

@section('title', 'Edit Admin User')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="card-title mb-2">
                                <i class="fas fa-user-edit me-2"></i>
                                Edit Admin User
                            </h1>
                            <p class="card-text mb-0">Update information for {{ $admin->name }}</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('admin.admins.show', $admin) }}" class="btn btn-light me-2">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a>
                            <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i>Back to Admins
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user-cog me-2"></i>Update Admin Information
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.admins.update', $admin) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name Field -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $admin->name) }}" 
                                       placeholder="Enter admin's full name"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $admin->email) }}" 
                                       placeholder="admin@example.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>New Password
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Leave blank to keep current password">
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                                <small class="form-text text-muted">Leave empty to keep the current password. Enter new password to change it.</small>
                            </div>
                        </div>

                        <!-- Password Confirmation Field -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Confirm New Password
                                </label>
                                <input type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Confirm new password">
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.admins.show', $admin) }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Admin User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Information Panel -->
        <div class="col-md-4">
            <!-- Current Admin Info -->
            <div class="card shadow mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Current Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        <h6 class="mb-1">{{ $admin->name }}</h6>
                        <small class="text-muted">{{ $admin->email }}</small>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">User ID</small>
                            <p class="mb-0"><strong>#{{ $admin->id }}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Role</small>
                            <p class="mb-0"><span class="badge bg-info">{{ ucfirst($admin->role) }}</span></p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <small class="text-muted">Created</small>
                            <p class="mb-0"><strong>{{ $admin->created_at->format('M d, Y') }}</strong></p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Last Login</small>
                            <p class="mb-0">
                                @if($admin->last_login_at)
                                    <strong>{{ $admin->last_login_at->format('M d, Y') }}</strong>
                                @else
                                    <strong>Never</strong>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Information -->
            <div class="card shadow">
                <div class="card-header bg-gradient-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-shield-alt me-2"></i>Security Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Email Verification</small>
                        <p class="mb-0">
                            @if($admin->email_verified_at)
                                <span class="badge bg-success">Verified</span>
                                <small class="text-muted d-block">{{ $admin->email_verified_at->format('M d, Y H:i') }}</small>
                            @else
                                <span class="badge bg-warning">Not Verified</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">Account Status</small>
                        <p class="mb-0">
                            <span class="badge bg-success">Active</span>
                        </p>
                    </div>

                    @if($admin->id === auth()->id())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> You are editing your own account. Be careful when changing your email or password.
                    </div>
                    @endif

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Security Tip:</strong> Regularly update passwords and use strong, unique passwords for admin accounts.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-label {
        font-weight: 600;
        color: var(--text-primary);
    }
    .form-control {
        background-color: var(--card-bg);
        border-color: var(--border-color);
        color: var(--text-primary);
    }
    .form-control:focus {
        background-color: var(--card-bg);
        border-color: var(--primary-color);
        color: var(--text-primary);
    }
    .invalid-feedback {
        color: #dc3545;
    }
    .text-danger {
        color: #dc3545;
    }
</style>
@endpush
