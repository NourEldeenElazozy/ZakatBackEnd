<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | GET /chat — عرض صفحة المحادثات
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $status = $request->input('status', 'open');
        $search = $request->input('search');

        $query = Conversation::with(['user', 'lastMessage'])
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('user', fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
            );
        }

        $conversations = $query->paginate(20)->withQueryString();

        // تحميل المحادثة المحددة إذا كان هناك id في الـ URL
        $activeConversation = null;
        $messages = collect();

        if ($request->has('conversation')) {
            $activeConversation = Conversation::with('user')
                ->find($request->conversation);

            if ($activeConversation) {
                $messages = Message::where('conversation_id', $activeConversation->id)
                    ->orderBy('created_at')
                    ->get();

                // تحديد رسائل المستخدم كمقروءة تلقائياً عند فتح المحادثة
                Message::where('conversation_id', $activeConversation->id)
                    ->where('sender_type', 'user')
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
        }

        // إجمالي الرسائل غير المقروءة من المستخدمين
        $totalUnread = Message::where('sender_type', 'user')
            ->whereNull('read_at')
            ->count();

        return view('chat.index', compact(
            'conversations',
            'activeConversation',
            'messages',
            'status',
            'search',
            'totalUnread'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | POST /chat/{conversation}/reply — إرسال رد من الأدمن
    |--------------------------------------------------------------------------
    */
    public function reply(Request $request, int $conversationId)
    {
        $request->validate([
            'message'    => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:20480',
        ]);

        if (empty($request->message) && !$request->hasFile('attachment')) {
            return back()->with('error', 'يجب إدخال رسالة أو مرفق');
        }

        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->status === 'closed') {
            return back()->with('error', 'المحادثة مغلقة');
        }

        $messageData = [
            'conversation_id' => $conversationId,
            'sender_id'       => Auth::id(),
            'sender_type'     => 'admin',
            'message'         => $request->message,
            'type'            => 'text',
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("chat/attachments/{$conversationId}", 'local');
            $messageData['attachment_path'] = $path;
            $messageData['attachment_name'] = $file->getClientOriginalName();
            $messageData['attachment_mime'] = $file->getMimeType();
            $messageData['attachment_size'] = $file->getSize();

            $mime = $file->getMimeType();
            $messageData['type'] = str_starts_with($mime, 'image/') ? 'image'
                : ($mime === 'application/pdf' ? 'pdf' : 'file');
        }

        $message = Message::create($messageData);

        $conversation->update([
            'last_message_id' => $message->id,
            'last_message_at' => $message->created_at,
        ]);

        // Firebase notification to user
        $this->notifyUser($conversation, $message);

        return redirect()->route('chat.index', [
            'conversation' => $conversationId,
            'status'       => $conversation->status,
        ])->with('success', 'تم إرسال الرد بنجاح');
    }

    /*
    |--------------------------------------------------------------------------
    | POST /chat/{conversation}/close — إغلاق محادثة
    |--------------------------------------------------------------------------
    */
    public function close(int $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        if ($conversation->status === 'open') {
            $conversation->update(['status' => 'closed']);
            $this->notifyUserClosed($conversation);
        }

        return redirect()->route('chat.index', ['status' => 'open'])
            ->with('success', 'تم إغلاق المحادثة');
    }

    /*
    |--------------------------------------------------------------------------
    | POST /chat/{conversation}/reopen — إعادة فتح محادثة
    |--------------------------------------------------------------------------
    */
    public function reopen(int $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $conversation->update(['status' => 'open']);

        return redirect()->route('chat.index', [
            'conversation' => $conversationId,
            'status'       => 'open',
        ])->with('success', 'تم إعادة فتح المحادثة');
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helpers
    |--------------------------------------------------------------------------
    */
    private function notifyUser(Conversation $conversation, Message $message): void
    {
        try {
            $user = User::find($conversation->user_id);
            if (!$user || empty($user->device_token)) return;

            app(FirebaseNotificationService::class)->sendNotificationToDevice(
                $user->device_token,
                'رسالة جديدة من الدعم',
                $message->type === 'text' ? ($message->message ?? 'رسالة') : 'مرفق جديد',
                [
                    'type'            => 'new_chat_message',
                    'conversation_id' => (string) $conversation->id,
                    'message_id'      => (string) $message->id,
                    'sender_type'     => 'admin',
                ]
            );
        } catch (\Exception $e) {
            Log::warning('Chat web: notify user failed: ' . $e->getMessage());
        }
    }

    private function notifyUserClosed(Conversation $conversation): void
    {
        try {
            $user = User::find($conversation->user_id);
            if (!$user || empty($user->device_token)) return;

            app(FirebaseNotificationService::class)->sendNotificationToDevice(
                $user->device_token,
                'تم إغلاق المحادثة',
                'قام فريق الدعم بإغلاق المحادثة',
                ['type' => 'conversation_closed', 'conversation_id' => (string) $conversation->id]
            );
        } catch (\Exception $e) {
            Log::warning('Chat web: notify closed failed: ' . $e->getMessage());
        }
    }
}
