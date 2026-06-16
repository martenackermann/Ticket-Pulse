<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [WorkspaceController::class, 'index'])->name('dashboard');

    Route::resource('workspaces', WorkspaceController::class)->only(['index', 'store', 'show']);
    Route::post('workspaces/{workspace}/boards', [BoardController::class, 'store'])->name('workspaces.boards.store');

    Route::resource('boards', BoardController::class)->only(['show', 'update', 'destroy']);
    Route::post('boards/{board}/cards', [CardController::class, 'store'])->name('boards.cards.store');

    Route::resource('cards', CardController::class)->only(['update', 'destroy']);
    Route::post('cards/{card}/move', [CardController::class, 'move'])->name('cards.move');
    Route::post('cards/{card}/comments', [CommentController::class, 'store'])->name('cards.comments.store');

    Route::prefix('ai')->name('ai.')->group(function () {
        Route::post('generate-description', [AIController::class, 'generateDescription'])->name('generate-description');
        Route::post('generate-breakdown', [AIController::class, 'generateBreakdown'])->name('generate-breakdown');
        Route::post('summarize-comments/{card}', [AIController::class, 'summarizeComments'])->name('summarize-comments');
    });
});

require __DIR__.'/settings.php';
