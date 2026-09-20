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
use Illuminate\Support\Facades\Validator;

class AdminChatController extends Controller
{
    protected FirebaseNotificationService $firebase;

    public function __construct(FirebaseNotificationService $firebase)
    {
        $this->firebase = $firebase;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Admin Identity
    | Checks that admin_id belongs to a real user with 'Admin' role.
    |--------------------------------------------------------------------------
    */
    private function validateAdmin(Request $request): ?JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $admin = User::find($request->admin_id);

        if (!$admin || !$admin->hasRole('Admin')) {
            return response()->json(['message' => 'غير مصرح لك بالوصول إلى هذه الخدمة'], 403);
        }

        return null; // null means validation passed
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/admin/chat/conversations
    | List all conversations (paginated) with filters.
    |--------------------------------------------------------------------------
    */
    public function listConversations(Request $request): JsonResponse
    {
        $authError = $this->validateAdmin($request);
        if ($authError) return $authError;

        $validator = Validator::make($request->all(), [
            'status'   => 'nullable|in:open,closed',
            'page'     => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'search'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = Conversation::with(['user', 'lastMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $perPage       = $request->input('per_page', 20);
        $conversations = $query->paginate($perPage);

        $items = collect($conversations->items())->map(
            fn($c) => (new ConversationResource($c, 'admin'))->toArray($request)
        );

        return response()->json([
            'conversations' => $items,
            'pagination'    => [
                'current_page' => $conversations->currentPage(),
                'last_page'    => $conversations->lastPage(),
                'per_page'     => $conversations->perPage(),
                'total'        => $conversations->total(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/admin/chat/conversations/{conversation}
    | Show a single conversation with details.
    |--------------------------------------------------------------------------
    */
    public function showConversation(Request $request, int $conversationId): JsonResponse
    {
        $authError = $this->validateAdmin($request);
        if ($authError) return $authError;

        $conversation = Conversation::with(['user', 'lastMessage'])->find($conversationId);

        if (!$conversation) {
            return response()->json(['message' => 'المحادثة غير موجودة'], 404);
        }

        return response()->json([
            'conversation' => new ConversationResource($conversation, 'admin'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/admin/chat/conversations/{conversation}/messages
    | Paginated messages list.
    |--------------------------------------------------------------------------
    */
    public function getMessages(Request $request, int $conversationId): JsonResponse
    {
        $authError = $this->validateAdmin($request);
        if ($authError) return $authError;

        $validator = Validator::make($request->all(), [
            'page'     => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return response()->json(['message' => 'المحادثة غير موجودة'], 404);
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
    | POST /api/admin/chat/conversations/{conversation}/messages
    | Send a message as an admin.
    |--------------------------------------------------------------------------
    */
    public function sendMessage(Request $request, int $conversationId): JsonResponse
    {
        $authError = $this->validateAdmin($request);
        if ($authError) return $authError;

        $validator = Validator::make($request->all(), [
            'message'    => 'nullable|string|max:5000',
            'type'       => 'nullable|in:text,image,pdf,file',
            'attachment' => 'nullable|file|max:20480', // 20MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return response()->json(['message' => 'المحادثة غير موجودة'], 404);
        }

        if ($conversation->status === 'closed') {
            return response()->json(['message' => 'المحادثة مغلقة ولا يمكن إرسال رسائل جديدة'], 403);
        }

        if (empty($request->message) && !$request->hasFile('attachment')) {
            return response()->json(['message' => 'يجب إرسال نص أو مرفق'], 422);
        }

        $messageData = [
            'conversation_id' => $conversationId,
            'sender_id'       => $request->admin_id,
            'sender_type'     => 'admin',
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

            // Auto-detect type from MIME
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

        // Send Firebase notification to the user
        $this->notifyUser($conversation, $message);

        return response()->json([
            'message_data' => new MessageResource($message),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/admin/chat/conversations/{conversation}/read
    | Mark all user messages as read (admin reads user's messages).
    |--------------------------------------------------------------------------
    */
    public function markAsRead(Request $request, int $conversationId): JsonResponse
    {
        $authError = $this->validateAdmin($request);
        if ($authError) return $authError;

        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return response()->json(['message' => 'المحادثة غير موجودة'], 404);
        }

        // Mark user's unread messages as read
        $count = Message::where('conversation_id', $conversationId)
            ->where('sender_type', 'user')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message'      => 'تم تحديد الرسائل كمقروءة',
            'marked_count' => $count,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/admin/chat/conversations/{conversation}/close
    | Close a conversation (by admin).
    |--------------------------------------------------------------------------
    */
    public function closeConversation(Request $request, int $conversationId): JsonResponse
    {
        $authError = $this->validateAdmin($request);
        if ($authError) return $authError;

        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return response()->json(['message' => 'المحادثة غير موجودة'], 404);
        }

        if ($conversation->status === 'closed') {
            return response()->json(['message' => 'المحادثة مغلقة بالفعل'], 400);
        }

        $conversation->update(['status' => 'closed']);

        // Optionally notify user that admin closed the conversation
        $this->notifyUserConversationClosed($conversation);

        return response()->json(['message' => 'تم إغلاق المحادثة بنجاح']);
    }

    /*
    |--------------------------------------------------------------------------
    | Private: Notify User via Firebase
    |--------------------------------------------------------------------------
    */
    private function notifyUser(Conversation $conversation, Message $message): void
    {
        try {
            $user = User::find($conversation->user_id);

            if (!$user || empty($user->device_token)) {
                return;
            }

            $notifTitle = 'رسالة جديدة من الدعم';
            $notifBody  = $message->type === 'text'
                ? ($message->message ?? 'رسالة')
                : 'أرسل مرفقاً';

            $data = [
                'type'            => 'new_chat_message',
                'conversation_id' => (string) $conversation->id,
                'message_id'      => (string) $message->id,
                'sender_type'     => 'admin',
            ];

            $this->firebase->sendNotificationToDevice(
                $user->device_token,
                $notifTitle,
                $notifBody,
                $data
            );
        } catch (\Exception $e) {
            Log::warning('Chat: Failed to notify user: ' . $e->getMessage());
        }
    }

    private function notifyUserConversationClosed(Conversation $conversation): void
    {
        try {
            $user = User::find($conversation->user_id);

            if (!$user || empty($user->device_token)) {
                return;
            }

            $this->firebase->sendNotificationToDevice(
                $user->device_token,
                'تم إغلاق المحادثة',
                'قام فريق الدعم بإغلاق المحادثة.',
                [
                    'type'            => 'conversation_closed',
                    'conversation_id' => (string) $conversation->id,
                ]
            );
        } catch (\Exception $e) {
            Log::warning('Chat: Failed to notify user of closure: ' . $e->getMessage());
        }
    }
}
