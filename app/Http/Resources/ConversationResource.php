<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * The role requesting this resource ('user' or 'admin').
     * Used to compute unread_count correctly.
     */
    public string $role;

    public function __construct($resource, string $role = 'user')
    {
        parent::__construct($resource);
        $this->role = $role;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'user_name'       => $this->whenLoaded('user', fn() => $this->user->name),
            'status'          => $this->status,
            'unread_count'    => $this->unreadCountFor($this->role),
            'last_message'    => $this->whenLoaded('lastMessage', function () {
                return $this->lastMessage
                    ? new MessageResource($this->lastMessage)
                    : null;
            }),
            'last_message_at' => $this->last_message_at?->toIso8601String(),
            'created_at'      => $this->created_at->toIso8601String(),
            'updated_at'      => $this->updated_at->toIso8601String(),
        ];
    }
}
