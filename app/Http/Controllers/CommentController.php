<?php

namespace App\Http\Controllers;

use App\Actions\Comments\CreateCommentAction;
use App\Models\Card;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Card $card, CreateCommentAction $action)
    {
        $this->authorize('view', $card->board);

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $action->execute($card, $request->user(), $validated);

        return back();
    }
}
