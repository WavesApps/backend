<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment;
use App\Models\PaymentBreakdown;
use App\Models\ChatSession;
use App\Models\Conversation;
use App\Models\Superstar;

class PaymentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/payments/process",
     *     summary="Process payment",
     *     description="Process payment for chat session and create payment records",
     *     tags={"User Payments"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"conversation_id","payment_method","amount"},
     *             @OA\Property(property="conversation_id", type="integer", example=1),
     *             @OA\Property(property="payment_method", type="string", enum={"wallet","card","mobile_money"}, example="card"),
     *             @OA\Property(property="amount", type="number", format="float", example=50.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment processed successfully"),
     *             @OA\Property(property="payment", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|exists:conversations,id',
            'payment_method' => 'required|in:wallet,card,mobile_money',
            'amount' => 'required|numeric|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $conversation = Conversation::findOrFail($request->conversation_id);

        // Verify user owns this conversation
        if ($conversation->user_google_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access to conversation'
            ], 403);
        }

        // Get superstar info for pricing
        $superstar = Superstar::findOrFail($conversation->superstar_id);
        $pricePerMinute = $superstar->price_per_hour / 60; // Convert hourly to per minute

        // Create or update chat session
        $chatSession = ChatSession::updateOrCreate(
            [
                'conversation_id' => $conversation->id,
                'status' => 'ongoing'
            ],
            [
                'started_at' => now(),
                'price_per_minute' => $pricePerMinute,
                'status' => 'ongoing'
            ]
        );

        // Calculate total minutes from amount
        $totalMinutes = floor($request->amount / $pricePerMinute);
        $totalAmount = $totalMinutes * $pricePerMinute;

        // Generate unique transaction reference
        $transactionReference = 'PAY_' . uniqid() . '_' . time();

        // Create payment record
        $payment = Payment::create([
            'user_google_id' => $user->id,
            'superstar_id' => $superstar->id,
            'total_amount' => $totalAmount,
            'chat_sessions_id' => $chatSession->id,
            'payment_method' => $request->payment_method,
            'payment_status' => 'paid', // Simulate successful payment
            'transaction_reference' => $transactionReference,
            'paid_at' => now()
        ]);

        // Calculate payment breakdown (80/20 split)
        $superstarAmount = $totalAmount * 0.80;
        $systemAmount = $totalAmount * 0.20;

        // Create payment breakdown
        PaymentBreakdown::create([
            'payment_id' => $payment->id,
            'superstar_amount' => $superstarAmount,
            'system_amount' => $systemAmount,
            'superstar_percentage' => 80.00,
            'system_percentage' => 20.00
        ]);

        // Update chat session with calculated values
        $chatSession->update([
            'total_minutes' => $totalMinutes,
            'total_amount' => $totalAmount,
            'status' => 'completed', // Mark as completed after payment
            'ended_at' => now(),
            'session_message' => 'Session completed successfully. Thank you for your payment!'
        ]);

        return response()->json([
            'message' => 'Payment processed successfully',
            'payment' => [
                'id' => $payment->id,
                'transaction_reference' => $transactionReference,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
                'paid_at' => $payment->paid_at,
                'chat_session' => [
                    'id' => $chatSession->id,
                    'total_minutes' => $totalMinutes,
                    'price_per_minute' => $pricePerMinute,
                    'status' => 'completed'
                ],
                'breakdown' => [
                    'superstar_amount' => $superstarAmount,
                    'system_amount' => $systemAmount,
                    'superstar_percentage' => 80.00,
                    'system_percentage' => 20.00
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/payments/history",
     *     summary="Get payment history",
     *     description="Get user's payment history with pagination",
     *     tags={"User Payments"},
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
        $user = auth()->user();
        
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        $payments = Payment::where('user_google_id', $user->id)
            ->with(['superstar', 'chatSession', 'paymentBreakdown'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
            
        return response()->json([
            'payments' => $payments->items(),
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

    public function getPaymentDetails($paymentId)
    {
        $user = auth()->user();
        
        $payment = Payment::where('id', $paymentId)
            ->where('user_google_id', $user->id)
            ->with(['superstar', 'chatSession', 'paymentBreakdown'])
            ->first();
            
        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found'
            ], 404);
        }
        
        return response()->json([
            'payment' => $payment
        ]);
    }
}
