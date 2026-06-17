<?php

namespace App\Http\Controllers;

use App\Actions\Boards\CreateBoardAction;
use App\Actions\Boards\DeleteBoardAction;
use App\Actions\Boards\RenameBoardAction;
use App\Models\Board;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BoardController extends Controller
{
    public function show(Board $board): Response
    {
        $this->authorize('view', $board);

        return Inertia::render('Boards/Show', [
            'workspace' => $board->workspace,
            'board' => $board->load(['cards.comments.user', 'activities.user']),
        ]);
    }

    public function store(Request $request, Workspace $workspace, CreateBoardAction $action): RedirectResponse
    {
        $this->authorize('update', $workspace);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $board = $action->execute($workspace, $validated);

        return redirect()->route('boards.show', $board);
    }

    public function update(Request $request, Board $board, RenameBoardAction $action): RedirectResponse
    {
        $this->authorize('update', $board);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $action->execute($board, $validated['name']);

        return back();
    }

    public function destroy(Board $board, DeleteBoardAction $action): RedirectResponse
    {
        $this->authorize('delete', $board);

        $workspace = $board->workspace;
        $action->execute($board);

        return redirect()->route('workspaces.show', $workspace);
    }
}
