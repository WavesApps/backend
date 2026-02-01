@extends('adminpages.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h2 class="card-title">Welcome back, {{ auth()->user()->name ?? 'Admin' }}! 👋</h2>
                    <p class="card-text mb-0">Here's what's happening with your platform today.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                            <div class="text-xs text-muted">
                                <i class="fas fa-arrow-up text-success"></i> {{ $newUsersToday }} today
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Superstars Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Superstars
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSuperstars }}</div>
                            <div class="text-xs text-muted">
                                <i class="fas fa-arrow-up text-success"></i> {{ $activeSuperstars }} active
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalRevenue, 2) }}</div>
                            <div class="text-xs text-muted">
                                <i class="fas fa-arrow-up text-success"></i> ${{ number_format($todayRevenue, 2) }} today
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Payments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPayments }}</div>
                            <div class="text-xs text-muted">
                                <i class="fas fa-calendar text-info"></i> This month: {{ $thisMonthRevenue ? '$' . number_format($thisMonthRevenue, 2) : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <!-- Content Stats -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Content Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Posts</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPosts }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Messages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMessages }}</div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Conversations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalConversations }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">New Posts Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $newPostsToday }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Breakdown -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">System Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($systemRevenue, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Superstar Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($superstarRevenue, 2) }}</div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Subscriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSubscriptions }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Subs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeSubscriptions }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Growth -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">User Growth</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Google Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGoogleUsers }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">New This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $newUsersThisMonth }}</div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">New Superstars</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $newSuperstarsThisMonth }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">New Messages Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $newMessagesToday }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <!-- Recent Payments -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Payments</h6>
                    <a href="{{ route('admin.finance.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Superstar</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPayments as $payment)
                                        <tr>
                                            <td>{{ $payment->user->username ?? 'N/A' }}</td>
                                            <td>{{ $payment->superstar->display_name ?? 'N/A' }}</td>
                                            <td>${{ number_format($payment->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $payment->payment_status == 'paid' ? 'success' : ($payment->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($payment->payment_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent payments</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Users</h6>
                    <a href="{{ route('admin.usergoogles.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentUsers as $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">{{ $user->username ?? $user->email }}</h6>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                    <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No recent users</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top Superstars -->
    @if($topSuperstars->count() > 0)
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top Superstars by Revenue</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($topSuperstars as $index => $superstar)
                            <div class="col-md-2 col-sm-4 col-6 mb-3 text-center">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $superstar->display_name ?? $superstar->username }}</h5>
                                        <p class="card-text">
                                            <strong>${{ number_format($superstar->payments_sum_total_amount ?? 0, 2) }}</strong>
                                        </p>
                                        <span class="badge badge-primary">#{{ $index + 1 }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }
    .text-xs {
        font-size: 0.7rem;
    }
    .font-weight-bold {
        font-weight: 700 !important;
    }
    .text-uppercase {
        text-transform: uppercase !important;
    }
    .text-gray-300 {
        color: #dddfeb !important;
    }
    .text-gray-800 {
        color: #5a5c69 !important;
    }
    .fa-2x {
        font-size: 2em;
    }
    .no-gutters {
        margin-right: 0;
        margin-left: 0;
    }
    .no-gutters > .col,
    .no-gutters > [class*="col-"] {
        padding-right: 0;
        padding-left: 0;
    }
    .mr-2 {
        margin-right: 0.5rem !important;
    }
    .py-2 {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
    }
    .h-100 {
        height: 100% !important;
    }
    .shadow {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    }
</style>
@endpush
