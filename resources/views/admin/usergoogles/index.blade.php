@extends('adminpages.layouts.app')

@section('title', 'App Users Management')

@section('content')
<div class="main-content">
    <div class="page-header">
        <div class="header-top">
            <h1>App Users Management</h1>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="data-table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Image</th>
                    <th>Subscriptions</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->username }}</td>
                        <td>
                            @if($user->image)
                                <img src="{{ $user->image }}" alt="User Image" class="user-avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div class="user-avatar-placeholder" style="width: 40px; height: 40px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $user->superstars_count }} Subscriptions</span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.usergoogles.show', $user) }}" class="btn btn-sm btn-info" title="View Subscriptions">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span>View Subscriptions</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $users->links() }}
    </div>
</div>

<style>
    .main-content {
        padding: 30px;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header-top h1 {
        font-size: 28px;
        font-weight: 600;
        color: #1a202c;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #c6f6d5;
        color: #22543d;
        border: 1px solid #9ae6b4;
    }

    .data-table-container {
        background: white;
        border-radius: 8px;
        overflow-x: auto;
        overflow-y: visible;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
    }

    .data-table thead {
        background-color: #f7fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .data-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #4a5568;
        font-size: 13px;
    }

    .data-table td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
    }

    .data-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-info {
        background-color: #bee3f8;
        color: #2c5282;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn {
        padding: 10px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .btn-info {
        color: #667eea;
        background-color: transparent;
        border: 1px solid #667eea;
    }

    .btn-info:hover {
        background-color: #f0f4ff;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: #718096;
    }

    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
</style>
@endsection

