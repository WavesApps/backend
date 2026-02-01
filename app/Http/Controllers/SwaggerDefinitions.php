<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Streaming App API",
 *     version="1.0.0",
 *     description="API documentation for the streaming application including user authentication, subscriptions, chat, and payment features",
 *     @OA\Contact(
 *         email="support@streamingapp.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="API Server"
 * )
 * 
 * @OA\Tag(
 *     name="User Authentication",
 *     description="User authentication and authorization endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="User Subscriptions",
 *     description="User subscription management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="SuperStar Authentication",
 *     description="SuperStar authentication and profile management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="SuperStar Posts",
 *     description="SuperStar post management endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Chat",
 *     description="Chat and messaging endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="Payments",
 *     description="Payment processing and history endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="SuperStars",
 *     description="SuperStar browsing and details endpoints"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     description="Enter token in format: Bearer {token}",
 *     name="Authorization",
 *     in="header",
 *     bearerFormat="JWT",
 *     scheme="bearer"
 * )
 * 
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operation successful"),
 *     @OA\Property(property="data", type="object", nullable=true)
 * )
 * 
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Error occurred"),
 *     @OA\Property(property="errors", type="object", nullable=true)
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="username", type="string", example="johndoe"),
 *     @OA\Property(property="image", type="string", nullable=true, example="https://example.com/avatar.jpg")
 * )
 * 
 * @OA\Schema(
 *     schema="SuperStar",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=5),
 *     @OA\Property(property="display_name", type="string", example="John Doe"),
 *     @OA\Property(property="bio", type="string", example="Professional streamer"),
 *     @OA\Property(property="price_per_hour", type="number", format="float", example=0.50),
 *     @OA\Property(property="is_available", type="boolean", example=true),
 *     @OA\Property(property="rating", type="number", format="float", example=4.5),
 *     @OA\Property(property="total_followers", type="integer", example=1500),
 *     @OA\Property(property="status", type="string", example="active")
 * )
 * 
 * @OA\Schema(
 *     schema="Pagination",
 *     type="object",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="last_page", type="integer", example=5),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=75)
 * )
 */
class SwaggerDefinitions
{
    // This class contains only Swagger annotations
}
