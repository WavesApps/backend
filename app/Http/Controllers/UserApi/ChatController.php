<?php

namespace App\Http\Controllers\UserApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\UserGoogle;
use App\Models\Superstar;

class ChatController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/user/chat/start/{superstarId}",
     *     summary="Start chat with superstar",
     *     description="Find or create conversation with a superstar",
     *     tags={"User Chat"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="superstarId",
     *         in="path",
     *         description="Superstar ID to start chat with",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Chat started successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Chat started successfully"),
     *             @OA\Property(property="conversation", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="superstar_id", type="integer", example=3),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z")
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
    public function startChat($superstarId)
    {
        $user = auth()->user();
        
        // Find or create conversation
        $conversation = Conversation::firstOrCreate(
            [
                'user_google_id' => $user->id,
                'superstar_id' => $superstarId
            ],
            [
                'status' => 'active',
                'started_at' => now()
            ]
        );
        
        return response()->json([
            'conversation_id' => $conversation->id,
            'status' => $conversation->status
        ]);
    }
    
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required_without:file|string',
            'message_type' => 'required|in:text,image,video,file',
            'file' => 'nullable|file|max:10240' // 10MB max
        ]);
        
        $user = auth()->user();
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verify user is part of this conversation
        if ($conversation->user_google_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $messageData = [
            'conversation_id' => $conversationId,
            'sender_type' => 'user',
            'sender_id' => $user->id,
            'message_type' => $request->message_type,
            'message' => $request->message,
            'is_read' => false
        ];
        
        // Handle file upload if present
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
            'message' => $message->load('conversation')
        ]);
    }
    
    public function getMessages(Request $request, $conversationId)
    {
        $user = auth()->user();
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verify user is part of this conversation
        if ($conversation->user_google_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);
        
        // Get messages from most recent to oldest (for pagination)
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
        
        // Reverse the messages array to show oldest first in the chat
        $reversedMessages = $messages->getCollection()->reverse()->values();
        
        // Mark messages as read (only superstar messages)
        Message::where('conversation_id', $conversationId)
            ->where('sender_type', 'superstar')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return response()->json([
            'messages' => $reversedMessages,
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'from' => $messages->firstItem(),
                'to' => $messages->lastItem(),
                'has_more_pages' => $messages->hasMorePages()
            ]
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/user/chat/conversations",
     *     summary="Get user conversations",
     *     description="Get all conversations for the authenticated user",
     *     tags={"User Chat"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Conversations retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="conversations", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_google_id", type="integer", example=5),
     *                 @OA\Property(property="superstar_id", type="integer", example=3),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="superstar", type="object",
     *                     @OA\Property(property="id", type="integer", example=3),
     *                     @OA\Property(property="display_name", type="string", example="John Doe"),
     *                     @OA\Property(property="bio", type="string", example="Professional superstar")
     *                 ),
     *                 @OA\Property(property="last_message", type="object", nullable=true,
     *                     @OA\Property(property="message", type="string", example="Hello!"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z")
     *                 )
     *             )),
     *             @OA\Property(property="pagination", type="object",
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
    public function getConversations(Request $request)
    {
        $user = auth()->user();
        
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        $conversations = Conversation::where('user_google_id', $user->id)
            ->with(['superstar', 'messages' => function($query) {
                $query->latest()->first();
            }])
            ->orderBy('updated_at', 'desc')
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
                'has_more_pages' => $conversations->hasMorePages()
            ]
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/user/chat/unread-count",
     *     summary="Get unread message count",
     *     description="Get count of unread messages for the authenticated user",
     *     tags={"User Chat"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Unread count retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="unread_count", type="integer", example=5)
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
    public function getUnreadCount()
    {
        $user = auth()->user();
        
        $unreadCount = Message::join('conversations', 'messages.conversation_id', '=', 'conversations.id')
            ->where('conversations.user_google_id', $user->id)
            ->where('messages.sender_type', 'superstar')
            ->where('messages.is_read', false)
            ->count();
            
        return response()->json(['unread_count' => $unreadCount]);
    }
    
    public function deleteMessage(Request $request, $messageId)
    {
        $user = auth()->user();
        
        $message = Message::where('id', $messageId)
            ->where('sender_type', 'user')
            ->where('sender_id', $user->id)
            ->first();
            
        if (!$message) {
            return response()->json([
                'message' => 'Message not found or unauthorized'
            ], 404);
        }
        
        // Delete file if it's an upload
        if ($message->file_path && Storage::disk('public')->exists($message->file_path)) {
            Storage::disk('public')->delete($message->file_path);
        }
        
        $message->delete();
        
        return response()->json([
            'message' => 'Message deleted successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/chat/read/{conversationId}",
     *     summary="Mark messages as read",
     *     description="Mark all messages in conversation as read for the authenticated user",
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
     *             @OA\Property(property="message", type="string", example="Messages marked as read successfully"),
     *             @OA\Property(property="messages_marked", type="integer", example=5)
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
     *         description="Conversation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Conversation not found")
     *         )
     *     )
     * )
     */
    public function markMessagesAsRead($conversationId)
    {
        $user = auth()->user();
        
        $conversation = Conversation::where('id', $conversationId)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('superstar_id', $user->id);
            })
            ->first();
            
        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found'
            ], 404);
        }
        
        // Mark unread messages as read for the user
        $messagesMarked = Message::where('conversation_id', $conversationId)
            ->where('sender_type', '!=', 'user') // Only mark messages from other party
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read successfully',
            'messages_marked' => $messagesMarked
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/user/chat/conversation/{conversationId}/status",
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
     *             @OA\Property(property="status", type="string", enum={"active", "ended", "blocked"}, example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conversation status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Conversation status updated successfully"),
     *             @OA\Property(property="conversation", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="active")
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
     *         description="Conversation not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Conversation not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object", example={"status": {"The status field is required."}})
     *         )
     *     )
     * )
     */
    public function updateConversationStatus(Request $request, $conversationId)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,ended,blocked'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $conversation = Conversation::where('id', $conversationId)
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('superstar_id', $user->id);
            })
            ->first();
            
        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found'
            ], 404);
        }
        
        $conversation->status = $request->status;
        $conversation->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Conversation status updated successfully',
            'conversation' => [
                'id' => $conversation->id,
                'status' => $conversation->status
            ]
        ], 200);
    }
}
