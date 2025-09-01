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
            ->configureIcon('lucide-check-check')
            ->configureInstanceSelectionRequired();
    }

    public function execute(array $data = []): array
    {
        Post::whereIn('id', $this->selectedIds())
            ->where('state', 'draft')
            ->get()
            ->each(fn (Post $post) => $post
                ->update(['state' => 'online'])
            );

        $this->notify('All selected posts were published!');

        return $this->reload();
    }
}
