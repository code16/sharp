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
    
    public function buildCommandConfig(): void
    {
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

        $post = sharp()
            ->context()
            ->findListInstance($instanceId, fn ($postId) => Post::find($postId));

        return $post && ($post->isOnline() || $post->author_id === auth()->id());
    }
}
