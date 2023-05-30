<?php

namespace App\Sharp\Posts\Commands;

use App\Models\Post;
use Code16\Sharp\EntityList\Commands\EntityCommand;

class BulkPublishPostsCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Publish all selected posts';
    }

    public function buildCommandConfig(): void
    {
        $this->configureDescription('Bulk command to publish posts')
            ->configureInstanceSelectionRequired();
    }

    public function execute(array $data = []): array
    {
        Post::whereIn('id', $this->selectedIds())
            ->get()
            ->each(fn (Post $post) => $post
                ->update(['state' => 'published'])
            );

        return $this->reload();
    }
}
