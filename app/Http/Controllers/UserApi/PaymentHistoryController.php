<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\PaymentBreakdown;
use App\Models\Superstar;

class PaymentHistoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/payment-history/user",
     *     summary="Get all user payments",
     *     description="Fetch all user payments with pagination",
     *     tags={"User Payment History"},
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
     *         description="Payments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="payments", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     )
     * )
     */
    public function getUserPayments(Request $request)
    {
        $user = auth()->user();
        
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        $payments = Payment::where('user_google_id', $user->id)
            ->with(['superstar', 'chatSession', 'paymentBreakdown'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
            
        // Transform payments to remove 'id' field and include all data
        $transformedPayments = $payments->getCollection()->map(function ($payment) {
            return [
                'user' => [
                    'name' => $payment->user->name ?? 'Unknown',
                    'email' => $payment->user->email ?? 'Unknown'
                ],
                'superstar' => [
                    'name' => $payment->superstar->display_name ?? 'Unknown',
                    'username' => $payment->superstar->username ?? 'Unknown'
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
                    'ended_at' => $payment->chatSession->ended_at ? $payment->chatSession->ended_at->format('Y-m-d H:i:s') : null
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
     *     path="/api/user/payment-history/superstar/{superstarId}",
     *     summary="Get payments by superstar",
     *     description="Fetch payments filtered by specific superstar with pagination",
     *     tags={"User Payment History"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="superstarId",
     *         in="path",
     *         description="Superstar ID",
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
     *         description="Payments retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="superstar", type="object"),
     *             @OA\Property(property="payments", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     )
     * )
     */
    public function getPaymentsBySuperstar(Request $request, $superstarId)
    {
        $user = auth()->user();
        
        // Verify superstar exists
        $superstar = Superstar::findOrFail($superstarId);
        
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        $payments = Payment::where('user_google_id', $user->id)
            ->where('superstar_id', $superstarId)
            ->with(['superstar', 'chatSession', 'paymentBreakdown'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
            
        // Transform payments to remove 'id' field
        $transformedPayments = $payments->getCollection()->map(function ($payment) {
            return [
                'user' => [
                    'name' => $payment->user->name ?? 'Unknown',
                    'email' => $payment->user->email ?? 'Unknown'
                ],
                'superstar' => [
                    'name' => $payment->superstar->display_name ?? 'Unknown',
                    'username' => $payment->superstar->username ?? 'Unknown',
                    'rating' => $payment->superstar->rating ?? 0,
                    'total_followers' => $payment->superstar->total_followers ?? 0
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
                    'ended_at' => $payment->chatSession->ended_at ? $payment->chatSession->ended_at->format('Y-m-d H:i:s') : null
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
            'superstar' => [
                'id' => $superstar->id,
                'name' => $superstar->display_name,
                'username' => $superstar->username,
                'rating' => $superstar->rating,
                'total_followers' => $superstar->total_followers
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
    
    /**
     * @OA\Get(
     *     path="/api/user/payment-history/transaction/{transactionReference}",
     *     summary="Get transaction details",
     *     description="View complete transaction details by reference",
     *     tags={"User Payment History"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="transactionReference",
     *         in="path",
     *         description="Transaction reference",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="transaction", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Transaction not found"
     *     )
     * )
     */
    public function getTransactionDetails($transactionReference)
    {
        $user = auth()->user();
        
        $payment = Payment::where('transaction_reference', $transactionReference)
            ->where('user_google_id', $user->id)
            ->with(['superstar', 'chatSession', 'paymentBreakdown'])
            ->first();
            
        if (!$payment) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }
        
        // Return complete transaction details without 'id'
        return response()->json([
            'transaction' => [
                'user' => [
                    'name' => $payment->user->name ?? 'Unknown',
                    'email' => $payment->user->email ?? 'Unknown'
                ],
                'superstar' => [
                    'name' => $payment->superstar->display_name ?? 'Unknown',
                    'username' => $payment->superstar->username ?? 'Unknown',
                    'rating' => $payment->superstar->rating ?? 0,
                    'total_followers' => $payment->superstar->total_followers ?? 0,
                    'price_per_hour' => $payment->superstar->price_per_hour ?? 0
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
                    'ended_at' => $payment->chatSession->ended_at ? $payment->chatSession->ended_at->format('Y-m-d H:i:s') : null
                ] : null,
                'breakdown' => $payment->paymentBreakdown ? [
                    'superstar_amount' => $payment->paymentBreakdown->superstar_amount,
                    'system_amount' => $payment->paymentBreakdown->system_amount,
                    'superstar_percentage' => $payment->paymentBreakdown->superstar_percentage,
                    'system_percentage' => $payment->paymentBreakdown->system_percentage
                ] : null,
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $payment->updated_at->format('Y-m-d H:i:s')
            ]
        ]);
    }
}
