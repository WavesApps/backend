<?php

namespace App\Http\Controllers\SuperStar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use App\Models\SuperstarStory;

class SuperstarStoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/superstar/stories",
     *     summary="Get superstar stories",
     *     description="Get all active stories for authenticated superstar",
     *     tags={"Superstar Stories"},
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
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="username", type="string", example="superstar123")
     *                 )
     *             )),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=30)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $superstar = $request->user();
        
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        // Get only active stories (less than 24 hours old)
        $stories = SuperstarStory::where('postedby_userid', $superstar->id)
            ->active()
            ->with('user')
            ->orderBy('timestap', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
            
        return response()->json([
            'stories' => $stories->items(),
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
     * @OA\Post(
     *     path="/api/superstar/stories",
     *     summary="Create superstar story",
     *     description="Upload a new story (image or video)",
     *     tags={"Superstar Stories"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"file_type"},
     *             @OA\Property(property="file_type", type="string", enum={"image","video"}, example="image"),
     *             @OA\Property(property="file", type="string", format="binary", description="Story file (image or video)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Story created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Story created successfully"),
     *             @OA\Property(property="story", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="postedby_userid", type="integer", example=2),
     *                 @OA\Property(property="file_type", type="string", example="image"),
     *                 @OA\Property(property="url_path", type="string", example="stories/image123.jpg"),
     *                 @OA\Property(property="timestap", type="string", format="date-time"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object", example={"file_type": {"The file type field is required."}})
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_type' => 'required|in:image,video',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $superstar = $request->user();
        
        if (!$request->hasFile('file')) {
            return response()->json([
                'message' => 'No file uploaded'
            ], 422);
        }

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('stories', $fileName, 'local');
        
        $story = SuperstarStory::create([
            'postedby_userid' => $superstar->id,
            'file_type' => $request->file_type,
            'url_path' => $filePath,
            'timestap' => now(),
        ]);

        return response()->json([
            'message' => 'Story created successfully',
            'story' => $story->load('user')
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/superstar/stories/{id}",
     *     summary="Get specific story",
     *     description="Get a specific story by ID",
     *     tags={"Superstar Stories"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Story ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Story retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="story", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="postedby_userid", type="integer", example=2),
     *                 @OA\Property(property="file_type", type="string", example="image"),
     *                 @OA\Property(property="url_path", type="string", example="stories/image123.jpg"),
     *                 @OA\Property(property="timestap", type="string", format="date-time"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="username", type="string", example="superstar123")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Story not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Story not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $story = SuperstarStory::with('user')->find($id);
        
        if (!$story) {
            return response()->json([
                'message' => 'Story not found'
            ], 404);
        }

        return response()->json([
            'story' => $story
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/superstar/stories/{id}",
     *     summary="Delete story",
     *     description="Delete a specific story",
     *     tags={"Superstar Stories"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Story ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Story deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Story deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Story not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Story not found")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $superstar = $request->user();
        $story = SuperstarStory::where('id', $id)
            ->where('postedby_userid', $superstar->id)
            ->first();
            
        if (!$story) {
            return response()->json([
                'message' => 'Story not found'
            ], 404);
        }
        
        // Delete file from storage
        if ($story->url_path && Storage::disk('local')->exists($story->url_path)) {
            Storage::disk('local')->delete($story->url_path);
        }
        
        $story->delete();
        
        return response()->json([
            'message' => 'Story deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/superstar/stories/file/{filename}",
     *     summary="Get story file",
     *     description="Serve story file from storage (public access)",
     *     tags={"Superstar Stories"},
     *     @OA\Parameter(
     *         name="filename",
     *         in="path",
     *         description="Story filename",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File served successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="file", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="File not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="File not found")
     *         )
     *     )
     * )
     */
    public function getFile($filename)
    {
        $filePath = 'stories/' . $filename;
        
        if (!Storage::disk('local')->exists($filePath)) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }
        
        $file = Storage::disk('local')->get($filePath);
        $fullPath = storage_path('app/' . $filePath);
        $mimeType = File::mimeType($fullPath);
        
        return Response::make($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
