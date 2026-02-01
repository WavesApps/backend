@extends('adminpages.layouts.app')

@section('title', 'Superstars Management')

@section('content')
<div class="main-content">
    <div class="page-header">
        <div class="header-top">
            <h1>Superstars Management</h1>
            <a href="{{ route('admin.superstars.create') }}" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Superstar
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                    <th>User</th>
                    <th>Display Name</th>
                    <th>Price/Hour</th>
                    <th>Rating</th>
                    <th>Followers</th>
                    <th>Available</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($superstars as $superstar)
                    <tr>
                        <td>{{ $superstar->id }}</td>
                        <td>
                            <div class="user-info">
                                <strong>{{ $superstar->user->name }}</strong>
                                <small>{{ $superstar->user->email }}</small>
                            </div>
                        </td>
                        <td>{{ $superstar->display_name }}</td>
                        <td>${{ number_format($superstar->price_per_hour, 2) }}</td>
                        <td>
                            <span class="badge badge-info">{{ $superstar->rating }}/5.00</span>
                        </td>
                        <td>{{ number_format($superstar->total_followers) }}</td>
                        <td>
                            @if ($superstar->is_available)
                                <span class="badge badge-success">Available</span>
                            @else
                                <span class="badge badge-danger">Unavailable</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $superstar->status === 'active' ? 'success' : ($superstar->status === 'inactive' ? 'warning' : 'danger') }}">
                                {{ ucfirst($superstar->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.superstars.show', $superstar) }}" class="btn btn-sm btn-info" title="View Details">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span>View</span>
                                </a>
                                <a href="{{ route('admin.superstars.edit', $superstar) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </a>
                                <form action="{{ route('admin.superstars.destroy', $superstar) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No superstars found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $superstars->links() }}
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

    .btn-primary {
        background-color: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background-color: #5a67d8;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .btn-info {
        color: #667eea;
        background-color: transparent;
        border: 1px solid #667eea;
    }

    .btn-warning {
        color: #f6ad55;
        background-color: transparent;
        border: 1px solid #f6ad55;
    }

    .btn-danger {
        color: #f56565;
        background-color: transparent;
        border: 1px solid #f56565;
    }

    .btn-info:hover,
    .btn-warning:hover,
    .btn-danger:hover {
        background-color: rgba(0, 0, 0, 0.05);
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

    .alert-danger {
        background-color: #fed7d7;
        color: #742a2a;
        border: 1px solid #fc8181;
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
        min-width: 1200px;
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

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-info small {
        color: #718096;
        font-size: 12px;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-success {
        background-color: #c6f6d5;
        color: #22543d;
    }

    .badge-danger {
        background-color: #fed7d7;
        color: #742a2a;
    }

    .badge-warning {
        background-color: #feebc8;
        color: #7c2d12;
    }

    .badge-info {
        background-color: #bee3f8;
        color: #2c5282;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .btn-sm span {
        display: inline;
    }

    .btn-info {
        color: #667eea;
        background-color: transparent;
        border: 1px solid #667eea;
    }

    .btn-info:hover {
        background-color: #f0f4ff;
    }

    .btn-warning {
        color: #f6ad55;
        background-color: transparent;
        border: 1px solid #f6ad55;
    }

    .btn-warning:hover {
        background-color: #fffaf0;
    }

    .btn-danger {
        color: #f56565;
        background-color: transparent;
        border: 1px solid #f56565;
        padding: 0;
    }

    .btn-danger:hover {
        background-color: #fff5f5;
    }

    .btn-danger svg,
    .btn-danger span {
        display: inline;
    }

    .inline-form {
        display: inline;
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
