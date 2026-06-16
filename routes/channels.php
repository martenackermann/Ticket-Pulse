<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('board.{id}', function ($user, $id) {
    $board = \App\Models\Board::find($id);
    
    if (!$board || (int) $user->id !== (int) $board->workspace->owner_id) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});
