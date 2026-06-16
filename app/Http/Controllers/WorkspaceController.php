<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkspaceController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Workspaces/Index', [
            'workspaces' => $request->user()->workspaces()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $workspace = $request->user()->workspaces()->create($validated);

        return redirect()->route('workspaces.show', $workspace);
    }

    public function show(Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        return Inertia::render('Workspaces/Show', [
            'workspace' => $workspace->load('boards'),
        ]);
    }
}
