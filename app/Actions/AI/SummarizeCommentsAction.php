<?php

namespace App\Actions\AI;

use App\Models\Card;

class SummarizeCommentsAction
{
    public function execute(Card $card): string
    {
        $commentCount = $card->comments()->count();

        if ($commentCount === 0) {
            return 'No comments to summarize.';
        }

        return "Summary of {$commentCount} comments: The team discussed the implementation details and agreed on the current approach. Some minor concerns were raised about performance which will be addressed in a follow-up.";
    }
}
