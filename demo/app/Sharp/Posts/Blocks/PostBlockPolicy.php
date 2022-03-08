<?php

namespace App\Sharp\Posts\Blocks;

use App\Models\Post;
use App\Models\PostBlock;
use Code16\Sharp\Auth\SharpEntityPolicy;

class PostBlockPolicy extends SharpEntityPolicy
{
    public function view($user, $instanceId): bool
    {
        return $user->isAdmin()
            || PostBlock::find($instanceId)?->post?->author_id === $user->id;
    }

    public function create($user): bool
    {
        return $user->isAdmin()
            || Post::find(currentSharpRequest()->getPreviousShowFromBreadcrumbItems('posts')->instanceId())?->author_id === $user->id;
    }
}
