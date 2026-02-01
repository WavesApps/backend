@extends('adminpages.layouts.app')

@section('title', 'Admin Users')

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
                                <i class="fas fa-users-cog me-2"></i>
                                Admin Users Management
                            </h1>
                            <p class="card-text mb-0">Manage platform administrators and their permissions</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <a href="{{ route('admin.admins.create') }}" class="btn btn-light">
                                <i class="fas fa-plus me-2"></i>Create Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-list me-2"></i>All Admin Users
                    </h6>
                </div>
                <div class="card-body">
                    @if($admins->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-user me-1"></i> Name</th>
                                        <th><i class="fas fa-envelope me-1"></i> Email</th>
                                        <th><i class="fas fa-calendar me-1"></i> Created</th>
                                        <th><i class="fas fa-clock me-1"></i> Last Login</th>
                                        <th><i class="fas fa-cog me-1"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($admins as $admin)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $admin->name }}</h6>
                                                        <small class="text-muted">
                                                            @if($admin->id === auth()->id())
                                                                <span class="badge bg-success">You</span>
                                                            @else
                                                                <span class="badge bg-info">Admin</span>
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $admin->email }}</div>
                                                    <small class="text-muted">{{ $admin->email_verified_at ? 'Verified' : 'Not Verified' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div>{{ $admin->created_at->format('M d, Y') }}</div>
                                                    <small class="text-muted">{{ $admin->created_at->format('H:i A') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    @if($admin->last_login_at)
                                                        <div>{{ $admin->last_login_at->format('M d, Y') }}</div>
                                                        <small class="text-muted">{{ $admin->last_login_at->format('H:i A') }}</small>
                                                    @else
                                                        <div class="text-muted">Never</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.admins.show', $admin) }}" class="btn btn-sm btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($admin->id !== auth()->id())
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="confirmDelete('{{ route('admin.admins.destroy', $admin) }}', '{{ $admin->name }}')" 
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $admins->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Admin Users Found</h5>
                            <p class="text-muted mb-4">Create your first admin user to get started.</p>
                            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create Admin User
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-sm {
        font-weight: 600;
        font-size: 0.875rem;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }
    .btn-group .btn {
        margin: 0 2px;
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
