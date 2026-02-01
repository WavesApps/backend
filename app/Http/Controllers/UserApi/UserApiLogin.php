<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;
use App\Models\UserGoogle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserApiLogin extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/google-login",
     *     summary="Google OAuth Login",
     *     description="This endpoint handles Google OAuth login by checking if the email exists, updating or creating user data, and returning user data with Sanctum token",
     *     tags={"User Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","username"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="username", type="string", example="johndoe"),
     *             @OA\Property(property="image", type="string", nullable=true, example="https://example.com/avatar.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *                     @OA\Property(property="username", type="string", example="johndoe"),
     *                     @OA\Property(property="image", type="string", nullable=true, example="https://example.com/avatar.jpg")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abc123token456"),
     *                 @OA\Property(property="token_type", type="string", example="Bearer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred during login"),
     *             @OA\Property(property="error", type="string", example="Database connection failed")
     *         )
     *     )
     * )
     */
    public function googleLogin(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'email' => 'required|email',
            'username' => 'required|string|max:255',
            'image' => 'nullable|string',
        ]);

        try {
            // Check if user with this email already exists
            $user = UserGoogle::where('email', $validated['email'])->first();

            if ($user) {
                // User exists - update their information
                $user->update([
                    'username' => $validated['username'],
                    'image' => $validated['image'] ?? $user->image,
                ]);

                $message = 'Login successful';
            } else {
                // User doesn't exist - create new user
                $user = UserGoogle::create([
                    'email' => $validated['email'],
                    'username' => $validated['username'],
                    'image' => $validated['image'],
                ]);

                $message = 'Account created successfully';
            }

            // Generate Sanctum token
            $token = $user->createToken('google-auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'username' => $user->username,
                        'image' => $user->image,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/me",
     *     summary="Get authenticated user",
     *     description="Get current authenticated user details including total number of subscriptions",
     *     tags={"User Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="image", type="string", nullable=true, example="https://example.com/avatar.jpg"),
     *                 @OA\Property(property="total_subscriptions", type="integer", example=5)
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
    public function getAuthUser(Request $request)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Get total number of subscriptions
        $totalSubscriptions = $user->superstars()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'username' => $user->username,
                'image' => $user->image,
                'total_subscriptions' => $totalSubscriptions,
            ],
        ], Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/user/logout",
     *     summary="Logout user",
     *     description="Logout user and revoke all Sanctum tokens",
     *     tags={"User Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
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
    public function logout(Request $request)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Delete all tokens for the user
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ], Response::HTTP_OK);
    }
}

