<?php

namespace App\Actions\Cards;

use App\Enums\CardStatus;
use App\Events\CardCreated;
use App\Models\Board;
use App\Models\Card;
use App\Models\User;

class CreateCardAction
{
    /**
     * @param  array{title: string, description?: string|null, status?: CardStatus}  $data
     */
    public function execute(Board $board, User $user, array $data): Card
    {
        $status = $data['status'] ?? CardStatus::Todo;

        $position = $board->cards()
            ->where('status', $status)
            ->max('position') + 1;

        $card = $board->cards()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $status,
            'position' => $position,
        ]);

        $board->activities()->create([
            'user_id' => $user->id,
            'event_type' => 'card_created',
            'payload' => ['card_title' => $card->title],
        ]);

        broadcast(new CardCreated($card))->toOthers();

        return $card;
    }
}
