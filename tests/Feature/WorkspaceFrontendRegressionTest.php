<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('workspace pages use generated route helpers and do not nest app layout', function () {
    $workspacePages = [
        resource_path('js/pages/Workspaces/Index.vue'),
        resource_path('js/pages/Workspaces/Show.vue'),
    ];

    foreach ($workspacePages as $workspacePage) {
        $content = file_get_contents($workspacePage);

        expect($content)
            ->not->toContain('route(')
            ->not->toContain('<AppLayout')
            ->not->toContain('</AppLayout>');
    }
});

test('authenticated user can create a workspace', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('workspaces.store'), [
            'name' => 'Roadmap',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('workspaces', [
        'owner_id' => $user->id,
        'name' => 'Roadmap',
    ]);
});
