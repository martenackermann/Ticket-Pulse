<?php

namespace App\Actions\Boards;

use App\Models\Board;

class RenameBoardAction
{
    public function execute(Board $board, string $name): Board
    {
        $board->update(['name' => $name]);
        return $board;
    }
}
