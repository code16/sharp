<?php

namespace App\Sharp;

use App\Pilot;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

class PilotSharpList extends SharpEntityList
{

    function buildListDataContainers()
    {
        $this->addDataContainer(
            EntityListDataContainer::make("name")
                ->setSortable()
                ->setLabel("Name")
        );
    }

    function buildListConfig()
    {
        $this->setSearchable()
            ->setDefaultSort("name", "asc")
            ->setMultiformAttribute("role")
            ->setPaginated();
    }

    function buildListLayout()
    {
        $this->addColumn("name", 12);
    }

    function getListData(EntityListQueryParams $params)
    {
        $pilots = Pilot::distinct();

        if($params->sortedBy()) {
            $pilots->orderBy($params->sortedBy(), $params->sortedDir());
        }

        if ($params->hasSearch()) {
            foreach ($params->searchWords() as $word) {
                $pilots->where('name', 'like', $word);
            }
        }

        return $this
            ->setCustomTransformer("role", function($role, $pilot) {
                return $pilot->role == "sr" ? "senior" : "junior";
            })
            ->transform($pilots->paginate(30));
    }
}