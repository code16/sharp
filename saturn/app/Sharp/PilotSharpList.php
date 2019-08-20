<?php

namespace App\Sharp;

use App\Pilot;
use App\Sharp\Commands\PilotDownloadPhoto;
use App\Sharp\Commands\PilotUpdateXPCommand;
use App\Sharp\Filters\PilotRoleFilter;
use App\Sharp\Filters\PilotSpaceshipFilter;
use App\Sharp\States\PilotEntityState;
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
        )->addDataContainer(
            EntityListDataContainer::make("role")
                ->setLabel("Role")
        )->addDataContainer(
            EntityListDataContainer::make("xp")
                ->setLabel("Xp")
        );
    }

    function buildListConfig()
    {
        $this->setSearchable()
            ->setDefaultSort("name", "asc")
            ->setMultiformAttribute("role")
            ->setPaginated()
            ->setEntityState("state", PilotEntityState::class)
            ->addFilter("spaceship", PilotSpaceshipFilter::class)
            ->addFilter("role", PilotRoleFilter::class)
            ->addEntityCommand("updateXP", PilotUpdateXPCommand::class)
            ->addInstanceCommand("download", PilotDownloadPhoto::class);
    }

    function buildListLayout()
    {
        $this->addColumn("name", 4)
            ->addColumn("role", 4)
            ->addColumn("xp", 4);
    }

    function getListData(EntityListQueryParams $params)
    {
        $pilots = Pilot::select("pilots.*")->distinct();

        if($ids = $params->specificIds()) {
            $pilots->whereIn("id", $ids);

        } else {
            if ($spaceship = $params->filterFor("spaceship")) {
                $pilots->leftJoin("pilot_spaceship", "pilots.id", "=", "pilot_spaceship.pilot_id")
                    ->leftJoin("spaceships", "spaceships.id", "=", "pilot_spaceship.spaceship_id")
                    ->where("spaceships.id", $spaceship);
            }

            if ($role = $params->filterFor("role")) {
                $pilots->where("role", $role);
            }

            if ($params->sortedBy()) {
                $pilots->orderBy($params->sortedBy(), $params->sortedDir());
            }

            if ($params->hasSearch()) {
                foreach ($params->searchWords() as $word) {
                    $pilots->where('name', 'like', $word);
                }
            }
        }

        return $this
            ->setCustomTransformer("role", function($role, $pilot) {
                return $pilot->role == "sr" ? "senior" : "junior";
            })
            ->setCustomTransformer("xp", function($xp, $pilot) {
                return $pilot->role == "sr" ? $xp . "y" : null;
            })
            ->transform($pilots->paginate(30));
    }
}