<?php

namespace App\Actions\Cards;

use App\Models\Card;
use App\Models\User;
use App\Events\CardDeleted;
use Illuminate\Support\Facades\DB;

class DeleteCardAction
{
    public function execute(Card $card, User $user): void
    {
        $cardId = $card->id;
        $boardId = $card->board_id;
        $cardTitle = $card->title;

        DB::transaction(function () use ($card, $user) {
            Card::where('board_id', $card->board_id)
                ->where('status', $card->status)
                ->where('position', '>', $card->position)
                ->decrement('position');

            $card->board->activities()->create([
                'user_id' => $user->id,
                'event_type' => 'card_deleted',
                'payload' => ['card_title' => $card->title],
            ]);

            $card->delete();
        });

        broadcast(new CardDeleted($cardId, $boardId))->toOthers();
    }
}
