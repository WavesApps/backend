@extends('adminpages.layouts.app')

@section('title', 'Admin User Details')

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
                                <i class="fas fa-user-cog me-2"></i>
                                Admin User Details
                            </h1>
                            <p class="card-text mb-0">View detailed information about this administrator</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('admin.admins.index') }}" class="btn btn-light me-2">
                                <i class="fas fa-arrow-left me-2"></i>Back to Admins
                            </a>
                            <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Edit Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Details -->
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px; font-size: 3rem;">
                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                    </div>
                    <h4 class="card-title">{{ $admin->name }}</h4>
                    <p class="card-text text-muted">{{ $admin->email }}</p>
                    <div class="mt-3">
                        @if($admin->id === auth()->id())
                            <span class="badge bg-success fs-6">Current User</span>
                        @else
                            <span class="badge bg-info fs-6">Admin</span>
                        @endif
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-{{ $admin->email_verified_at ? 'success' : 'warning' }}">
                            {{ $admin->email_verified_at ? 'Email Verified' : 'Email Not Verified' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-tools me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                        @if($admin->id !== auth()->id())
                            <button type="button" class="btn btn-outline-danger" 
                                    onclick="confirmDelete('{{ route('admin.admins.destroy', $admin) }}', '{{ $admin->name }}')">
                                <i class="fas fa-trash me-2"></i>Delete Admin
                            </button>
                        @endif
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>All Admins
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Account Information -->
            <div class="card shadow mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Full Name</label>
                            <p class="mb-0"><strong>{{ $admin->name }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Email Address</label>
                            <p class="mb-0"><strong>{{ $admin->email }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">User Role</label>
                            <p class="mb-0"><span class="badge bg-info">{{ ucfirst($admin->role) }}</span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">User ID</label>
                            <p class="mb-0"><strong>#{{ $admin->id }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Email Verification</label>
                            <p class="mb-0">
                                @if($admin->email_verified_at)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning">Not Verified</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Account Status</label>
                            <p class="mb-0"><span class="badge bg-success">Active</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card shadow">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-history me-2"></i>Activity Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Account Created -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Account Created</h6>
                                <p class="text-muted mb-0">
                                    {{ $admin->created_at->format('F d, Y \a\t H:i A') }}
                                </p>
                            </div>
                        </div>

                        <!-- Email Verification -->
                        @if($admin->email_verified_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success">
                                <i class="fas fa-envelope-check"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Email Verified</h6>
                                <p class="text-muted mb-0">
                                    {{ $admin->email_verified_at->format('F d, Y \a\t H:i A') }}
                                </p>
                            </div>
                        </div>
                        @endif

                        <!-- Last Login -->
                        @if($admin->last_login_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Last Login</h6>
                                <p class="text-muted mb-0">
                                    {{ $admin->last_login_at->format('F d, Y \a\t H:i A') }}
                                </p>
                            </div>
                        </div>
                        @else
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning">
                                <i class="fas fa-minus"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">No Login Activity</h6>
                                <p class="text-muted mb-0">
                                    This admin has never logged in
                                </p>
                            </div>
                        </div>
                        @endif

                        <!-- Last Updated -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Last Updated</h6>
                                <p class="text-muted mb-0">
                                    {{ $admin->updated_at->format('F d, Y \a\t H:i A') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-lg {
        font-weight: 600;
    }
    .timeline {
        position: relative;
        padding-left: 40px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: var(--border-color);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    .timeline-marker {
        position: absolute;
        left: -25px;
        top: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
    }
    .timeline-content {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 15px;
    }
</style>
@endpush

@push('scripts')
<script>
function confirmDelete(url, name) {
    if (confirm('Are you sure you want to delete the admin user "' + name + '"? This action cannot be undone.')) {
        window.location.href = url;
    }
}
</script>
@endpush
