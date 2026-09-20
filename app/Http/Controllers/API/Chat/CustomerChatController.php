<?php

namespace App\Http\Controllers\API\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerChatController extends Controller
{
    protected FirebaseNotificationService $firebase;

    public function __construct(FirebaseNotificationService $firebase)
    {
        $this->firebase = $firebase;
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/chat/conversations
    | Create or return existing open conversation for a user.
    |--------------------------------------------------------------------------
    */
    public function createConversation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $userId = $request->user_id;

        // Return existing open conversation or create a new one
        $conversation = Conversation::firstOrCreate(
            ['user_id' => $userId, 'status' => 'open'],
            ['user_id' => $userId, 'status' => 'open']
        );

        $conversation->load(['user', 'lastMessage']);

        return response()->json([
            'message'      => 'تمت العملية بنجاح',
            'conversation' => new ConversationResource($conversation, 'user'),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/chat/conversations?user_id=123
    | List all conversations for a user.
    |--------------------------------------------------------------------------
    */
    public function listConversations(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversations = Conversation::with(['user', 'lastMessage'])
            ->where('user_id', $request->user_id)
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'conversations' => ConversationResource::collection(
                $conversations->map(fn($c) => tap($c, fn($c) => $c->role = 'user'))
            )->map(fn($r) => (new ConversationResource($r->resource, 'user'))->toArray($request)),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/chat/conversations/{conversation}?user_id=123
    | Show a single conversation (IDOR-protected).
    |--------------------------------------------------------------------------
    */
    public function showConversation(Request $request, int $conversationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = Conversation::with(['user', 'lastMessage'])
            ->where('id', $conversationId)
            ->where('user_id', $request->user_id) // IDOR protection
            ->first();

        if (!$conversation) {
            return response()->json(['message' => 'المحادثة غير موجودة أو لا تخصك'], 404);
        }

        return response()->json([
            'conversation' => new ConversationResource($conversation, 'user'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/chat/conversations/{conversation}/messages?user_id=123
    | Paginated messages list.
    |--------------------------------------------------------------------------
    */
    public function getMessages(Request $request, int $conversationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required|integer|exists:users,id',
            'page'     => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = Conversation::where('id', $conversationId)
            ->where('user_id', $request->user_id) // IDOR protection
            ->first();

        if (!$conversation) {
            return response()->json(['message' => 'المحادثة غير موجودة أو لا تخصك'], 404);
        }

        $perPage = $request->input('per_page', 30);

        $messages = Message::where('conversation_id', $conversationId)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'messages'   => MessageResource::collection($messages->items()),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page'    => $messages->lastPage(),
                'per_page'     => $messages->perPage(),
                'total'        => $messages->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/chat/conversations/{conversation}/messages
    | Send a message as a user.
    |--------------------------------------------------------------------------
    */
    public function sendMessage(Request $request, int $conversationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id'    => 'required|integer|exists:users,id',
            'message'    => 'nullable|string|max:5000',
            'type'       => 'nullable|in:text,image,pdf,file',
            'attachment' => 'nullable|file|max:20480', // 20MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // IDOR protection
        $conversation = Conversation::where('id', $conversationId)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$conversation) {
            return response()->json(['message' => 'المحادثة غير موجودة أو لا تخصك'], 404);
        }

        if ($conversation->status === 'closed') {
            return response()->json(['message' => 'المحادثة مغلقة ولا يمكن إرسال رسائل جديدة'], 403);
        }

        // Validate that there's at least a message or attachment
        if (empty($request->message) && !$request->hasFile('attachment')) {
            return response()->json(['message' => 'يجب إرسال نص أو مرفق'], 422);
        }

        $messageData = [
            'conversation_id' => $conversationId,
            'sender_id'       => $request->user_id,
            'sender_type'     => 'user',
            'message'         => $request->message,
            'type'            => $request->input('type', 'text'),
        ];

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("chat/attachments/{$conversationId}", 'local');

            $messageData['attachment_path'] = $path;
            $messageData['attachment_name'] = $file->getClientOriginalName();
            $messageData['attachment_mime'] = $file->getMimeType();
            $messageData['attachment_size'] = $file->getSize();

            // Auto-detect type from MIME if not provided
            if ($request->input('type', 'text') === 'text') {
                $mime = $file->getMimeType();
                if (str_starts_with($mime, 'image/')) {
                    $messageData['type'] = 'image';
                } elseif ($mime === 'application/pdf') {
                    $messageData['type'] = 'pdf';
                } else {
                    $messageData['type'] = 'file';
                }
            }
        }

        $message = Message::create($messageData);

        // Update conversation's last message
        $conversation->update([
            'last_message_id' => $message->id,
            'last_message_at' => $message->created_at,
        ]);

        // Send Firebase notification to admin users with device tokens
        $this->notifyAdmins($request->user_id, $conversation, $message);

        return response()->json([
            'message_data' => new MessageResource($message),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/chat/conversations/{conversation}/read
    | Mark all admin messages as read (user reads admin's messages).
    |--------------------------------------------------------------------------
    */
    public function markAsRead(Request $request, int $conversationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = Conversation::where('id', $conversationId)
            ->where('user_id', $request->user_id) // IDOR protection
            ->first();

        if (!$conversation) {
            return response()->json(['message' => 'المحادثة غير موجودة أو لا تخصك'], 404);
        }

        // Mark admin's unread messages as read
        $count = Message::where('conversation_id', $conversationId)
            ->where('sender_type', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message'       => 'تم تحديد الرسائل كمقروءة',
            'marked_count'  => $count,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/chat/conversations/{conversation}/close
    | Close a conversation (by user).
    |--------------------------------------------------------------------------
    */
    public function closeConversation(Request $request, int $conversationId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = Conversation::where('id', $conversationId)
            ->where('user_id', $request->user_id) // IDOR protection
            ->first();

        if (!$conversation) {
            return response()->json(['message' => 'المحادثة غير موجودة أو لا تخصك'], 404);
        }

        if ($conversation->status === 'closed') {
            return response()->json(['message' => 'المحادثة مغلقة بالفعل'], 400);
        }

        $conversation->update(['status' => 'closed']);

        return response()->json(['message' => 'تم إغلاق المحادثة بنجاح']);
    }

    /*
    |--------------------------------------------------------------------------
    | Private: Notify Admins via Firebase
    |--------------------------------------------------------------------------
    */
    private function notifyAdmins(int $userId, Conversation $conversation, Message $message): void
    {
        try {
            $user = User::find($userId);
            $userName = $user?->name ?? 'مستخدم';

            // Get admin users who have device tokens
            $adminUsers = User::role('Admin')->whereNotNull('device_token')->get();

            $notifTitle = 'رسالة جديدة من ' . $userName;
            $notifBody  = $message->type === 'text'
                ? ($message->message ?? 'رسالة')
                : 'أرسل مرفقاً';

            $data = [
                'type'            => 'new_chat_message',
                'conversation_id' => (string) $conversation->id,
                'message_id'      => (string) $message->id,
                'sender_type'     => 'user',
            ];

            foreach ($adminUsers as $admin) {
                $this->firebase->sendNotificationToDevice(
                    $admin->device_token,
                    $notifTitle,
                    $notifBody,
                    $data
                );
            }
        } catch (\Exception $e) {
            Log::warning('Chat: Failed to notify admins: ' . $e->getMessage());
        }
    }
}
