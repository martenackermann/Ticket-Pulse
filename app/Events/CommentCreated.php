<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Comment $comment) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('board.'.$this->comment->card->board_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'comment.created';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'comment' => [
                'id' => $this->comment->id,
                'card_id' => $this->comment->card_id,
                'user_id' => $this->comment->user_id,
                'body' => $this->comment->body,
                'created_at' => $this->comment->created_at?->toIso8601String(),
                'user' => [
                    'id' => $this->comment->user?->id,
                    'name' => $this->comment->user?->name,
                    'email' => $this->comment->user?->email,
                ],
            ],
        ];
    }
}
