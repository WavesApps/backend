<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;
use App\Models\Superstar;
use App\Models\UserGoogle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/subscriptions",
     *     summary="Get user subscriptions",
     *     description="Get authenticated user's subscriptions with pagination. Returns superstars with their details",
     *     tags={"User Subscriptions"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscriptions retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="display_name", type="string", example="John Doe"),
     *                 @OA\Property(property="bio", type="string", example="Professional streamer"),
     *                 @OA\Property(property="price_per_hour", type="number", format="float", example=0.50),
     *                 @OA\Property(property="is_available", type="boolean", example=true),
     *                 @OA\Property(property="rating", type="number", format="float", example=4.5),
     *                 @OA\Property(property="total_followers", type="integer", example=1500),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="subscribed_at", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     *             )),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=75)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $perPage = $request->get('per_page', 15);
        
        $subscriptions = $user->superstars()
            ->select([
                'superstars.id',
                'superstars.user_id',
                'superstars.display_name',
                'superstars.bio',
                'superstars.price_per_hour',
                'superstars.is_available',
                'superstars.rating',
                'superstars.total_followers',
                'superstars.status',
                'subscribes.created_at as subscribed_at'
            ])
            ->orderBy('subscribes.created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/user/subscriptions",
     *     summary="Subscribe to superstar",
     *     description="Subscribe to a superstar and increases total_followers count",
     *     tags={"User Subscriptions"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"superstar_id"},
     *             @OA\Property(property="superstar_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subscription successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subscribed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"superstar_id": {"The superstar id field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Superstar not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Superstar not found")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $validated = $request->validate([
            'superstar_id' => 'required|exists:superstars,id',
        ]);

        $superstar = Superstar::findOrFail($validated['superstar_id']);

        // Check if already subscribed
        if ($user->superstars()->where('superstar_id', $superstar->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already subscribed to this superstar',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Subscribe
        $user->superstars()->attach($superstar->id);

        // Increase total_followers
        $superstar->increment('total_followers');

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to superstar',
            'data' => [
                'superstar' => [
                    'id' => $superstar->id,
                    'display_name' => $superstar->display_name,
                    'total_followers' => $superstar->total_followers,
                ],
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/user/subscriptions/{superstarId}",
     *     summary="Subscribe to superstar by ID",
     *     description="Subscribe to a superstar using ID in URL and increases total_followers count",
     *     tags={"User Subscriptions"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="superstarId",
     *         in="path",
     *         description="Superstar ID to subscribe to",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Subscription successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully subscribed to superstar"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="superstar", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="display_name", type="string", example="John Doe"),
     *                     @OA\Property(property="total_followers", type="integer", example=1501)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Already subscribed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are already subscribed to this superstar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Superstar not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Superstar not found")
     *         )
     *     )
     * )
     */
    public function subscribe($superstarId)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $superstar = Superstar::findOrFail($superstarId);

        // Debug logging
        Log::info('Subscription Debug:', [
            'user_id' => $user->id,
            'superstar_id' => $superstar->id,
            'current_followers' => $superstar->total_followers,
            'is_already_subscribed' => $user->superstars()->where('superstar_id', $superstar->id)->exists()
        ]);

        // Check if already subscribed
        if ($user->superstars()->where('superstar_id', $superstar->id)->exists()) {
            Log::info('User already subscribed, skipping increment');
            return response()->json([
                'success' => false,
                'message' => 'You are already subscribed to this superstar',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Subscribe
        $user->superstars()->attach($superstar->id);
        
        Log::info('Before increment - followers: ' . $superstar->fresh()->total_followers);

        // Increase total_followers
        $superstar->increment('total_followers');
        
        Log::info('After increment - followers: ' . $superstar->fresh()->total_followers);

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to superstar',
            'data' => [
                'superstar' => [
                    'id' => $superstar->id,
                    'display_name' => $superstar->display_name,
                    'total_followers' => $superstar->total_followers,
                ],
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/user/subscriptions/{superstarId}",
     *     summary="Unsubscribe from superstar",
     *     description="Unsubscribe from a superstar and decreases total_followers count",
     *     tags={"User Subscriptions"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="superstarId",
     *         in="path",
     *         description="Superstar ID to unsubscribe from",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Unsubscription successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unsubscribed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Subscription not found"
     *     )
     * )
     */
    public function destroy($superstarId)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $superstar = Superstar::findOrFail($superstarId);

        // Check if subscribed
        if (!$user->superstars()->where('superstar_id', $superstar->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not subscribed to this superstar',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Unsubscribe
        $user->superstars()->detach($superstar->id);

        // Decrease total_followers (but don't go below 0)
        if ($superstar->total_followers > 0) {
            $superstar->decrement('total_followers');
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully unsubscribed from superstar',
            'data' => [
                'superstar' => [
                    'id' => $superstar->id,
                    'display_name' => $superstar->display_name,
                    'total_followers' => $superstar->fresh()->total_followers,
                ],
            ],
        ], Response::HTTP_OK);
    }

    /**
     * View superstar details by ID
     * Returns all superstar details
     */
    public function show($id)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $superstar = Superstar::select([
            'id',
            'user_id',
            'display_name',
            'bio',
            'price_per_hour',
            'is_available',
            'rating',
            'total_followers',
            'status',
        ])->findOrFail($id);

        // Check if user is subscribed
        $isSubscribed = $user->superstars()->where('superstar_id', $id)->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'superstar' => $superstar,
                'is_subscribed' => $isSubscribed,
            ],
        ], Response::HTTP_OK);
    }
}

