<?php

namespace App\Actions\Cards;

use App\Models\Card;

class UpdateCardAction
{
    /**
     * @param  array{title?: string, description?: string|null}  $data
     */
    public function execute(Card $card, array $data): Card
    {
        $card->update([
            'title' => $data['title'] ?? $card->title,
            'description' => $data['description'] ?? $card->description,
        ]);

        return $card;
    }
}
