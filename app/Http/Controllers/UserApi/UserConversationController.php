<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\UserGoogle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

class UserConversationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/conversations",
     *     summary="Get user conversations",
     *     description="Get all conversations for the authenticated user with pagination",
     *     tags={"User Chat"},
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
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by conversation status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "ended", "blocked"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conversations retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="conversations", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="pagination", type="object")
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
    public function getConversations(Request $request)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 15);
        $status = $request->get('status');

        $query = Conversation::where('user_google_id', $user->id)
            ->with(['superstar', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }]);

        if ($status) {
            $query->where('status', $status);
        }

        $conversations = $query->orderBy('updated_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'conversations' => $conversations->items(),
            'pagination' => [
                'current_page' => $conversations->currentPage(),
                'last_page' => $conversations->lastPage(),
                'per_page' => $conversations->perPage(),
                'total' => $conversations->total(),
                'from' => $conversations->firstItem(),
                'to' => $conversations->lastItem(),
                'has_more_pages' => $conversations->hasMorePages(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/conversations/{conversationId}/messages",
     *     summary="Get conversation messages",
     *     description="Get all messages in a specific conversation for the authenticated user",
     *     tags={"User Chat"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="conversationId",
     *         in="path",
     *         description="Conversation ID",
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
     *         description="Messages per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Messages retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="messages", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="pagination", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conversation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Conversation not found")
     *         )
     *     )
     * )
     */
    public function getMessages(Request $request, $conversationId)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->user_google_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $page = (int) $request->get('page', 1);
        $perPage = (int) $request->get('per_page', 20);

        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $reversedMessages = $messages->getCollection()->reverse()->values();

        return response()->json([
            'messages' => $reversedMessages,
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'from' => $messages->firstItem(),
                'to' => $messages->lastItem(),
                'has_more_pages' => $messages->hasMorePages(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/conversations/{conversationId}/messages",
     *     summary="Send message to conversation",
     *     description="Send a text message or file to a conversation",
     *     tags={"User Chat"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="conversationId",
     *         in="path",
     *         description="Conversation ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"message_type"},
     *             @OA\Property(property="message", type="string", nullable=true, example="Hello there!"),
     *             @OA\Property(property="message_type", type="string", enum={"text","image","video","file"}, example="text"),
     *             @OA\Property(property="file", type="string", format="binary", nullable=true, description="File upload (max 10MB)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required_without:file|string',
            'message_type' => 'required|in:text,image,video,file',
            'file' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->user_google_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messageData = [
            'conversation_id' => $conversationId,
            'sender_type' => 'user',
            'sender_id' => $user->id,
            'message_type' => $request->message_type,
            'message' => $request->message,
            'is_read' => false,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('chat_files', $fileName, 'public');

            $messageData['file_path'] = $filePath;
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_size'] = $file->getSize();
        }

        $message = Message::create($messageData);

        return response()->json([
            'message' => $message->load('conversation'),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/conversations/{conversationId}/read",
     *     summary="Mark messages as read",
     *     description="Mark all superstar messages in a conversation as read",
     *     tags={"User Chat"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="conversationId",
     *         in="path",
     *         description="Conversation ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Messages marked as read successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Messages marked as read"),
     *             @OA\Property(property="messages_marked", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function markMessagesAsRead(Request $request, $conversationId)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->user_google_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $updatedCount = Message::where('conversation_id', $conversationId)
            ->where('sender_type', 'superstar')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'message' => 'Messages marked as read',
            'messages_marked' => $updatedCount,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/user/unread-count",
     *     summary="Get unread messages count",
     *     description="Get total count of unread messages from all conversations",
     *     tags={"User Chat"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Unread count retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="unread_count", type="integer", example=12)
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
    public function getUnreadCount(Request $request)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $unreadCount = Message::join('conversations', 'messages.conversation_id', '=', 'conversations.id')
            ->where('conversations.user_google_id', $user->id)
            ->where('messages.sender_type', 'superstar')
            ->where('messages.is_read', false)
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * @OA\Put(
     *     path="/api/user/conversations/{conversationId}/status",
     *     summary="Update conversation status",
     *     description="Update the status of a conversation (active, ended, blocked)",
     *     tags={"User Chat"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="conversationId",
     *         in="path",
     *         description="Conversation ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"active","ended","blocked"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conversation status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Conversation status updated successfully"),
     *             @OA\Property(property="conversation", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function updateConversationStatus(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,ended,blocked',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->user_google_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $updateData = [
            'status' => $request->status,
        ];

        if ($request->status === 'active' && !$conversation->started_at) {
            $updateData['started_at'] = now();
        } elseif ($request->status === 'ended') {
            $updateData['ended_at'] = now();
        }

        $conversation->update($updateData);

        return response()->json([
            'message' => 'Conversation status updated successfully',
            'conversation' => $conversation->load('superstar'),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/user/messages/{messageId}",
     *     summary="Delete message",
     *     description="Delete a message sent by the authenticated user",
     *     tags={"User Chat"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="messageId",
     *         in="path",
     *         description="Message ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Message deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Message not found or unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Message not found or unauthorized")
     *         )
     *     )
     * )
     */
    public function deleteMessage(Request $request, $messageId)
    {
        /** @var UserGoogle|null $user */
        $user = auth('sanctum')->user();

        if (!$user || !($user instanceof UserGoogle)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $message = Message::where('id', $messageId)
            ->where('sender_type', 'user')
            ->where('sender_id', $user->id)
            ->first();

        if (!$message) {
            return response()->json([
                'message' => 'Message not found or unauthorized',
            ], 404);
        }

        if ($message->file_path && Storage::disk('public')->exists($message->file_path)) {
            Storage::disk('public')->delete($message->file_path);
        }

        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully',
        ]);
    }
}
