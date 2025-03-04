<?php

namespace App\Sharp\Posts\Blocks;

use App\Models\Post;
use App\Models\PostBlock;
use App\Sharp\Entities\PostEntity;
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
            || Post::find(sharp()->context()->breadcrumb()->previousShowSegment(PostEntity::class)->instanceId())?->author_id === $user->id;
    }
}
