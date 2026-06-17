<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WorkspaceController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Workspaces/Index', [
            'workspaces' => Workspace::query()->with('boards')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $workspace = $request->user()->workspaces()->create($validated);

        return redirect()->route('workspaces.show', $workspace);
    }

    public function show(Workspace $workspace): Response
    {
        $this->authorize('view', $workspace);

        return Inertia::render('Workspaces/Show', [
            'workspace' => $workspace->load('boards'),
        ]);
    }
}
