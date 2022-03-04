<?php

namespace App\Sharp\Posts\Commands;

use App\Models\Post;
use Code16\Sharp\EntityList\Commands\InstanceCommand;

class PreviewPostCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Preview post';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        return $this->view('sharp.post-preview', [
            'post' => Post::findOrFail($instanceId),
        ]);
    }

    public function authorizeFor(mixed $instanceId): bool
    {
        if (auth()->user()->isAdmin()) {
            return true;
        }

        if ($post = Post::find($instanceId)) {
            return $post->isOnline() || $post->author_id === auth()->id();
        }

        return false;
    }
}
