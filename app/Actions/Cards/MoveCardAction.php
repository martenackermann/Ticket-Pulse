<?php

namespace App\Actions\Cards;

use App\Enums\CardStatus;
use App\Events\CardMoved;
use App\Models\Card;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MoveCardAction
{
    public function execute(Card $card, User $user, CardStatus $newStatus, int $newPosition): Card
    {
        DB::transaction(function () use ($card, $user, $newStatus, $newPosition) {
            $oldStatus = $card->status;
            $oldPosition = $card->position;

            if ($oldStatus === $newStatus) {
                if ($oldPosition < $newPosition) {
                    Card::where('board_id', $card->board_id)
                        ->where('status', $newStatus)
                        ->whereBetween('position', [$oldPosition + 1, $newPosition])
                        ->decrement('position');
                } elseif ($oldPosition > $newPosition) {
                    Card::where('board_id', $card->board_id)
                        ->where('status', $newStatus)
                        ->whereBetween('position', [$newPosition, $oldPosition - 1])
                        ->increment('position');
                }
            } else {
                // Remove from old status
                Card::where('board_id', $card->board_id)
                    ->where('status', $oldStatus)
                    ->where('position', '>', $oldPosition)
                    ->decrement('position');

                // Make room in new status
                Card::where('board_id', $card->board_id)
                    ->where('status', $newStatus)
                    ->where('position', '>=', $newPosition)
                    ->increment('position');
            }

            $card->update([
                'status' => $newStatus,
                'position' => $newPosition,
            ]);

            $card->board->activities()->create([
                'user_id' => $user->id,
                'event_type' => 'card_moved',
                'payload' => [
                    'card_title' => $card->title,
                    'from_status' => $oldStatus->value,
                    'to_status' => $newStatus->value,
                ],
            ]);
        });

        $card = $card->fresh();
        broadcast(new CardMoved($card))->toOthers();

        return $card;
    }
}
