<?php

namespace App\Events;

use App\Models\Card;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Card $card) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('board.'.$this->card->board_id),
        ];
    }
}
