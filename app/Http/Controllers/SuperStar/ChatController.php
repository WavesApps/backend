<?php

namespace App\Http\Controllers\SuperStar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\UserGoogle;

class ChatController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/superstar/conversations",
     *     summary="Get superstar conversations",
     *     description="Get all conversations for the authenticated superstar with pagination",
     *     tags={"Superstar Chat"},
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
     *             @OA\Property(property="conversations", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="superstar_id", type="integer", example=2),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="username", type="string", example="johndoe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com")
     *                 )
     *             )),
     *             @OA\Property(property="pagination", type="object",
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
        $superstar = $request->user();
        
        // Pagination parameters
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        
        // Filter parameters
        $status = $request->get('status');
        
        $query = Conversation::where('superstar_id', $superstar->id)
            ->with(['user', 'messages' => function($query) {
                $query->latest()->first();
            }]);
        
        // Apply status filter
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
                'has_more_pages' => $conversations->hasMorePages()
            ]
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/superstar/conversations/{conversationId}/messages",
     *     summary="Get conversation messages",
     *     description="Get all messages in a specific conversation for the authenticated superstar",
     *     tags={"Superstar Chat"},
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
     *             @OA\Property(property="messages", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="conversation_id", type="integer", example=1),
     *                 @OA\Property(property="sender_type", type="string", example="user"),
     *                 @OA\Property(property="sender_id", type="integer", example=5),
     *                 @OA\Property(property="message_type", type="string", example="text"),
     *                 @OA\Property(property="message", type="string", example="Hello!"),
     *                 @OA\Property(property="file_path", type="string", nullable=true),
     *                 @OA\Property(property="file_name", type="string", nullable=true),
     *                 @OA\Property(property="file_size", type="integer", nullable=true),
     *                 @OA\Property(property="is_read", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=20),
     *                 @OA\Property(property="has_more_pages", type="boolean", example=true)
     *             )
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
        $superstar = $request->user();
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verify superstar is part of this conversation
        if ($conversation->superstar_id !== $superstar->id) {
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
     * @OA\Post(
     *     path="/api/superstar/conversations/{conversationId}/messages",
     *     summary="Send message to conversation",
     *     description="Send a text message or file to a conversation",
     *     tags={"Superstar Chat"},
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
     *             @OA\Property(property="message", type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="conversation_id", type="integer", example=1),
     *                 @OA\Property(property="sender_type", type="string", example="superstar"),
     *                 @OA\Property(property="sender_id", type="integer", example=2),
     *                 @OA\Property(property="message_type", type="string", example="text"),
     *                 @OA\Property(property="message", type="string", example="Hello there!"),
     *                 @OA\Property(property="file_path", type="string", nullable=true),
     *                 @OA\Property(property="file_name", type="string", nullable=true),
     *                 @OA\Property(property="file_size", type="integer", nullable=true),
     *                 @OA\Property(property="is_read", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="conversation", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="superstar_id", type="integer", example=2)
     *                 )
     *             )
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
     *             @OA\Property(property="errors", type="object", example={"message_type": {"The message type field is required."}})
     *         )
     *     )
     * )
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required_without:file|string',
            'message_type' => 'required|in:text,image,video,file',
            'file' => 'nullable|file|max:10240' // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $superstar = $request->user();
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verify superstar is part of this conversation
        if ($conversation->superstar_id !== $superstar->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $messageData = [
            'conversation_id' => $conversationId,
            'sender_type' => 'superstar',
            'sender_id' => $superstar->id,
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
    
    /**
     * @OA\Post(
     *     path="/api/superstar/conversations/{conversationId}/read",
     *     summary="Mark messages as read",
     *     description="Mark all user messages in a conversation as read",
     *     tags={"Superstar Chat"},
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
        $superstar = $request->user();
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verify superstar is part of this conversation
        if ($conversation->superstar_id !== $superstar->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Mark user messages as read
        $updatedCount = Message::where('conversation_id', $conversationId)
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return response()->json([
            'message' => 'Messages marked as read',
            'messages_marked' => $updatedCount
        ]);
    }
    
    /**
     * @OA\Get(
     *     path="/api/superstar/unread-count",
     *     summary="Get unread messages count",
     *     description="Get total count of unread messages from all conversations",
     *     tags={"Superstar Chat"},
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
        $superstar = $request->user();
        
        $unreadCount = Message::join('conversations', 'messages.conversation_id', '=', 'conversations.id')
            ->where('conversations.superstar_id', $superstar->id)
            ->where('messages.sender_type', 'user')
            ->where('messages.is_read', false)
            ->count();
            
        return response()->json(['unread_count' => $unreadCount]);
    }
    
    /**
     * @OA\Put(
     *     path="/api/superstar/conversations/{conversationId}/status",
     *     summary="Update conversation status",
     *     description="Update the status of a conversation (active, ended, blocked)",
     *     tags={"Superstar Chat"},
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
     *             @OA\Property(property="conversation", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="superstar_id", type="integer", example=2),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=5),
     *                     @OA\Property(property="username", type="string", example="johndoe")
     *                 )
     *             )
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
     *             @OA\Property(property="errors", type="object", example={"status": {"The status field is required."}})
     *         )
     *     )
     * )
     */
    public function updateConversationStatus(Request $request, $conversationId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,ended,blocked'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $superstar = $request->user();
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verify superstar is part of this conversation
        if ($conversation->superstar_id !== $superstar->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $updateData = [
            'status' => $request->status
        ];
        
        // Set timestamps based on status
        if ($request->status === 'active' && !$conversation->started_at) {
            $updateData['started_at'] = now();
        } elseif ($request->status === 'ended') {
            $updateData['ended_at'] = now();
        }
        
        $conversation->update($updateData);
        
        return response()->json([
            'message' => 'Conversation status updated successfully',
            'conversation' => $conversation->load('user')
        ]);
    }
    
    /**
     * @OA\Delete(
     *     path="/api/superstar/messages/{messageId}",
     *     summary="Delete message",
     *     description="Delete a message sent by the superstar",
     *     tags={"Superstar Chat"},
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
        $superstar = $request->user();
        
        $message = Message::where('id', $messageId)
            ->where('sender_type', 'superstar')
            ->where('sender_id', $superstar->id)
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
}
