@extends('adminpages.layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Transaction Information</h4>
                    <div class="card-actions">
                        <a href="{{ route('admin.finance.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left"></i>
                            Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5 class="text-primary"><i class="bi bi-info-circle"></i> Transaction Details</h5>
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Transaction Reference:</strong></div>
                                        <div class="col-sm-8">{{ $payment->transaction_reference }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Amount:</strong></div>
                                        <div class="col-sm-8"><span class="badge bg-success fs-6">${{ number_format($payment->amount, 2) }}</span></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Payment Method:</strong></div>
                                        <div class="col-sm-8"><span class="badge bg-info text-white">{{ ucfirst($payment->payment_method) }}</span></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Status:</strong></div>
                                        <div class="col-sm-8">
                                            <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }} text-white">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4"><strong>Date & Time:</strong></div>
                                        <div class="col-sm-8">{{ $payment->created_at->format('F d, Y H:i:s A') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5 class="text-primary"><i class="bi bi-person-circle"></i> User Information</h5>
                                <div class="mb-3">
                                    @if($payment->user)
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Username:</strong></div>
                                            <div class="col-sm-8">{{ $payment->user->username }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Email:</strong></div>
                                            <div class="col-sm-8">{{ $payment->user->email }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4"><strong>User ID:</strong></div>
                                            <div class="col-sm-8">#{{ $payment->user->id }}</div>
                                        </div>
                                    @else
                                        <div class="text-muted">No user information available</div>
                                    @endif
                                </div>
                            </div>
                        
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5 class="text-primary"><i class="bi bi-star"></i> Superstar Information</h5>
                                <div class="mb-3">
                                    @if($payment->superstar)
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Display Name:</strong></div>
                                            <div class="col-sm-8">{{ $payment->superstar->display_name }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Username:</strong></div>
                                            <div class="col-sm-8">{{ $payment->superstar->username }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Email:</strong></div>
                                            <div class="col-sm-8">{{ $payment->superstar->email }}</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Superstar ID:</strong></div>
                                            <div class="col-sm-8">#{{ $payment->superstar->id }}</div>
                                        </div>
                                    @else
                                        <div class="text-muted">No superstar information available</div>
                                    @endif
                                </div>
                            </div>
                        
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5 class="text-primary"><i class="bi bi-currency-dollar"></i> Revenue Breakdown</h5>
                                <div class="mb-3">
                                    @if($payment->breakdowns && $payment->breakdowns->count() > 0)
                                        @foreach($payment->breakdowns as $breakdown)
                                            <div class="row mb-2">
                                                <div class="col-sm-6"><strong>{{ ucfirst($breakdown->type) }} Revenue:</strong></div>
                                                <div class="col-sm-6"><span class="badge bg-primary fs-6">${{ number_format($breakdown->amount, 2) }}</span></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6"><strong>Percentage:</strong></div>
                                                <div class="col-sm-6"><span class="badge bg-secondary">{{ $breakdown->percentage }}%</span></div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-muted">No breakdown data available</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
