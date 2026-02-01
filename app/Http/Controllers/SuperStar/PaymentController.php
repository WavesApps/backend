<?php

namespace App\Http\Controllers\SuperStar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentBreakdown;
use App\Models\UserGoogle;

class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/superstar/payments/history",
     *     summary="Get superstar payment history",
     *     description="Fetch superstar's own payment history with pagination",
     *     tags={"Superstar Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment history retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="payments", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     )
     * )
     */
    public function getPaymentHistory(Request $request)
    {
        $superstar = $request->user();
        
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        $payments = Payment::where('superstar_id', $superstar->id)
            ->with(['user', 'chatSession', 'paymentBreakdown'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
            
        // Transform payments to remove 'id' field and include all data
        $transformedPayments = $payments->getCollection()->map(function ($payment) {
            return [
                'user' => [
                    'name' => $payment->user->name ?? 'Unknown',
                    'email' => $payment->user->email ?? 'Unknown'
                ],
                'total_amount' => $payment->total_amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
                'transaction_reference' => $payment->transaction_reference,
                'paid_at' => $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : null,
                'chat_session' => $payment->chatSession ? [
                    'total_minutes' => $payment->chatSession->total_minutes,
                    'price_per_minute' => $payment->chatSession->price_per_minute,
                    'total_amount' => $payment->chatSession->total_amount,
                    'status' => $payment->chatSession->status,
                    'started_at' => $payment->chatSession->started_at ? $payment->chatSession->started_at->format('Y-m-d H:i:s') : null,
                    'ended_at' => $payment->chatSession->ended_at ? $payment->chatSession->ended_at->format('Y-m-d H:i:s') : null,
                    'session_message' => $payment->chatSession->session_message ? $payment->chatSession->session_message : null
                ] : null,
                'breakdown' => $payment->paymentBreakdown ? [
                    'superstar_amount' => $payment->paymentBreakdown->superstar_amount,
                    'system_amount' => $payment->paymentBreakdown->system_amount,
                    'superstar_percentage' => $payment->paymentBreakdown->superstar_percentage,
                    'system_percentage' => $payment->paymentBreakdown->system_percentage
                ] : null,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $payment->updated_at->format('Y-m-d H:i:s')
            ];
        });
            
        return response()->json([
            'payments' => $transformedPayments,
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
                'has_more_pages' => $payments->hasMorePages()
            ]
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/superstar/payments/system-revenue",
     *     summary="Get system revenue",
     *     description="Show system revenue (20% of all payments) with breakdown",
     *     tags={"Superstar Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="System revenue retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="revenue_summary", type="object"),
     *             @OA\Property(property="breakdowns", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     )
     * )
     */
    public function getSystemRevenue(Request $request)
    {
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        $paymentBreakdowns = PaymentBreakdown::with('payment')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
            
        // Calculate totals
        $totalSystemRevenue = PaymentBreakdown::sum('system_amount');
        $totalSuperstarRevenue = PaymentBreakdown::sum('superstar_amount');
        $totalRevenue = $totalSystemRevenue + $totalSuperstarRevenue;
        
        return response()->json([
            'revenue_summary' => [
                'total_system_revenue' => $totalSystemRevenue,
                'total_superstar_revenue' => $totalSuperstarRevenue,
                'total_revenue' => $totalRevenue,
                'system_percentage' => 20.00,
                'superstar_percentage' => 80.00
            ],
            'breakdowns' => $paymentBreakdowns->getCollection()->map(function ($breakdown) {
                return [
                    'payment' => $breakdown->payment ? [
                        'total_amount' => $breakdown->payment->total_amount,
                        'payment_method' => $breakdown->payment->payment_method,
                        'payment_status' => $breakdown->payment->payment_status,
                        'transaction_reference' => $breakdown->payment->transaction_reference,
                        'paid_at' => $breakdown->payment->paid_at ? $breakdown->payment->paid_at->format('Y-m-d H:i:s') : null,
                        'superstar' => $breakdown->payment->superstar ? [
                            'name' => $breakdown->payment->superstar->display_name,
                            'username' => $breakdown->payment->superstar->username
                        ] : null,
                        'user' => $breakdown->payment->user ? [
                            'name' => $breakdown->payment->user->name,
                            'email' => $breakdown->payment->user->email
                        ] : null
                    ] : null,
                    'superstar_amount' => $breakdown->superstar_amount,
                    'system_amount' => $breakdown->system_amount,
                    'superstar_percentage' => $breakdown->superstar_percentage,
                    'system_percentage' => $breakdown->system_percentage,
                    'created_at' => $breakdown->created_at->format('Y-m-d H:i:s')
                ];
            }),
            'pagination' => [
                'current_page' => $paymentBreakdowns->currentPage(),
                'last_page' => $paymentBreakdowns->lastPage(),
                'per_page' => $paymentBreakdowns->perPage(),
                'total' => $paymentBreakdowns->total(),
                'from' => $paymentBreakdowns->firstItem(),
                'to' => $paymentBreakdowns->lastItem(),
                'has_more_pages' => $paymentBreakdowns->hasMorePages()
            ]
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/superstar/payments/user/{userId}",
     *     summary="Get user payment history",
     *     description="View payment history for specific user with pagination",
     *     tags={"Superstar Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User payment history retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="payments", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function getUserPaymentHistory(Request $request, $userId)
    {
        $superstar = $request->user();
        
        // Verify user exists
        $user = UserGoogle::findOrFail($userId);
        
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        $payments = Payment::where('superstar_id', $superstar->id)
            ->where('user_google_id', $userId)
            ->with(['user', 'chatSession', 'paymentBreakdown'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
            
        // Transform payments to remove 'id' field
        $transformedPayments = $payments->getCollection()->map(function ($payment) {
            return [
                'user' => [
                    'name' => $payment->user->name ?? 'Unknown',
                    'email' => $payment->user->email ?? 'Unknown'
                ],
                'total_amount' => $payment->total_amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
                'transaction_reference' => $payment->transaction_reference,
                'paid_at' => $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i:s') : null,
                'chat_session' => $payment->chatSession ? [
                    'total_minutes' => $payment->chatSession->total_minutes,
                    'price_per_minute' => $payment->chatSession->price_per_minute,
                    'total_amount' => $payment->chatSession->total_amount,
                    'status' => $payment->chatSession->status,
                    'started_at' => $payment->chatSession->started_at ? $payment->chatSession->started_at->format('Y-m-d H:i:s') : null,
                    'ended_at' => $payment->chatSession->ended_at ? $payment->chatSession->ended_at->format('Y-m-d H:i:s') : null,
                    'session_message' => $payment->chatSession->session_message ? $payment->chatSession->session_message : null
                ] : null,
                'breakdown' => $payment->paymentBreakdown ? [
                    'superstar_amount' => $payment->paymentBreakdown->superstar_amount,
                    'system_amount' => $payment->paymentBreakdown->system_amount,
                    'superstar_percentage' => $payment->paymentBreakdown->superstar_percentage,
                    'system_percentage' => $payment->paymentBreakdown->system_percentage
                ] : null,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $payment->updated_at->format('Y-m-d H:i:s')
            ];
        });
            
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'payments' => $transformedPayments,
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
                'has_more_pages' => $payments->hasMorePages()
            ]
        ]);
    }
}
