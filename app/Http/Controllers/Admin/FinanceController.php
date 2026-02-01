<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentBreakdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    /**
     * Display a listing of all payments with pagination.
     */
    public function index(Request $request)
    {
        $payments = Payment::with(['user', 'superstar', 'breakdowns'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('adminpages.finance.index', compact('payments'));
    }

    /**
     * Show the details of a specific payment.
     */
    public function show($id)
    {
        $payment = Payment::with(['user', 'superstar', 'breakdowns'])
            ->findOrFail($id);

        return view('adminpages.finance.show', compact('payment'));
    }

    /**
     * Get payment statistics for dashboard.
     */
    public function statistics()
    {
        $totalRevenue = Payment::sum('amount');
        $totalPayments = Payment::count();
        $systemRevenue = PaymentBreakdown::where('type', 'system')->sum('amount');
        $superstarRevenue = PaymentBreakdown::where('type', 'superstar')->sum('amount');

        $recentPayments = Payment::with(['user', 'superstar'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'totalRevenue' => $totalRevenue,
            'totalPayments' => $totalPayments,
            'systemRevenue' => $systemRevenue,
            'superstarRevenue' => $superstarRevenue,
            'recentPayments' => $recentPayments
        ]);
    }
}
