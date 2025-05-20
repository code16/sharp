<?php

namespace App\Sharp\Authors;

use App\Models\User;
use App\Sharp\Authors\Commands\InviteUserCommand;
use App\Sharp\Authors\Commands\VisitFacebookProfileCommand;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpUploadModelThumbnailUrlTransformer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class AuthorList extends SharpEntityList
{
    protected function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('avatar')
                    ->setWidth(.1)
                    ->setLabel(''),
            )
            ->addField(
                EntityListField::make('name')
                    ->setWidth(.3)
                    ->setLabel('Name')
                    ->setSortable(),
            )
            ->addField(
                EntityListField::make('email')
                    ->setWidth(.3)
                    ->hideOnSmallScreens()
                    ->setLabel('Email')
                    ->setSortable(),
            )
            ->addField(
                EntityListField::make('role')
                    ->setWidth(.3)
                    ->setLabel('Role'),
            );
    }

    public function buildListConfig(): void
    {
        $this->configureSearchable()
            ->configurePrimaryEntityCommand(InviteUserCommand::class)
            ->configureDefaultSort('name');
    }

    protected function getInstanceCommands(): ?array
    {
        return [
            VisitFacebookProfileCommand::class,
        ];
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
            ->orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir());

        return $this
            ->setCustomTransformer('avatar', (new SharpUploadModelThumbnailUrlTransformer(100))->renderAsImageTag())
            ->setCustomTransformer('role', function ($value) {
                return match ($value) {
                    'admin' => 'Admin',
                    'editor' => 'Editor',
                    default => 'Unknown',
                };
            })
            ->setCustomTransformer('email', function ($value, User $user) {
                return $user->hasVerifiedEmail()
                    ? $value
                    : sprintf(
                        '<div>%s</div><div style="color: darkorange">%s pending invitation...</div>',
                        $value,
                        svg('lucide-mail-x')->toHtml()
                    );
            })
            ->transform($users->get());
    }
}
