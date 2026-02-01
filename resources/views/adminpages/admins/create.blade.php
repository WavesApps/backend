@extends('adminpages.layouts.app')

@section('title', 'Create Admin User')

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
                                <i class="fas fa-user-plus me-2"></i>
                                Create New Admin User
                            </h1>
                            <p class="card-text mb-0">Add a new administrator to manage your streaming platform</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('admin.admins.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i>Back to Admins
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-user-cog me-2"></i>Admin User Information
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.admins.store') }}">
                        @csrf
                        
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
                                       value="{{ old('name') }}" 
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
                                       value="{{ old('email') }}" 
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
                                    <i class="fas fa-lock me-1"></i>Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Enter password (minimum 8 characters)"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                                <small class="form-text text-muted">Password must be at least 8 characters long.</small>
                            </div>
                        </div>

                        <!-- Password Confirmation Field -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Confirm Password <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Confirm password"
                                       required>
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
                                    <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Create Admin User
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
            <div class="card shadow">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Admin Permissions
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-primary">What can admins do?</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Manage all platform users
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            View and manage superstars
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Access financial data
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Create and manage other admins
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            View platform analytics
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Manage platform settings
                        </li>
                    </ul>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Security Note:</strong> Only grant admin access to trusted personnel who need platform management capabilities.
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header bg-gradient-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-shield-alt me-2"></i>Security Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-lock text-primary me-2"></i>
                            Use strong, unique passwords
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-user-shield text-primary me-2"></i>
                            Only create admin accounts when necessary
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-history text-primary me-2"></i>
                            Regularly review admin access
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-key text-primary me-2"></i>
                            Change passwords periodically
                        </li>
                    </ul>
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
