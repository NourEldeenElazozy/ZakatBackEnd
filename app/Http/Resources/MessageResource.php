<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'conversation_id'  => $this->conversation_id,
            'sender_id'        => $this->sender_id,
            'sender_type'      => $this->sender_type, // 'user' or 'admin'
            'message'          => $this->message,
            'type'             => $this->type,         // 'text','image','pdf','file'
            'attachment'       => $this->when($this->attachment_path, [
                'url'  => $this->attachment_url,
                'name' => $this->attachment_name,
                'mime' => $this->attachment_mime,
                'size' => $this->attachment_size,
            ]),
            'is_read'          => $this->isRead(),
            'read_at'          => $this->read_at?->toIso8601String(),
            'created_at'       => $this->created_at->toIso8601String(),
        ];
    }
}
