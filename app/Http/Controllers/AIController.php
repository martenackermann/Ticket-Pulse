<?php

namespace App\Http\Controllers;

use App\Actions\AI\GenerateCardDescriptionAction;
use App\Actions\AI\GenerateTaskBreakdownAction;
use App\Actions\AI\SummarizeCommentsAction;
use App\Models\Card;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function generateDescription(Request $request, GenerateCardDescriptionAction $action)
    {
        $request->validate(['title' => 'required|string']);

        return response()->json([
            'description' => $action->execute($request->title),
        ]);
    }

    public function generateBreakdown(Request $request, GenerateTaskBreakdownAction $action)
    {
        $request->validate(['title' => 'required|string']);

        return response()->json([
            'tasks' => $action->execute($request->title),
        ]);
    }

    public function summarizeComments(Card $card, SummarizeCommentsAction $action)
    {
        $this->authorize('view', $card->board);

        return response()->json([
            'summary' => $action->execute($card),
        ]);
    }
}
