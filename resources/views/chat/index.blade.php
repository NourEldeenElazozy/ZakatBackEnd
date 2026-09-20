@extends('layouts.master')
@section('title', 'محادثات الدعم')

@section('css')
<style>
/* ======================================
   Chat Panel Layout
====================================== */
.chat-wrapper {
    display: flex;
    height: calc(100vh - 180px);
    min-height: 500px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 20px rgba(0,0,0,.08);
    background: #fff;
}

/* ---- Conversations Sidebar ---- */
.chat-sidebar {
    width: 320px;
    min-width: 280px;
    border-left: 1px solid #e8ecf1;
    display: flex;
    flex-direction: column;
    background: #f8f9fc;
}
.chat-sidebar-header {
    padding: 16px;
    background: #fff;
    border-bottom: 1px solid #e8ecf1;
}
.chat-sidebar-header h6 { margin: 0; font-weight: 700; font-size: 15px; }
.chat-search { position: relative; margin-top: 10px; }
.chat-search input {
    border-radius: 20px;
    padding-right: 36px;
    font-size: 13px;
    background: #f0f2f5;
    border: none;
}
.chat-search i {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 13px;
}
.chat-filter-tabs {
    display: flex;
    border-bottom: 1px solid #e8ecf1;
    background: #fff;
}
.chat-filter-tabs a {
    flex: 1;
    text-align: center;
    padding: 8px 4px;
    font-size: 12px;
    color: #6c757d;
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: all .2s;
}
.chat-filter-tabs a.active {
    color: #6259ca;
    border-bottom-color: #6259ca;
    font-weight: 600;
}
.conversation-list {
    flex: 1;
    overflow-y: auto;
}
.conv-item {
    display: flex;
    align-items: center;
    padding: 12px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f0f2f5;
    text-decoration: none;
    transition: background .15s;
    position: relative;
}
.conv-item:hover { background: #eef0f8; text-decoration: none; }
.conv-item.active { background: #eef0f8; border-right: 3px solid #6259ca; }
.conv-avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: #6259ca;
    color: #fff;
    font-weight: 700;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-left: 10px;
}
.conv-info { flex: 1; min-width: 0; }
.conv-name {
    font-weight: 600;
    font-size: 13px;
    color: #3d405b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.conv-preview {
    font-size: 11px;
    color: #999;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 2px;
}
.conv-meta { text-align: left; flex-shrink: 0; }
.conv-time { font-size: 10px; color: #bbb; }
.conv-badge {
    display: inline-block;
    background: #f74f75;
    color: #fff;
    border-radius: 10px;
    font-size: 10px;
    padding: 1px 6px;
    margin-top: 3px;
}
.conv-status-closed { opacity: .55; }

/* ---- Chat Main Area ---- */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #f0f4f8;
}
.chat-main-placeholder {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #bbb;
}
.chat-main-placeholder i { font-size: 60px; margin-bottom: 12px; }
.chat-top-bar {
    padding: 14px 18px;
    background: #fff;
    border-bottom: 1px solid #e8ecf1;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.chat-top-bar .user-info { display: flex; align-items: center; gap: 10px; }
.chat-top-bar .user-name { font-weight: 700; font-size: 15px; }
.chat-top-bar .user-meta { font-size: 11px; color: #999; }
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
/* Bubble */
.msg-row { display: flex; }
.msg-row.from-admin { justify-content: flex-start; }
.msg-row.from-user { justify-content: flex-end; }
.msg-bubble {
    max-width: 60%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 13px;
    line-height: 1.5;
    position: relative;
    word-break: break-word;
}
.msg-row.from-admin .msg-bubble {
    background: #fff;
    border-bottom-right-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,.07);
    color: #333;
}
.msg-row.from-user .msg-bubble {
    background: #6259ca;
    border-bottom-left-radius: 4px;
    color: #fff;
}
.msg-time {
    font-size: 10px;
    margin-top: 4px;
    opacity: .6;
    text-align: right;
}
.msg-row.from-admin .msg-time { text-align: left; }
.msg-attachment img { max-width: 200px; border-radius: 8px; margin-bottom: 4px; cursor: pointer; }
.msg-attachment a { color: inherit; font-size: 12px; }

/* Input Area */
.chat-input-area {
    padding: 12px 16px;
    background: #fff;
    border-top: 1px solid #e8ecf1;
}
.chat-input-area.closed-bar {
    text-align: center;
    color: #999;
    font-size: 13px;
    padding: 16px;
}
.chat-input-form { display: flex; gap: 8px; align-items: flex-end; }
.chat-input-form textarea {
    flex: 1;
    border-radius: 20px;
    resize: none;
    padding: 10px 16px;
    font-size: 13px;
    border: 1px solid #e0e3ef;
    max-height: 120px;
    min-height: 42px;
}
.chat-input-form textarea:focus { outline: none; border-color: #6259ca; box-shadow: none; }
.chat-send-btn {
    background: #6259ca;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 42px; height: 42px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: background .2s;
    flex-shrink: 0;
}
.chat-send-btn:hover { background: #4a43b1; }
.chat-attach-btn {
    background: #f0f2f5;
    color: #555;
    border: none;
    border-radius: 50%;
    width: 42px; height: 42px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
}
.chat-attach-btn:hover { background: #e0e3ef; }
#attachmentPreview { font-size: 11px; color: #6259ca; margin-top: 4px; }

/* Date separator */
.date-separator {
    text-align: center;
    margin: 8px 0;
    font-size: 11px;
    color: #bbb;
}
.date-separator span {
    background: #e8ecf1;
    padding: 3px 12px;
    border-radius: 20px;
}
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <h2 class="main-content-title tx-24 mg-b-1">محادثات الدعم</h2>
        <p class="mg-b-0">إدارة محادثات المستخدمين والرد عليها</p>
    </div>
    <div class="right-content">
        @if($totalUnread > 0)
        <span class="badge badge-danger badge-pill px-3 py-2" style="font-size:13px;">
            <i class="fa fa-envelope"></i> {{ $totalUnread }} رسالة جديدة
        </span>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="chat-wrapper">

    {{-- ===================== SIDEBAR ===================== --}}
    <div class="chat-sidebar">

        {{-- Header + Search --}}
        <div class="chat-sidebar-header">
            <h6><i class="fa fa-comments text-primary ml-1"></i> المحادثات</h6>
            <form method="GET" action="{{ route('chat.index') }}" class="chat-search">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="بحث باسم أو هاتف..." value="{{ $search }}">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="hidden" name="conversation" value="{{ $activeConversation?->id }}">
                <i class="fa fa-search"></i>
            </form>
        </div>

        {{-- Status Filter Tabs --}}
        <div class="chat-filter-tabs">
            <a href="{{ route('chat.index', ['status'=>'open', 'conversation'=>$activeConversation?->id]) }}"
               class="{{ $status === 'open' ? 'active' : '' }}">
               <i class="fa fa-circle text-success" style="font-size:8px"></i> مفتوحة
            </a>
            <a href="{{ route('chat.index', ['status'=>'closed', 'conversation'=>$activeConversation?->id]) }}"
               class="{{ $status === 'closed' ? 'active' : '' }}">
               <i class="fa fa-circle text-secondary" style="font-size:8px"></i> مغلقة
            </a>
            <a href="{{ route('chat.index', ['status'=>'all', 'conversation'=>$activeConversation?->id]) }}"
               class="{{ $status === 'all' ? 'active' : '' }}">
               الكل
            </a>
        </div>

        {{-- Conversations List --}}
        <div class="conversation-list">
            @forelse($conversations as $conv)
            @php
                $unread = \App\Models\Message::where('conversation_id', $conv->id)
                    ->where('sender_type','user')->whereNull('read_at')->count();
                $initial = mb_strtoupper(mb_substr($conv->user->name ?? 'U', 0, 1));
                $avatarColors = ['#6259ca','#f74f75','#0ca9f2','#28a745','#fd7e14','#6c757d'];
                $color = $avatarColors[$conv->user_id % count($avatarColors)];
                $isActive = $activeConversation && $activeConversation->id === $conv->id;
                $isClosed = $conv->status === 'closed';
            @endphp
            <a class="conv-item {{ $isActive ? 'active' : '' }} {{ $isClosed ? 'conv-status-closed' : '' }}"
               href="{{ route('chat.index', ['conversation'=>$conv->id, 'status'=>$status, 'search'=>$search]) }}">
                <div class="conv-avatar" style="background:{{ $color }}">{{ $initial }}</div>
                <div class="conv-info">
                    <div class="conv-name">
                        {{ $conv->user->name ?? 'مستخدم محذوف' }}
                        @if($isClosed)<i class="fa fa-lock text-muted" style="font-size:10px"></i>@endif
                    </div>
                    <div class="conv-preview">
                        @if($conv->lastMessage)
                            @if($conv->lastMessage->sender_type === 'admin')<span>أنت: </span>@endif
                            {{ $conv->lastMessage->type !== 'text' ? '📎 مرفق' : Str::limit($conv->lastMessage->message, 35) }}
                        @else
                            <em>لا توجد رسائل بعد</em>
                        @endif
                    </div>
                </div>
                <div class="conv-meta">
                    <div class="conv-time">
                        {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : $conv->created_at->diffForHumans() }}
                    </div>
                    @if($unread > 0)
                    <div class="text-left"><span class="conv-badge">{{ $unread }}</span></div>
                    @endif
                </div>
            </a>
            @empty
            <div class="text-center text-muted p-4">
                <i class="fa fa-inbox fa-2x mb-2"></i><br>لا توجد محادثات
            </div>
            @endforelse

            {{-- Pagination --}}
            @if($conversations->hasPages())
            <div class="p-2" style="font-size:12px;">
                {{ $conversations->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ===================== MAIN CHAT AREA ===================== --}}
    <div class="chat-main">

        @if($activeConversation)
        {{-- Top Bar --}}
        <div class="chat-top-bar">
            <div class="user-info">
                @php
                    $initial = mb_strtoupper(mb_substr($activeConversation->user->name ?? 'U', 0, 1));
                    $avatarColors = ['#6259ca','#f74f75','#0ca9f2','#28a745','#fd7e14','#6c757d'];
                    $color = $avatarColors[$activeConversation->user_id % count($avatarColors)];
                @endphp
                <div class="conv-avatar" style="background:{{ $color }}; width:36px; height:36px; font-size:14px">{{ $initial }}</div>
                <div>
                    <div class="user-name">{{ $activeConversation->user->name ?? 'مستخدم محذوف' }}</div>
                    <div class="user-meta">
                        {{ $activeConversation->user->phone ?? '' }}
                        &nbsp;·&nbsp;
                        @if($activeConversation->status === 'open')
                            <span class="text-success"><i class="fa fa-circle" style="font-size:8px"></i> مفتوحة</span>
                        @else
                            <span class="text-muted"><i class="fa fa-lock" style="font-size:8px"></i> مغلقة</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                @if($activeConversation->status === 'open')
                <form method="POST" action="{{ route('chat.close', $activeConversation->id) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('هل تريد إغلاق هذه المحادثة؟')">
                        <i class="fa fa-times-circle"></i> إغلاق
                    </button>
                </form>
                @else
                <form method="POST" action="{{ route('chat.reopen', $activeConversation->id) }}" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="fa fa-redo"></i> إعادة فتح
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Messages --}}
        <div class="chat-messages" id="chatMessages">
            @php $lastDate = null; @endphp
            @forelse($messages as $msg)
                @php $msgDate = $msg->created_at->format('Y-m-d'); @endphp
                @if($msgDate !== $lastDate)
                    <div class="date-separator">
                        <span>{{ $msg->created_at->isToday() ? 'اليوم' : ($msg->created_at->isYesterday() ? 'أمس' : $msg->created_at->format('d M Y')) }}</span>
                    </div>
                    @php $lastDate = $msgDate; @endphp
                @endif

                <div class="msg-row from-{{ $msg->sender_type }}">
                    <div class="msg-bubble">
                        {{-- Attachment --}}
                        @if($msg->attachment_path)
                        <div class="msg-attachment">
                            @if($msg->type === 'image')
                                <img src="{{ Storage::url($msg->attachment_path) }}"
                                     onclick="window.open(this.src)" alt="صورة"><br>
                            @else
                                <a href="{{ Storage::url($msg->attachment_path) }}" target="_blank">
                                    <i class="fa fa-file-{{ $msg->type === 'pdf' ? 'pdf' : 'alt' }}"></i>
                                    {{ $msg->attachment_name }}
                                </a><br>
                            @endif
                        </div>
                        @endif

                        {{-- Text --}}
                        @if($msg->message)
                            <div>{{ $msg->message }}</div>
                        @endif

                        {{-- Time + read --}}
                        <div class="msg-time">
                            {{ $msg->created_at->format('H:i') }}
                            @if($msg->sender_type === 'admin')
                                @if($msg->read_at)
                                    <i class="fa fa-check-double" style="color:{{ $msg->sender_type==='admin' ? '#a0d8ef' : '#999' }}"></i>
                                @else
                                    <i class="fa fa-check" style="opacity:.6"></i>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="chat-main-placeholder">
                    <i class="fa fa-comment-dots"></i>
                    <p>ابدأ المحادثة بإرسال رسالة</p>
                </div>
            @endforelse
        </div>

        {{-- Input / Closed Bar --}}
        @if($activeConversation->status === 'closed')
        <div class="chat-input-area closed-bar">
            <i class="fa fa-lock ml-1"></i> هذه المحادثة مغلقة — لا يمكن إرسال رسائل جديدة
        </div>
        @else
        <div class="chat-input-area">
            @if(session('success'))
            <div class="alert alert-success alert-sm py-1 mb-2" style="font-size:12px">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-sm py-1 mb-2" style="font-size:12px">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('chat.reply', $activeConversation->id) }}"
                  enctype="multipart/form-data" id="replyForm">
                @csrf
                <div class="chat-input-form">
                    {{-- Attach button --}}
                    <label for="attachment" class="chat-attach-btn mb-0" title="إرفاق ملف">
                        <i class="fa fa-paperclip"></i>
                    </label>
                    <input type="file" id="attachment" name="attachment" class="d-none"
                           accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip">

                    <textarea name="message" id="messageInput" class="form-control"
                              placeholder="اكتب ردك هنا..." rows="1"
                              onkeydown="submitOnEnter(event)"></textarea>

                    <button type="submit" class="chat-send-btn" title="إرسال">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </div>
                <div id="attachmentPreview"></div>
            </form>
        </div>
        @endif

        @else
        {{-- No conversation selected --}}
        <div class="chat-main-placeholder">
            <i class="fa fa-comments"></i>
            <p style="font-size:15px; margin-top:12px">اختر محادثة من القائمة للبدء</p>
        </div>
        @endif

    </div>{{-- /chat-main --}}
</div>{{-- /chat-wrapper --}}
@endsection

@section('js')
<script>
// Auto scroll to bottom
const chatMessages = document.getElementById('chatMessages');
if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Send on Enter (Shift+Enter for new line)
function submitOnEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('replyForm')?.submit();
    }
}

// Auto resize textarea
const ta = document.getElementById('messageInput');
if (ta) {
    ta.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
}

// Attachment preview
document.getElementById('attachment')?.addEventListener('change', function() {
    const preview = document.getElementById('attachmentPreview');
    if (this.files[0]) {
        preview.innerHTML = '<i class="fa fa-paperclip"></i> ' + this.files[0].name +
            ' <a href="#" onclick="clearAttachment(event)">✕</a>';
    }
});

function clearAttachment(e) {
    e.preventDefault();
    document.getElementById('attachment').value = '';
    document.getElementById('attachmentPreview').innerHTML = '';
}

// Auto-refresh every 15s if there's an active conversation
@if($activeConversation && $activeConversation->status === 'open')
setTimeout(() => location.reload(), 15000);
@endif
</script>
@endsection
