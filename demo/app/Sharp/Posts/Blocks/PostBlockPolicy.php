<?php

namespace App\Sharp\Posts\Blocks;

use App\Models\Post;
use App\Models\PostBlock;
use App\Sharp\Entities\PostEntity;
use Code16\Sharp\Auth\SharpEntityPolicy;

class PostBlockPolicy extends SharpEntityPolicy
{
    public function entity($user): bool
    {
        // Only authorized in EEL case
        return sharp()->context()->breadcrumb()->previousShowSegment(PostEntity::class) !== null;
    }

    public function view($user, $instanceId): bool
    {
        $block = sharp()
            ->context()
            ->findListInstance($instanceId, fn ($postBlockId) => PostBlock::find($postBlockId));

        return $block && ($user->isAdmin() || $block->post?->author_id === $user->id);
    }

    public function create($user): bool
    {
        if (! $postId = sharp()->context()->breadcrumb()->previousShowSegment(PostEntity::class)?->instanceId()) {
            return false;
        }

        $post = Post::find($postId);

        return $post && ($user->isAdmin() || $post->author_id === $user->id);
    }
}
