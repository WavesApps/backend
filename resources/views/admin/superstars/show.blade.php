@extends('adminpages.layouts.app')

@section('title', $superstar->display_name)

@section('content')
<div class="main-content">
    <div class="page-header">
        <div class="header-top">
            <div>
                <h1>{{ $superstar->display_name }}</h1>
                <p class="subtitle">Superstar Profile</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.superstars.edit', $superstar) }}" class="btn btn-warning">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Edit
                </a>
                <form action="{{ route('admin.superstars.destroy', $superstar) }}" method="POST" class="inline-form" onsubmit="return confirm('Are you sure?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Main Info Card -->
        <div class="card">
            <div class="card-header">
                <h2>Profile Information</h2>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="label">Display Name</span>
                    <span class="value">{{ $superstar->display_name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Bio</span>
                    <span class="value">{{ $superstar->bio ?? 'No bio provided' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Price Per Hour</span>
                    <span class="value"><strong>${{ number_format($superstar->price_per_hour, 2) }}</strong></span>
                </div>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="card">
            <div class="card-header">
                <h2>Account Information</h2>
            </div>
            <div class="card-body">
                <div class="user-card">
                    <div class="user-avatar">{{ strtoupper(substr($superstar->user->name, 0, 1)) }}</div>
                    <div class="user-details">
                        <div class="user-name">{{ $superstar->user->name }}</div>
                        <div class="user-email">{{ $superstar->user->email }}</div>
                        <div class="user-username">@{{ $superstar->user->username }}</div>
                    </div>
                </div>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                    <div class="info-row">
                        <span class="label">Phone</span>
                        <span class="value">{{ $superstar->user->phone ?? 'Not provided' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Account Status</span>
                        <span class="value">
                            @if ($superstar->user->is_verified)
                                <span class="badge badge-success">Verified</span>
                            @else
                                <span class="badge badge-warning">Not Verified</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Account Active</span>
                        <span class="value">
                            @if (!$superstar->user->is_blocked)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Blocked</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Stats Card -->
        <div class="card">
            <div class="card-header">
                <h2>Statistics</h2>
            </div>
            <div class="card-body">
                <div class="stat-grid">
                    <div class="stat-item">
                        <div class="stat-label">Rating</div>
                        <div class="stat-value">{{ $superstar->rating }}/5.00</div>
                        <div class="stat-bar">
                            <div class="stat-fill" style="width: {{ ($superstar->rating / 5) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Followers</div>
                        <div class="stat-value">{{ number_format($superstar->total_followers) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="card">
            <div class="card-header">
                <h2>Status</h2>
            </div>
            <div class="card-body">
                <div class="status-info">
                    <div class="info-row">
                        <span class="label">Availability</span>
                        <span class="value">
                            @if ($superstar->is_available)
                                <span class="badge badge-success">Available</span>
                            @else
                                <span class="badge badge-danger">Unavailable</span>
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="label">Account Status</span>
                        <span class="value">
                            <span class="badge badge-{{ $superstar->status === 'active' ? 'success' : ($superstar->status === 'inactive' ? 'warning' : 'danger') }}">
                                {{ ucfirst($superstar->status) }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card full-width">
        <div class="card-header">
            <h2>Timeline</h2>
        </div>
        <div class="card-body">
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker created"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Created</div>
                        <div class="timeline-date">{{ $superstar->created_at->format('F d, Y g:i A') }}</div>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker updated"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Last Updated</div>
                        <div class="timeline-date">{{ $superstar->updated_at->format('F d, Y g:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="action-footer">
        <a href="{{ route('admin.superstars.index') }}" class="btn btn-secondary">Back to List</a>
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
        align-items: flex-start;
    }

    .page-header h1 {
        font-size: 32px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 5px;
    }

    .subtitle {
        color: #718096;
        font-size: 14px;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .card-header {
        padding: 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .card-header h2 {
        font-size: 16px;
        font-weight: 600;
        color: #1a202c;
        margin: 0;
    }

    .card-body {
        padding: 20px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .label {
        font-weight: 500;
        color: #718096;
        font-size: 14px;
    }

    .value {
        color: #2d3748;
        font-size: 14px;
    }

    .user-card {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 600;
    }

    .user-details {
        flex: 1;
    }

    .user-name {
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 4px;
    }

    .user-email {
        font-size: 13px;
        color: #718096;
    }

    .user-username {
        font-size: 13px;
        color: #a0aec0;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-label {
        font-size: 13px;
        color: #718096;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 600;
        color: #667eea;
        margin-bottom: 12px;
    }

    .stat-bar {
        height: 6px;
        background-color: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }

    .stat-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transition: width 0.3s ease;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
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

    .status-info {
        display: flex;
        flex-direction: column;
    }

    .timeline {
        position: relative;
        padding-left: 40px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -40px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 3px solid white;
        background-color: #cbd5e0;
    }

    .timeline-marker.created {
        background-color: #48bb78;
    }

    .timeline-marker.updated {
        background-color: #667eea;
    }

    .timeline-content::before {
        content: '';
        position: absolute;
        left: -30px;
        top: 20px;
        width: 2px;
        height: calc(100% + 20px);
        background-color: #e2e8f0;
    }

    .timeline-item:last-child .timeline-content::before {
        display: none;
    }

    .timeline-title {
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 4px;
    }

    .timeline-date {
        font-size: 13px;
        color: #718096;
    }

    .btn {
        padding: 10px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .btn-warning {
        color: #f6ad55;
        background-color: transparent;
        border: 1px solid #f6ad55;
    }

    .btn-warning:hover {
        background-color: rgba(246, 173, 85, 0.1);
    }

    .btn-danger {
        color: #f56565;
        background-color: transparent;
        border: 1px solid #f56565;
    }

    .btn-danger:hover {
        background-color: rgba(245, 101, 101, 0.1);
    }

    .btn-secondary {
        background-color: #e2e8f0;
        color: #4a5568;
    }

    .btn-secondary:hover {
        background-color: #cbd5e0;
    }

    .inline-form {
        display: inline;
    }

    .action-footer {
        margin-top: 30px;
    }

    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .header-top {
            flex-direction: column;
            gap: 20px;
        }

        .header-actions {
            width: 100%;
        }
    }
</style>
@endsection
