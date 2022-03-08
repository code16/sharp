<?php

namespace App\Sharp\Posts;

use App\Models\Post;
use Code16\Sharp\EntityList\Commands\EntityState;

class PostStateHandler extends EntityState
{
    protected function buildStates(): void
    {
        $this->addState('draft', 'Draft', 'orange')
            ->addState('online', 'Online', '#0c4589');
    }

    protected function updateState($instanceId, string $stateId): array
    {
        Post::findOrFail($instanceId)->update([
            'state' => $stateId,
        ]);

        return $this->refresh($instanceId);
    }

    public function authorizeFor(mixed $instanceId): bool
    {
        if (auth()->user()->isAdmin()) {
            return true;
        }

        return Post::find($instanceId)?->author_id === auth()->id();
    }
}
