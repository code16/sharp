<?php

namespace App\Sharp;

use App\Sharp\Commands\ExportUsersCommand;
use App\Sharp\Commands\InviteUserCommand;
use App\User;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

class UserSharpList extends SharpEntityList
{
    use WithCustomTransformers;

    public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('name')
                    ->setLabel('Name')
                    ->setSortable()
            )
            ->addField(
                EntityListField::make('email')
                    ->setLabel('Email')
                    ->setSortable()
            )
            ->addField(
                EntityListField::make('group')
                    ->setLabel('Group')
            );
    }

    public function getEntityCommands(): ?array
    {
        return [
            InviteUserCommand::class,
            ExportUsersCommand::class,
        ];
    }

    public function buildListConfig(): void
    {
        $this->configureInstanceIdAttribute('id')
            ->configurePrimaryEntityCommand(InviteUserCommand::class)
            ->configureDefaultSort('name', 'asc');
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout->addColumn('name', 4)
            ->addColumn('email', 4)
            ->addColumn('group', 4);
    }

    public function getListData(): array
    {
        return $this
            ->setCustomTransformer('group', function ($group) {
                return implode('<br>', explode(',', $group));
            })
            ->transform(
                User::orderBy(
                    $this->queryParams->sortedBy(),
                    $this->queryParams->sortedDir()
                )
                    ->get()
            );
    }
}
