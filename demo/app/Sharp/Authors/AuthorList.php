<?php

namespace App\Sharp\Authors;

use App\Models\User;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class AuthorList extends SharpEntityList
{
    public function buildListConfig(): void
    {
        $this->configureSearchable()
            ->configureDefaultSort('name');
    }

    public function getListData(): array|Arrayable
    {
        $users = User::query()

            // Handle search words
            ->when(
                $this->queryParams->hasSearch(),
                function (Builder $builder) {
                    foreach ($this->queryParams->searchWords() as $word) {
                        $builder->where(function ($query) use ($word) {
                            $query
                                ->orWhere('name', 'like', $word)
                                ->orWhere('email', 'like', $word);
                        });
                    }
                },
            )

            // Handle sorting
            ->when(
                $this->queryParams->sortedBy() === 'email',
                function (Builder $builder) {
                    $builder
                        ->orderBy('email', $this->queryParams->sortedDir());
                },
                function (Builder $builder) {
                    $builder->orderBy('name', $this->queryParams->sortedDir() ?: 'asc');
                },
            );

        return $this
            ->setCustomTransformer('avatar', function ($value, User $user) {
                return '<img src="'.$user->avatar->thumbnail(140).'" alt="" class="img-fluid">';
            })
            ->transform($users->get());
    }

    protected function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('avatar')
                    ->setLabel('Avatar'),
            )
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Name')
                    ->setSortable(),
            )
            ->addField(
                EntityListField::make('email')
                    ->setLabel('Email')
                    ->setSortable(),
            );
    }

    protected function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('avatar', 2)
            ->addColumn('name', 5)
            ->addColumn('email', 5);
    }
}
