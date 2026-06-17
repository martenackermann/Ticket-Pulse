<?php

namespace App\Actions\Boards;

use App\Models\Board;
use App\Models\Workspace;

class CreateBoardAction
{
    /**
     * @param  array{name: string}  $data
     */
    public function execute(Workspace $workspace, array $data): Board
    {
        return $workspace->boards()->create([
            'name' => $data['name'],
        ]);
    }
}
