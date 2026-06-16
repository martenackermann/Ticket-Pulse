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

test('user can create a board in someone else\'s workspace for collaboration testing', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $otherUser->id]);

    $response = $this->actingAs($user)
        ->post(route('workspaces.boards.store', $workspace->id), [
            'name' => 'Evil Board',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('boards', [
        'workspace_id' => $workspace->id,
        'name' => 'Evil Board',
    ]);
});

test('board show page uses generated route helpers and does not nest app layout', function () {
    $boardContent = file_get_contents(resource_path('js/pages/Boards/Show.vue'));
    $modalContent = file_get_contents(resource_path('js/components/CardModal.vue'));

    expect($boardContent)
        ->toContain('@/routes/boards/cards')
        ->toContain("import { echo } from '@laravel/echo-vue';")
        ->toContain('.error((error: any) => {')
        ->toContain(".listen('.comment.created', (e: any) => {")
        ->not->toContain('route(')
        ->not->toContain('<AppLayout')
        ->not->toContain('</AppLayout>');

    expect($modalContent)
        ->toContain('@/routes/cards/comments')
        ->toContain('@/routes/cards')
        ->toContain('persistCard();')
        ->not->toContain('route(');
});

test('authenticated user can authorize board presence channel', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create(['owner_id' => $user->id]);
    $board = $workspace->boards()->create(['name' => 'Realtime Board']);

    $response = $this->actingAs($user)
        ->withHeader('Accept', 'application/json')
        ->post('/broadcasting/auth', [
            'socket_id' => '1234.5678',
            'channel_name' => 'presence-board.'.$board->id,
        ]);

    $response->assertOk();
    $response->assertJsonStructure([
        'auth',
        'channel_data',
    ]);
});
