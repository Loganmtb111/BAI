<?php

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;

class IdeaPolicy
{
    /**
     * Determine if the given user can update the idea.
     */
    public function update(User $user, Idea $idea): bool
    {
        return $user->id === $idea->user_id || $user->isAdmin();
    }

    /**
     * Determine if the given user can delete the idea.
     */
    public function delete(User $user, Idea $idea): bool
    {
        return $user->id === $idea->user_id || $user->isAdmin();
    }
}
