<?php

namespace App\Sharp\Posts;

use App\Models\Post;
use Code16\Sharp\Auth\SharpEntityPolicy;

class PostPolicy extends SharpEntityPolicy
{
    public function view($user, $instanceId): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($post = Post::find($instanceId)) {
            return $post->isOnline() || $post->author_id === $user->id;
        }

        return false;
    }

    public function update($user, $instanceId): bool
    {
        return $user->isAdmin()
            || Post::find($instanceId)->author_id === $user->id;
    }

    public function delete($user, $instanceId): bool
    {
        return $this->update($user, $instanceId);
    }
}
