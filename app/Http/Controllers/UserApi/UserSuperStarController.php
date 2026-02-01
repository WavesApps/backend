<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;
use App\Models\Superstar;
use App\Models\SuperstarPost;
use App\Models\UserGoogle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

class UserSuperStarController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/superstars",
     *     summary="Get all superstars",
     *     description="Fetch all superstars with pagination",
     *     tags={"User Superstars"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Superstars retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="display_name", type="string", example="John Doe"),
     *                     @OA\Property(property="bio", type="string", example="Professional superstar"),
     *                     @OA\Property(property="price_per_hour", type="number", format="float", example=50.00),
     *                     @OA\Property(property="is_available", type="boolean", example=true),
     *                     @OA\Property(property="rating", type="number", format="float", example=4.5),
     *                     @OA\Property(property="total_followers", type="integer", example=1500),
     *                     @OA\Property(property="status", type="string", example="active")
     *                 )),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=75),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=15),
     *                 @OA\Property(property="has_more_pages", type="boolean", example=true)
     *             )
     *         )
     *     )
     * )
     */
    /**
     * Fetch all superstars with pagination.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);

        $superstars = Superstar::select([
                'id',
                'user_id',
                'display_name',
                'bio',
                'price_per_hour',
                'is_available',
                'rating',
                'total_followers',
                'status',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $superstars,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/user/superstars/{id}",
     *     summary="Get superstar details",
     *     description="Fetch superstar details and whether authenticated user is subscribed",
     *     tags={"User Superstars"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Superstar ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Superstar details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="superstar", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="display_name", type="string", example="John Doe"),
     *                     @OA\Property(property="bio", type="string", example="Professional superstar"),
     *                     @OA\Property(property="price_per_hour", type="number", format="float", example=50.00),
     *                     @OA\Property(property="is_available", type="boolean", example=true),
     *                     @OA\Property(property="rating", type="number", format="float", example=4.5),
     *                     @OA\Property(property="total_followers", type="integer", example=1500),
     *                     @OA\Property(property="status", type="string", example="active")
     *                 ),
     *                 @OA\Property(property="is_subscribed", type="boolean", example=true)
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
     *         response=404,
     *         description="Superstar not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Superstar not found")
     *         )
     *     )
     * )
     */
    /**
     * Fetch superstar details and whether authenticated user is subscribed.
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

        $isSubscribed = $user->superstars()->where('superstar_id', $id)->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'superstar' => $superstar,
                'is_subscribed' => $isSubscribed,
            ],
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/user/superstars/{id}/posts",
     *     summary="Get superstar posts",
     *     description="Fetch superstar posts (paginated), newest first",
     *     tags={"User Superstars"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Superstar ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Superstar posts retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="media_type", type="string", example="image"),
     *                     @OA\Property(property="resource_type", type="string", example="upload"),
     *                     @OA\Property(property="resource_url_path", type="string", example="posts/image123.jpg"),
     *                     @OA\Property(property="description", type="string", nullable=true, example="Check out my new post!"),
     *                     @OA\Property(property="is_pg", type="boolean", example=true),
     *                     @OA\Property(property="is_disturbing", type="boolean", example=false),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     *                 )),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=75),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=15),
     *                 @OA\Property(property="has_more_pages", type="boolean", example=true)
     *             )
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
    /**
     * Fetch superstar posts (paginated), newest first.
     */
    public function posts(Request $request, $id)
    {
        $perPage = (int) $request->get('per_page', 15);

        $superstar = Superstar::findOrFail($id);

        $posts = SuperstarPost::where('user_id', $superstar->user_id)
            ->select(['id', 'user_id', 'media_type', 'resource_type', 'resource_url_path', 'description', 'is_pg', 'is_disturbing', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $posts,
        ], Response::HTTP_OK);
    }
}