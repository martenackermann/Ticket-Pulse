<?php

use App\Models\User;
use App\Models\Workspace;

test('user can create a board in their workspace', function () {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);

    $response = $this->actingAs($user)
        ->post(route('workspaces.boards.store', $workspace->id), [
            'name' => 'New Board',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('boards', [
        'workspace_id' => $workspace->id,
        'name' => 'New Board',
    ]);
});

test('user cannot create a board in someone else\'s workspace', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $otherUser->id]);

    $response = $this->actingAs($user)
        ->post(route('workspaces.boards.store', $workspace->id), [
            'name' => 'Evil Board',
        ]);

    $response->assertStatus(403);
});
