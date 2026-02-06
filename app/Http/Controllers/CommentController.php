<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller responsible for comments on ideas.
 *
 * NOTE:
 * - No validation (TODO)
 * - No limit per user (add max 3 comments per idea) (TODO)
 * - Authorization via CommentPolicy (owner OR admin can delete)
 */
class CommentController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Store a new comment for an idea.
     */
    public function store(Request $request, Idea $idea)
    {
        Comment::create([
            'idea_id'     => $idea->id,
            'user_id'     => Auth::id(),
            'description' => $request->input('description'), // XSS vulnerable
        ]);

        return redirect()
            ->route('ideas.show', $idea)
            ->with('status', 'Comment added.');
    }

    /**
     * Remove a comment.
     */
    public function destroy(Idea $idea, Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return redirect()
            ->route('ideas.show', $idea)
            ->with('status', 'Comment deleted.');
    }
}
