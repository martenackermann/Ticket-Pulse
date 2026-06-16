<?php

use App\Enums\CardStatus;
use App\Models\Board;
use App\Models\Card;
use App\Models\User;

test('user can create a card', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create();
    $board->workspace->update(['owner_id' => $user->id]);

    $response = $this->actingAs($user)
        ->post(route('boards.cards.store', $board->id), [
            'title' => 'New Card',
            'status' => 'todo',
        ]);

    $response->assertStatus(302);
    $this->assertDatabaseHas('cards', [
        'board_id' => $board->id,
        'title' => 'New Card',
        'status' => 'todo',
        'position' => 1,
    ]);
});

test('user can move a card', function () {
    $user = User::factory()->create();
    $board = Board::factory()->create();
    $board->workspace->update(['owner_id' => $user->id]);
    $card = Card::factory()->create(['board_id' => $board->id, 'status' => CardStatus::Todo, 'position' => 1]);

    $response = $this->actingAs($user)
        ->post(route('cards.move', $card->id), [
            'status' => 'in_progress',
            'position' => 0,
        ]);

    $response->assertStatus(302);
    $this->assertDatabaseHas('cards', [
        'id' => $card->id,
        'status' => 'in_progress',
        'position' => 0,
    ]);
});
