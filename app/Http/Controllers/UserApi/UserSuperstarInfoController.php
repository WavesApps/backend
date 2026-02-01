<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;
use App\Models\Superstar;
use App\Models\SuperstarPost;
use App\Models\UserGoogle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class UserSuperstarInfoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/usersuperstar",
     *     summary="Get all superstars with detailed information",
     *     description="Fetch all superstars with username, DP image, total posts, followers count, subscription status, and cost",
     *     tags={"User Superstars"},
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
     *         description="Superstars retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="username", type="string", example="johndoe123"),
     *                     @OA\Property(property="dp_image", type="string", example="https://example.com/images/user123.jpg"),
     *                     @OA\Property(property="total_posts", type="integer", example=25),
     *                     @OA\Property(property="followers", type="integer", example=1500),
     *                     @OA\Property(property="is_subscribed", type="boolean", example=true),
     *                     @OA\Property(property="cost", type="number", format="float", example=50.00),
     *                     @OA\Property(property="display_name", type="string", example="John Doe"),
     *                     @OA\Property(property="bio", type="string", example="Professional superstar")
     *                 )),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=42),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=15),
     *                 @OA\Property(property="has_more_pages", type="boolean", example=true)
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
    public function usersuperstar(Request $request)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $perPage = (int) $request->get('per_page', 15);

        // Get superstars with their user information and post counts
        $superstars = Superstar::with(['user'])
            ->select([
                'superstars.id',
                'superstars.user_id',
                'superstars.display_name',
                'superstars.bio',
                'superstars.price_per_hour as cost',
                'superstars.total_followers as followers',
                'superstars.status',
                'superstars.is_available',
                'superstars.rating'
            ])
            ->where('superstars.status', 'active')
            ->leftJoin('users', 'superstars.user_id', '=', 'users.id')
            ->addSelect([
                'users.username',
                'users.profile_image as dp_image',
                'total_posts' => SuperstarPost::selectRaw('COUNT(*)')
                    ->whereColumn('superstar_posts.user_id', 'superstars.user_id')
            ])
            ->orderBy('superstars.created_at', 'desc')
            ->paginate($perPage);

        // Get user's subscribed superstar IDs
        $subscribedSuperstarIds = $user->superstars()->pluck('superstars.id')->toArray();

        // Transform the data to include subscription status
        $transformedData = $superstars->getCollection()->map(function ($superstar) use ($subscribedSuperstarIds) {
            return [
                'id' => (int) $superstar->id,
                'username' => $superstar->username,
                'dp_image' => $superstar->dp_image,
                'total_posts' => (int) $superstar->total_posts,
                'followers' => (int) $superstar->followers,
                'is_subscribed' => in_array($superstar->id, $subscribedSuperstarIds),
                'cost' => (float) $superstar->cost,
                'display_name' => $superstar->display_name,
                'bio' => $superstar->bio,
            ];
        });

        // Replace the collection in the paginator
        $superstars->setCollection($transformedData);

        return response()->json([
            'success' => true,
            'data' => $superstars,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/user/feed",
     *     summary="Get feed of posts from subscribed superstars",
     *     description="Get paginated posts from all superstars that the authenticated user is subscribed to, ordered by most recent posts first",
     *     tags={"User Feed"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="media_type",
     *         in="query",
     *         description="Filter by media type",
     *         required=false,
     *         @OA\Schema(type="string", enum={"image", "video"})
     *     ),
     *     @OA\Parameter(
     *         name="is_pg",
     *         in="query",
     *         description="Filter by PG rating",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="is_disturbing",
     *         in="query",
     *         description="Filter by disturbing content",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Feed retrieved successfully",
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
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:35:00Z"),
     *                     @OA\Property(property="superstar", type="object",
     *                         @OA\Property(property="id", type="integer", example=5),
     *                         @OA\Property(property="display_name", type="string", example="John Doe"),
     *                         @OA\Property(property="username", type="string", example="johndoe"),
     *                         @OA\Property(property="profile_image", type="string", example="https://example.com/images/user123.jpg")
     *                     )
     *                 )),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=42),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=15),
     *                 @OA\Property(property="has_more_pages", type="boolean", example=true)
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
    public function feed(Request $request)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $perPage = (int) $request->get('per_page', 15);
        $page = (int) $request->get('page', 1);
        
        // Filter parameters
        $mediaType = $request->get('media_type');
        $isPg = $request->get('is_pg');
        $isDisturbing = $request->get('is_disturbing');

        // Get posts from subscribed superstars only
        $query = SuperstarPost::with(['superstar.user'])
            ->whereIn('user_id', function ($query) use ($user) {
                $query->select('superstars.id')
                    ->from('superstars')
                    ->join('subscribes', 'superstars.id', '=', 'subscribes.superstar_id')
                    ->where('subscribes.user_google_id', $user->id);
            })
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($mediaType) {
            $query->where('media_type', $mediaType);
        }
        if ($isPg !== null) {
            $query->where('is_pg', filter_var($isPg, FILTER_VALIDATE_BOOLEAN));
        }
        if ($isDisturbing !== null) {
            $query->where('is_disturbing', filter_var($isDisturbing, FILTER_VALIDATE_BOOLEAN));
        }

        $posts = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform the data to include superstar information
        $transformedPosts = collect($posts->items())->map(function ($post) {
            return [
                'id' => $post->id,
                'user_id' => $post->user_id,
                'media_type' => $post->media_type,
                'resource_type' => $post->resource_type,
                'resource_url_path' => $post->resource_url_path,
                'description' => $post->description,
                'is_pg' => $post->is_pg,
                'is_disturbing' => $post->is_disturbing,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'superstar' => [
                    'id' => $post->superstar->id,
                    'display_name' => $post->superstar->display_name,
                    'username' => $post->superstar->user ? $post->superstar->user->username : null,
                    'profile_image' => $post->superstar->user ? $post->superstar->user->profile_image : null,
                ]
            ];
        });

        // Replace the collection in the paginator
        $posts->setCollection($transformedPosts);

        return response()->json([
            'success' => true,
            'data' => $posts,
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/user/superstars/{id}/details",
     *     summary="Get superstar details with posts",
     *     description="Get comprehensive information about a superstar including bio, stats, and paginated posts. Only accessible if user follows the superstar.",
     *     tags={"User Superstars"},
     *     security={{"sanctum":{}}},
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
     *         description="Number of posts per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for posts",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="media_type",
     *         in="query",
     *         description="Filter posts by media type",
     *         required=false,
     *         @OA\Schema(type="string", enum={"image", "video"})
     *     ),
     *     @OA\Parameter(
     *         name="is_pg",
     *         in="query",
     *         description="Filter posts by PG rating",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="is_disturbing",
     *         in="query",
     *         description="Filter posts by disturbing content",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Superstar details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="superstar", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=3),
     *                     @OA\Property(property="display_name", type="string", example="John Doe"),
     *                     @OA\Property(property="bio", type="string", example="Professional superstar with amazing content"),
     *                     @OA\Property(property="price_per_hour", type="number", format="float", example=50.00),
     *                     @OA\Property(property="is_available", type="boolean", example=true),
     *                     @OA\Property(property="rating", type="number", format="float", example=4.5),
     *                     @OA\Property(property="total_followers", type="integer", example=1500),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="username", type="string", example="johndoe"),
     *                     @OA\Property(property="profile_image", type="string", example="https://example.com/images/user123.jpg"),
     *                     @OA\Property(property="is_subscribed", type="boolean", example=true)
     *                 ),
     *                 @OA\Property(property="posts", type="object",
     *                     @OA\Property(property="data", type="array", @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="user_id", type="integer", example=3),
     *                         @OA\Property(property="media_type", type="string", example="image"),
     *                         @OA\Property(property="resource_type", type="string", example="upload"),
     *                         @OA\Property(property="resource_url_path", type="string", example="posts/image123.jpg"),
     *                         @OA\Property(property="description", type="string", nullable=true, example="Check out my new post!"),
     *                         @OA\Property(property="is_pg", type="boolean", example=true),
     *                         @OA\Property(property="is_disturbing", type="boolean", example=false),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:35:00Z")
     *                     )),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=3),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="total", type="integer", example=42),
     *                     @OA\Property(property="from", type="integer", example=1),
     *                     @OA\Property(property="to", type="integer", example=15),
     *                     @OA\Property(property="has_more_pages", type="boolean", example=true)
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
     *         response=403,
     *         description="Not subscribed to superstar",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You must subscribe to this superstar to view their content")
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
     *     )
     * )
     */
    public function superstarDetails(Request $request, $id)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Get the superstar with user information
        $superstar = Superstar::with(['user'])
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
                'superstars.created_at',
                'superstars.updated_at'
            ])
            ->findOrFail($id);

        // Check if user is subscribed to this superstar
        $isSubscribed = $user->superstars()->where('superstar_id', $superstar->id)->exists();

        if (!$isSubscribed) {
            return response()->json([
                'success' => false,
                'message' => 'You must subscribe to this superstar to view their content',
            ], Response::HTTP_FORBIDDEN);
        }

        // Pagination and filter parameters
        $perPage = (int) $request->get('per_page', 15);
        $page = (int) $request->get('page', 1);
        $mediaType = $request->get('media_type');
        $isPg = $request->get('is_pg');
        $isDisturbing = $request->get('is_disturbing');

        // Get posts for this superstar
        $query = SuperstarPost::where('user_id', $superstar->id)
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($mediaType) {
            $query->where('media_type', $mediaType);
        }
        if ($isPg !== null) {
            $query->where('is_pg', filter_var($isPg, FILTER_VALIDATE_BOOLEAN));
        }
        if ($isDisturbing !== null) {
            $query->where('is_disturbing', filter_var($isDisturbing, FILTER_VALIDATE_BOOLEAN));
        }

        $posts = $query->paginate($perPage, ['*'], 'page', $page);

        // Prepare superstar data
        $superstarData = [
            'id' => $superstar->id,
            'user_id' => $superstar->user_id,
            'display_name' => $superstar->display_name,
            'bio' => $superstar->bio,
            'price_per_hour' => (float) $superstar->price_per_hour,
            'is_available' => $superstar->is_available,
            'rating' => (float) $superstar->rating,
            'total_followers' => (int) $superstar->total_followers,
            'status' => $superstar->status,
            'username' => $superstar->user ? $superstar->user->username : null,
            'profile_image' => $superstar->user ? $superstar->user->profile_image : null,
            'is_subscribed' => $isSubscribed,
            'created_at' => $superstar->created_at,
            'updated_at' => $superstar->updated_at,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'superstar' => $superstarData,
                'posts' => $posts,
            ],
        ], Response::HTTP_OK);
    }
}
