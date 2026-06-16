<?php

use App\Models\Board;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('board.{id}', function ($user, $id) {
    $board = Board::find($id);

    if (! $board) {
        return false;
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});
