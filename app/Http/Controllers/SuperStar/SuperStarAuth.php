<?php

namespace App\Http\Controllers\SuperStar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Superstar;
use App\Models\User;

class SuperStarAuth extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/superstar/login",
     *     summary="Superstar Login",
     *     description="Login endpoint for superstars using email or username",
     *     tags={"Superstar Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"login","password"},
     *             @OA\Property(property="login", type="string", description="Email or username", example="superstar@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="superstar", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="display_name", type="string", example="John Doe"),
     *                 @OA\Property(property="bio", type="string", example="Professional superstar"),
     *                 @OA\Property(property="price_per_hour", type="number", format="float", example=50.00),
     *                 @OA\Property(property="is_available", type="boolean", example=true),
     *                 @OA\Property(property="rating", type="number", format="float", example=4.5),
     *                 @OA\Property(property="total_followers", type="integer", example=1500),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="email", type="string", format="email", example="superstar@example.com"),
     *                 @OA\Property(property="role", type="string", example="superstar"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     *             ),
     *             @OA\Property(property="token", type="string", example="1|abc123token456"),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Admin access forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Please use admin app for admin access")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object", example={"login": {"The login field is required."}})
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // Can be email or username
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $loginField = $request->input('login');
        $password = $request->input('password');

        // Check if login field is email or username
        $loginType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find user by email or username
        $user = User::where($loginType, $loginField)->first();

        if (!$user || $user->role !== 'superstar') {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Check role - if admin, return error
        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Please use the admin app for admin access'
            ], 403);
        }

        // Verify password
        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Ensure superstar profile exists
        $superstar = $user->superstar;
        if (!$superstar) {
            return response()->json([
                'message' => 'Superstar profile not found'
            ], 404);
        }

        // Revoke existing tokens if any
        $user->tokens()->delete();

        // Create new sanctum token
        $token = $user->createToken('superstar-auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'superstar' => [
                'id' => $superstar->id,
                'user_id' => $superstar->user_id,
                'display_name' => $superstar->display_name,
                'bio' => $superstar->bio,
                'price_per_hour' => $superstar->price_per_hour,
                'is_available' => $superstar->is_available,
                'rating' => $superstar->rating,
                'total_followers' => $superstar->total_followers,
                'status' => $superstar->status,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/superstar/logout",
     *     summary="Logout superstar",
     *     description="Logout of authenticated superstar and revoke token",
     *     tags={"Superstar Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
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
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/superstar/me",
     *     summary="Get authenticated superstar",
     *     description="Get authenticated superstar's profile information",
     *     tags={"Superstar Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="superstar", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="display_name", type="string", example="John Doe"),
     *                 @OA\Property(property="bio", type="string", example="Professional superstar"),
     *                 @OA\Property(property="price_per_hour", type="number", format="float", example=50.00),
     *                 @OA\Property(property="is_available", type="boolean", example=true),
     *                 @OA\Property(property="rating", type="number", format="float", example=4.5),
     *                 @OA\Property(property="total_followers", type="integer", example=1500),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="email", type="string", format="email", example="superstar@example.com"),
     *                 @OA\Property(property="profile_image", type="string", nullable=true, example="images/superstars/profile.jpg"),
     *                 @OA\Property(property="role", type="string", example="superstar"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-20T15:45:00Z")
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
    public function me(Request $request)
    {
        $user = $request->user();
        $superstar = $user->superstar;

        if (!$superstar) {
            return response()->json([
                'message' => 'Superstar profile not found'
            ], 404);
        }

        return response()->json([
            'superstar' => [
                'id' => $superstar->id,
                'user_id' => $superstar->user_id,
                'display_name' => $superstar->display_name,
                'bio' => $superstar->bio,
                'price_per_hour' => $superstar->price_per_hour,
                'is_available' => $superstar->is_available,
                'rating' => $superstar->rating,
                'total_followers' => $superstar->total_followers,
                'status' => $superstar->status,
                'username' => $user->username,
                'email' => $user->email,
                'profile_image' => $user->profile_image,
                'role' => $user->role,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/superstar/profile",
     *     summary="Update superstar profile",
     *     description="Update the authenticated superstar's profile information",
     *     tags={"Superstar Profile"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="display_name", type="string", example="John Doe"),
     *             @OA\Property(property="bio", type="string", example="Professional superstar"),
     *             @OA\Property(property="is_available", type="boolean", example=true),
     *             @OA\Property(property="username", type="string", example="johndoe"),
     *             @OA\Property(property="profile_image", type="string", format="binary", description="Profile image file (jpeg, png, jpg, gif, max 2MB)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="superstar", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
  public function updateProfile(Request $request)
{
    $validator = Validator::make($request->all(), [
        'display_name'  => 'sometimes|string|max:255',
        'bio'           => 'sometimes|string|max:1000',
        'is_available'  => 'sometimes|boolean',
        'username'      => 'sometimes|string|max:255|unique:users,username,' . $request->user()->id,
        'profile_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors'  => $validator->errors(),
        ], 422);
    }

    $user = $request->user();
    $superstar = $user->superstar;

    if (!$superstar) {
        return response()->json([
            'message' => 'Superstar profile not found'
        ], 404);
    }

    // Update superstar fields
    $superstar->update($request->only([
        'display_name',
        'bio',
        'is_available',
    ]));

    // Update user fields
    if ($request->has('username')) {
        $user->username = $request->username;
    }

    // Handle image upload
    if ($request->hasFile('profile_image')) {
        $image = $request->file('profile_image');
        $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images/superstars'), $imageName);

        $user->profile_image = 'images/superstars/' . $imageName;
    }

    $user->save();

    return response()->json([
        'message' => 'Profile updated successfully',
        'superstar' => [
            'id' => $superstar->id,
            'display_name' => $superstar->display_name,
            'bio' => $superstar->bio,
            'is_available' => $superstar->is_available,
            'username' => $user->username,
            'email' => $user->email,
            'profile_image' => $user->profile_image,
        ]
    ]);
}

    /**
     * @OA\Post(
     *     path="/api/superstar/change-password",
     *     summary="Change superstar password",
     *     description="Change authenticated superstar's password",
     *     tags={"Superstar Authentication"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password","new_password"},
     *             @OA\Property(property="current_password", type="string", format="password", example="oldpassword123"),
     *             @OA\Property(property="new_password", type="string", format="password", minLength=8, example="newpassword123"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password changed successfully")
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
     *             @OA\Property(property="errors", type="object", example={"current_password": {"The current password field is required."}})
     *         )
     *     )
     * )
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);
        
        // Revoke all existing tokens (force logout from all devices)
        $user->tokens()->delete();
        
        return response()->json([
            'message' => 'Password changed successfully. Please login again.'
        ]);
    }
}
