<?php

namespace App\Sharp;

use App\Sharp\Commands\ExportUsersCommand;
use App\Sharp\Commands\InviteUserCommand;
use App\User;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

class UserSharpList extends SharpEntityList
{
    use WithCustomTransformers;

    function buildListDataContainers(): void
    {
        $this
            ->addDataContainer(
                EntityListDataContainer::make("name")
                    ->setLabel("Name")
                    ->setSortable()
            )
            ->addDataContainer(
                EntityListDataContainer::make("email")
                    ->setLabel("Email")
                    ->setSortable()
            )
            ->addDataContainer(
                EntityListDataContainer::make("group")
                    ->setLabel("Group")
            );
    }

    function buildListConfig(): void
    {
        $this->setInstanceIdAttribute("id")
            ->setPrimaryEntityCommand("invite_new_user", InviteUserCommand::class)
            ->addEntityCommand("export_users", ExportUsersCommand::class)
            ->setDefaultSort("name", "asc");
    }

    function buildListLayout(): void
    {
        $this->addColumn("name", 4)
            ->addColumn("email", 4)
            ->addColumn("group", 4);
    }

    function getListData(EntityListQueryParams $params)
    {
        return $this
            ->setCustomTransformer("group", function($group) {
                return implode("<br>", explode(",", $group));
            })
            ->transform(
                User::orderBy($params->sortedBy(), $params->sortedDir())->get()
            );
    }
}