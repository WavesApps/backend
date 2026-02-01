@extends('adminpages.layouts.app')

@section('title', 'Finance - Transactions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">All Transactions</h4>
                    <div class="card-actions">
                        <button class="btn btn-primary" onclick="refreshTransactions()">
                            <i class="bi bi-arrow-clockwise"></i>
                            Refresh
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>User</th>
                                        <th>Superstar</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $payment->transaction_reference }}</span>
                                            </td>
                                            <td>
                                                @if($payment->user)
                                                    <div>
                                                        <strong>{{ $payment->user->username ?? $payment->user->email }}</strong>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->superstar)
                                                    <div>
                                                        <strong>{{ $payment->superstar->display_name ?? $payment->superstar->username }}</strong>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-success">${{ number_format($payment->amount, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info text-white">{{ ucfirst($payment->payment_method) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status == 'paid' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }} text-white">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $payment->created_at->format('M d, Y H:i') }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.finance.show', $payment->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-4"></i>
                            <h5>No Transactions Found</h5>
                            <p class="text-muted">There are no payment transactions in the system yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshTransactions() {
    window.location.reload();
}

// Auto-refresh every 30 seconds
setInterval(function() {
    refreshTransactions();
}, 30000);
</script>
@endpush
