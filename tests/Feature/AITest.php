<?php

use App\Actions\AI\GenerateCardDescriptionAction;
use App\Actions\AI\GenerateTaskBreakdownAction;
use App\Actions\AI\SummarizeCommentsAction;
use App\Models\Card;
use App\Models\Comment;

test('ai can generate card description', function () {
    $action = new GenerateCardDescriptionAction();
    $result = $action->execute('Test Card');

    expect($result)->toContain('Test Card');
    expect($result)->toContain('Laravel Sanctum');
});

test('ai can generate task breakdown', function () {
    $action = new GenerateTaskBreakdownAction();
    $result = $action->execute('Test Card');

    expect($result)->toBeArray();
    expect($result)->toHaveCount(5);
});

test('ai can summarize comments', function () {
    $card = Card::factory()->create();
    Comment::factory()->count(3)->create(['card_id' => $card->id]);

    $action = new SummarizeCommentsAction();
    $result = $action->execute($card);

    expect($result)->toContain('Summary of 3 comments');
});
