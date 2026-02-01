<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\SuperstarStory;
use App\Models\SuperstarPost;
use App\Models\SuperStar;

class PublicAuthList extends Controller
{
    /**
     * @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     securityScheme="sanctum"
     * )
     */
    
    /**
     * @OA\Get(
     *     path="/api/public/superstar-stories",
     *     summary="Get all superstar stories",
     *     description="Get paginated list of all active superstar stories with superstar info",
     *     tags={"Public API"},
     *     security={{"sanctum": {}}},
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
     *         description="Stories retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="stories", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="postedby_userid", type="integer", example=2),
     *                 @OA\Property(property="file_type", type="string", example="image"),
     *                 @OA\Property(property="url_path", type="string", example="stories/image123.jpg"),
     *                 @OA\Property(property="timestap", type="string", format="date-time"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="superstar", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="display_name", type="string", example="John Doe"),
     *                     @OA\Property(property="username", type="string", example="superstar123"),
     *                     @OA\Property(property="profile_image", type="string", nullable=true)
     *                 )
     *             )),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=30)
     *             )
     *         )
     *     )
     * )
     */
    public function getSuperstarStories(Request $request)
    {
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        // Get only active stories (less than 24 hours old) with superstar info
        $stories = SuperstarStory::active()
            ->with(['user.superstar'])
            ->orderBy('timestap', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
            
        // Transform the data to include superstar info
        $transformedStories = $stories->getCollection()->map(function ($story) {
            $superstar = $story->user->superstar ?? null;
            
            return [
                'id' => $story->id,
                'postedby_userid' => $story->postedby_userid,
                'file_type' => $story->file_type,
                'url_path' => $story->url_path,
                'timestap' => $story->timestap,
                'created_at' => $story->created_at,
                'updated_at' => $story->updated_at,
                'superstar' => $superstar ? [
                    'id' => $superstar->id,
                    'display_name' => $superstar->display_name,
                    'username' => $story->user->username,
                    'profile_image' => $story->user->profile_image
                ] : null
            ];
        });
        
        return response()->json([
            'stories' => $transformedStories,
            'pagination' => [
                'current_page' => $stories->currentPage(),
                'last_page' => $stories->lastPage(),
                'per_page' => $stories->perPage(),
                'total' => $stories->total(),
                'from' => $stories->firstItem(),
                'to' => $stories->lastItem(),
                'has_more_pages' => $stories->hasMorePages()
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/public/superstar-posts",
     *     summary="Get all superstar posts",
     *     description="Get paginated list of all superstar posts with superstar info",
     *     tags={"Public API"},
     *     security={{"sanctum": {}}},
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
     *         description="Posts retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="posts", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="superstar_id", type="integer", example=2),
     *                 @OA\Property(property="title", type="string", example="My New Post"),
     *                 @OA\Property(property="description", type="string", example="This is my post description"),
     *                 @OA\Property(property="file_type", type="string", example="image"),
     *                 @OA\Property(property="url_path", type="string", example="posts/image123.jpg"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="superstar", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="display_name", type="string", example="John Doe"),
     *                     @OA\Property(property="username", type="string", example="superstar123"),
     *                     @OA\Property(property="profile_image", type="string", nullable=true)
     *                 )
     *             )),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=30)
     *             )
     *         )
     *     )
     * )
     */
    public function getSuperstarPosts(Request $request)
    {
        // Test response to verify method is being called
        return response()->json([
            'test' => 'Method is being called',
            'posts' => [
                [
                    'id' => 999,
                    'superstar_id' => 3,
                    'title' => 'Test Post',
                    'description' => 'Test Description',
                    'superstaname' => 'Test Superstar',
                    'superstarusername' => 'testuser',
                    'superstarprofileimage' => 'test.jpg',
                    'file_type' => 'image',
                    'url_path' => 'test/path.jpg',
                    'created_at' => '2026-01-30T12:00:00Z',
                    'updated_at' => '2026-01-30T12:00:00Z',
                    'superstar' => [
                        'id' => 3,
                        'display_name' => 'Test Superstar',
                        'username' => 'testuser',
                        'profile_image' => 'test.jpg'
                    ]
                ]
            ],
            'pagination' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 15,
                'total' => 1,
                'from' => 1,
                'to' => 1,
                'has_more_pages' => false
            ]
        ]);
        
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        // Get all posts with user and superstar info
        $posts = SuperstarPost::with(['user.superstar'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
            
        // Transform the data to include superstar info
        $transformedPosts = $posts->getCollection()->map(function ($post) {
            $user = $post->user;
            $superstar = $user ? $user->superstar : null;
            
            return [
                'id' => $post->id,
                'superstar_id' => $post->user_id,
                'title' => $post->description, // Using description as title since no title field exists
                'description' => $post->description,
                'superstaname' => $superstar ? $superstar->display_name : null,
                'superstarusername' => $user ? $user->username : null,
                'superstarprofileimage' => $user ? $user->profile_image : null,
                'file_type' => $post->media_type,
                'url_path' => $post->resource_url_path,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'superstar' => $superstar ? [
                    'id' => $superstar->id,
                    'display_name' => $superstar->display_name,
                    'username' => $user ? $user->username : null,
                    'profile_image' => $user ? $user->profile_image : null
                ] : null
            ];
        });
        
        return response()->json([
            'posts' => $transformedPosts,
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'from' => $posts->firstItem(),
                'to' => $posts->lastItem(),
                'has_more_pages' => $posts->hasMorePages()
            ]
        ]);
    }
}
