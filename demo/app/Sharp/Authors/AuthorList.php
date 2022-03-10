<?php

namespace App\Sharp\Authors;

use App\Models\User;
use App\Sharp\Authors\Commands\InviteUserCommand;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpUploadModelThumbnailUrlTransformer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class AuthorList extends SharpEntityList
{
    public function buildListConfig(): void
    {
        $this->configureSearchable()
            ->configurePrimaryEntityCommand(InviteUserCommand::class)
            ->configureDefaultSort('name');
    }

    protected function getEntityCommands(): ?array
    {
        return [
            InviteUserCommand::class,
        ];
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
            ->setCustomTransformer('avatar', (new SharpUploadModelThumbnailUrlTransformer(100))->renderAsImageTag())
            ->setCustomTransformer('role', function ($value, User $user) {
                return match ($value) {
                    'admin' => 'Admin',
                    'editor' => 'Editor',
                    default => 'Unknown',
                };
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
            )
            ->addField(
                EntityListField::make('role')
                    ->setLabel('Role')
                    ->setSortable(),
            );
    }

    protected function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout
            ->addColumn('avatar', 1)
            ->addColumn('name', 3)
            ->addColumn('email', 4)
            ->addColumn('role', 4);
    }
}
