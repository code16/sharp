<?php

namespace App\Sharp\Categories;

use App\Models\Category;
use App\Sharp\Categories\Commands\CleanUnusedCategoriesCommand;
use Code16\Sharp\EntityList\Eloquent\SimpleEloquentReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Filters\CheckFilter;
use Illuminate\Contracts\Support\Arrayable;

class CategoryList extends SharpEntityList
{
    protected function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Name'),
            )
            ->addField(
                EntityListField::make('posts_count')
                    ->setLabel('# posts'),
            );
    }

    public function buildListConfig(): void
    {
        $this->configureReorderable(new SimpleEloquentReorderHandler(Category::class));
    }

    protected function getEntityCommands(): ?array
    {
        return [
            CleanUnusedCategoriesCommand::class,
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            new class extends CheckFilter
            {
                public function buildFilterConfig(): void
                {
                    $this->configureKey('orphan')
                        ->configureLabel('Orphan categories only.');
                }
            },
        ];
    }

    public function getListData(): array|Arrayable
    {
        $categories = Category::withCount('posts')
            ->orderBy('order')
            ->when(
                $this->queryParams->filterFor('orphan'),
                fn ($q) => $q->having('posts_count', 0)
            );
            
        return $this->transform($categories->get());
    }
}
