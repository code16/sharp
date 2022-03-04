<?php

namespace App\Sharp\Categories\Commands;

use App\Models\Category;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;

class CleanUnusedCategoriesCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Clean unused categories...';
    }

    public function buildCommandConfig(): void
    {
        $this->configureConfirmationText('Delete all categories with 0 post attached?')
            ->configureDescription('This action will remove all orphan categories');
    }

    public function execute(array $data = []): array
    {
        $deletedCount = Category::whereDoesntHave('posts')->delete();

        if ($deletedCount === 0) {
            throw new SharpApplicativeException('No unused category found!');
        }

        return $this->reload();
    }
}
