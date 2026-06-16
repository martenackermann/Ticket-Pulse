<?php

namespace App\Actions\Boards;

use App\Models\Board;

class DeleteBoardAction
{
    public function execute(Board $board): void
    {
        $board->delete();
    }
}
