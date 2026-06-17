<?php

namespace App\Http\Controllers;

use App\Actions\Comments\CreateCommentAction;
use App\Models\Card;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Card $card, CreateCommentAction $action): JsonResponse|RedirectResponse
    {
        $this->authorize('view', $card->board);

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $comment = $action->execute($card, $request->user(), $validated);

        if ($request->expectsJson()) {
            return response()->json([
                'comment' => $comment,
            ]);
        }

        return back();
    }
}
