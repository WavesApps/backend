<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\UserGoogle;
use App\Models\Superstar;

class ChatController extends Controller
{
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
    
    public function getMessages($conversationId)
    {
        $user = auth()->user();
        $conversation = Conversation::findOrFail($conversationId);
        
        // Verify user is part of this conversation
        if ($conversation->user_google_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Mark messages as read
        Message::where('conversation_id', $conversationId)
            ->where('sender_type', 'superstar')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return response()->json(['messages' => $messages]);
    }
    
    public function getConversations()
    {
        $user = auth()->user();
        
        $conversations = Conversation::where('user_google_id', $user->id)
            ->with(['superstar', 'messages' => function($query) {
                $query->latest()->first();
            }])
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return response()->json(['conversations' => $conversations]);
    }
}
