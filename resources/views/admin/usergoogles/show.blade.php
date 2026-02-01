@extends('adminpages.layouts.app')

@section('title', 'User Subscriptions')

@section('content')
<div class="main-content">
    <div class="page-header">
        <div class="header-top">
            <h1>User Subscriptions</h1>
            <a href="{{ route('admin.usergoogles.index') }}" class="btn btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Users
            </a>
        </div>
    </div>

    <div class="user-info-card">
        <div class="user-avatar-section">
            @if($user->image)
                <img src="{{ $user->image }}" alt="User Image" class="user-avatar-large">
            @else
                <div class="user-avatar-placeholder-large">
                    {{ strtoupper(substr($user->username, 0, 1)) }}
                </div>
            @endif
        </div>
        <div class="user-details">
            <h2>{{ $user->username }}</h2>
            <p class="user-email">{{ $user->email }}</p>
            <p class="user-meta">Joined: {{ $user->created_at->format('M d, Y') }}</p>
            <p class="user-meta">Total Subscriptions: <strong>{{ $user->superstars->count() }}</strong></p>
        </div>
    </div>

    <div class="data-table-container">
        <h3 class="section-title">Subscribed Superstars</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Display Name</th>
                    <th>Bio</th>
                    <th>Price/Hour</th>
                    <th>Rating</th>
                    <th>Followers</th>
                    <th>Available</th>
                    <th>Status</th>
                    <th>Subscribed Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($user->superstars as $superstar)
                    <tr>
                        <td>{{ $superstar->id }}</td>
                        <td><strong>{{ $superstar->display_name }}</strong></td>
                        <td>
                            <div class="bio-text">
                                {{ Str::limit($superstar->bio, 50) }}
                            </div>
                        </td>
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
                        <td>{{ $superstar->pivot->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">This user has no subscriptions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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

    .btn-secondary {
        background-color: #718096;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #4a5568;
    }

    .user-info-card {
        background: white;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .user-avatar-section {
        flex-shrink: 0;
    }

    .user-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #667eea;
    }

    .user-avatar-placeholder-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 32px;
        border: 3px solid #667eea;
    }

    .user-details h2 {
        font-size: 24px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 8px;
    }

    .user-email {
        color: #718096;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .user-meta {
        color: #4a5568;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 20px;
    }

    .data-table-container {
        background: white;
        border-radius: 8px;
        padding: 30px;
        overflow-x: auto;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

    .bio-text {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #4a5568;
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

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: #718096;
    }
</style>
@endsection

