<?php

namespace App\Http\Controllers;

use App\Actions\Cards\CreateCardAction;
use App\Actions\Cards\DeleteCardAction;
use App\Actions\Cards\MoveCardAction;
use App\Actions\Cards\UpdateCardAction;
use App\Enums\CardStatus;
use App\Models\Board;
use App\Models\Card;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class CardController extends Controller
{
    public function store(Request $request, Board $board, CreateCardAction $action): RedirectResponse
    {
        $this->authorize('update', $board);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => [new Enum(CardStatus::class)],
        ]);

        $action->execute($board, $request->user(), $validated);

        return back();
    }

    public function update(Request $request, Card $card, UpdateCardAction $action): RedirectResponse
    {
        $this->authorize('update', $card);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $action->execute($card, $validated);

        return back();
    }

    public function move(Request $request, Card $card, MoveCardAction $action): RedirectResponse
    {
        $this->authorize('update', $card);

        $validated = $request->validate([
            'status' => ['required', new Enum(CardStatus::class)],
            'position' => 'required|integer|min:0',
        ]);

        $action->execute(
            $card,
            $request->user(),
            CardStatus::from($validated['status']),
            $validated['position']
        );

        return back();
    }

    public function destroy(Card $card, DeleteCardAction $action): RedirectResponse
    {
        $this->authorize('delete', $card);

        $action->execute($card, request()->user());

        return back();
    }
}
