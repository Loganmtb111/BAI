<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Services\Logging\ActionLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller responsible for managing ideas.
 *
 * NOTE:
 * - No validation (voluntary vulnerabilities for the cybersecurity exercises) (TODO)
 * - Authorization via IdeaPolicy (owner OR admin can edit/delete)
 * - XSS not escaped in the views (TODO)
 */
class IdeaController extends Controller
{
    public function __construct()
    {

    }

    /**
     * Display all ideas.
     */
    public function index()
    {
        // Loads ideas with their authors (simple pagination)
        $ideas = Idea::with('user')
            ->latest()
            ->paginate(10);

        return view('ideas.index', compact('ideas'));
    }

    /**
     * Show the form to create a new idea.
     */
    public function create()
    {
        return view('ideas.create');
    }

    /**
     * Store a newly created idea.
     *
     * SECURITY NOTE:
     * - No validation (TODO)
     * - No rate limiting (TODO)
     */
    public function store(Request $request)
    {
        $idea = Idea::create([
            'user_id'     => Auth::id(),
            'title'       => $request->input('title'),
            'description' => $request->input('description'), // XSS not escaped
            'application' => $request->input('application'),
        ]);

        app(ActionLogService::class)->log(
            userId: Auth::id(),
            action: 'idea_created',
            ideaId: $idea->id,
            request: $request
        );

        return redirect()
            ->route('ideas.show', $idea)
            ->with('status', 'Idea created (vulnerable version).');
    }

    /**
     * Display the specified idea.
     */
    public function show(Idea $idea)
    {
        $idea->load('comments.user');
        return view('ideas.show', compact('idea'));
    }

    /**
     * Show edit form.
     */
    public function edit(Idea $idea)
    {
        $this->authorize('update', $idea);
        return view('ideas.edit', compact('idea'));
    }

    /**
     * Update the idea.
     */
    public function update(Request $request, Idea $idea)
    {
        $this->authorize('update', $idea);

        $dataBefore = json_encode($idea->only('title', 'description', 'application'));

        $idea->update([
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'application' => $request->input('application'),
        ]);

        app(ActionLogService::class)->log(
            userId: Auth::id(),
            action: 'idea_updated',
            ideaId: $idea->id,
            dataBefore: $dataBefore,
            dataAfter: json_encode($idea->only('title', 'description', 'application')),
            request: $request
        );

        return redirect()
            ->route('ideas.show', $idea)
            ->with('status', 'Idea updated.');
    }

    /**
     * Remove an idea.
     */
    public function destroy(Idea $idea)
    {
        $this->authorize('delete', $idea);

        app(ActionLogService::class)->log(
            userId: Auth::id(),
            action: 'idea_deleted',
            ideaId: $idea->id,
            dataBefore: json_encode($idea->only('title', 'description', 'application')),
            request: request()
        );

        $idea->delete();

        return redirect()
            ->route('ideas.index')
            ->with('status', 'Idea deleted.');
    }
}
