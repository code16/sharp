<?php

namespace App\Sharp;

use App\User;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

class UserSharpList extends SharpEntityList
{
    use WithCustomTransformers;

    function buildListDataContainers()
    {
        $this->addDataContainer(
            EntityListDataContainer::make("name")
                ->setLabel("Name")
                ->setSortable()

        )->addDataContainer(
            EntityListDataContainer::make("email")
                ->setLabel("Email")
                ->setSortable()

        )->addDataContainer(
            EntityListDataContainer::make("group")
                ->setLabel("Group")
        );
    }

    function buildListConfig()
    {
        $this->setInstanceIdAttribute("id")
            ->setDefaultSort("name", "asc");
    }

    function buildListLayout()
    {
        $this->addColumn("name", 4)
            ->addColumn("email", 4)
            ->addColumn("group", 4);
    }

    function getListData(EntityListQueryParams $params)
    {
        return $this->setCustomTransformer("group", function($user) {
            return implode("<br>", explode(",", $user->group));
        })
        ->transform(
            User::orderBy($params->sortedBy(), $params->sortedDir())->get()
        );
    }
}