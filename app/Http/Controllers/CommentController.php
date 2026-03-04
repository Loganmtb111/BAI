<?php

namespace App\Http\Controllers;

use App\Models\Idea;
use App\Models\Comment;
use App\Services\Logging\ActionLogService;
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
        $comment = Comment::create([
            'idea_id'     => $idea->id,
            'user_id'     => Auth::id(),
            'description' => $request->input('description'), // XSS vulnerable
        ]);

        app(ActionLogService::class)->log(
            userId: Auth::id(),
            action: 'comment_created',
            ideaId: $idea->id,
            commentId: $comment->id,
            request: $request
        );

        return redirect()
            ->route('ideas.show', $idea)
            ->with('status', 'Comment added.');
    }

    /**
     * Show edit form for a comment.
     */
    public function edit(Idea $idea, Comment $comment)
    {
        $this->authorize('update', $comment);
        return view('comments.edit', compact('idea', 'comment'));
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, Idea $idea, Comment $comment)
    {
        $this->authorize('update', $comment);

        $dataBefore = json_encode($comment->only('description'));

        $comment->update([
            'description' => $request->input('description'),
        ]);

        app(ActionLogService::class)->log(
            userId: Auth::id(),
            action: 'comment_updated',
            ideaId: $idea->id,
            commentId: $comment->id,
            dataBefore: $dataBefore,
            dataAfter: json_encode($comment->only('description')),
            request: $request
        );

        return redirect()
            ->route('ideas.show', $idea)
            ->with('status', 'Comment updated.');
    }

    /**
     * Remove a comment.
     */
    public function destroy(Idea $idea, Comment $comment)
    {
        $this->authorize('delete', $comment);

        app(ActionLogService::class)->log(
            userId: Auth::id(),
            action: 'comment_deleted',
            ideaId: $idea->id,
            commentId: $comment->id,
            dataBefore: json_encode($comment->only('description')),
            request: request()
        );

        $comment->delete();

        return redirect()
            ->route('ideas.show', $idea)
            ->with('status', 'Comment deleted.');
    }
}
