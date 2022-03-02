<?php

namespace App\Sharp\Categories;

use App\Models\Category;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class CategoryList extends SharpEntityList
{
    public function buildListConfig(): void
    {
        $this
            ->configureDefaultSort('name');
    }

    public function getListData(): array|Arrayable
    {
        $categories = Category::withCount('posts')

            // Handle sorting
            ->when(
                $this->queryParams->sortedBy() === 'name',
                function (Builder $builder) {
                    $builder
                        ->orderBy('name', $this->queryParams->sortedDir());
                },
                function (Builder $builder) {
                    $builder->orderBy('post_count', $this->queryParams->sortedDir() ?: 'asc');
                },
            );

        return $this->transform($categories->get());
    }

    protected function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Name')
                    ->setSortable(),
            )
            ->addField(
                EntityListField::make('posts_count')
                    ->setLabel('# posts')
                    ->setSortable(),
            );
    }

    protected function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('name', 7)
            ->addColumn('posts_count', 5);
    }
}
