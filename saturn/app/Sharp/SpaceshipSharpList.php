<?php

namespace App\Sharp;

use App\Sharp\Commands\SpaceshipSendMessage;
use App\Sharp\Commands\SpaceshipSynchronize;
use App\Sharp\Filters\SpaceshipPilotsFilter;
use App\Sharp\Filters\SpaceshipTypeFilter;
use App\Sharp\States\SpaceshipEntityState;
use App\Spaceship;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\Eloquent\WithSharpEntityListEloquentTransformer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

class SpaceshipSharpList extends SharpEntityList
{
    use WithSharpEntityListEloquentTransformer;

    function buildListDataContainers()
    {
        $this->addDataContainer(
            EntityListDataContainer::make("picture")

        )->addDataContainer(
            EntityListDataContainer::make("name")
                ->setLabel("Name")
                ->setSortable()

        )->addDataContainer(
            EntityListDataContainer::make("capacity")
                ->setLabel("Capacity")
                ->setSortable()
                ->setHtml(false)

        )->addDataContainer(
            EntityListDataContainer::make("type")
                ->setLabel("Type")
                ->setSortable()

        )->addDataContainer(
            EntityListDataContainer::make("pilots.name")
                ->setLabel("Pilots")
                ->setHtml()
        );
    }

    function buildListConfig()
    {
        $this->setInstanceIdAttribute("id")
            ->setSearchable()
            ->setDefaultSort("name", "asc")
            ->addEntityCommand("synchronize", SpaceshipSynchronize::class)
            ->addInstanceCommand("message", SpaceshipSendMessage::class)
            ->addFilter("type", SpaceshipTypeFilter::class)
            ->addFilter("pilots", SpaceshipPilotsFilter::class)
            ->setEntityState("state", SpaceshipEntityState::class)
            ->setPaginated();
    }

    function buildListLayout()
    {
        $this->addColumn("picture", 1, 2)
            ->addColumn("name", 3, 5)
            ->addColumnLarge("capacity", 2)
            ->addColumn("type", 2, 5)
            ->addColumnLarge("pilots.name", 4);
    }

    function getListData(EntityListQueryParams $params)
    {
        $spaceships = Spaceship::select("spaceships.*")->distinct();

        if($params->specificIds()) {
            $spaceships->whereIn("id", $params->specificIds());
        }

        if($params->sortedBy()) {
            $spaceships->orderBy($params->sortedBy(), $params->sortedDir());
        }

        if($params->filterFor("type")) {
            $spaceships->where("type_id", $params->filterFor("type"));
        }

        if($params->hasSearch() || $params->filterFor("pilots")) {
            $spaceships->select("spaceships.*")
                ->leftJoin("pilot_spaceship", "spaceships.id", "=", "pilot_spaceship.spaceship_id")
                ->leftJoin("pilots", "pilots.id", "=", "pilot_spaceship.pilot_id");

            if ($params->filterFor("pilots")) {
                $spaceships->whereIn("pilots.id", $params->filterFor("pilots"));
            }

            if ($params->hasSearch()) {
                foreach ($params->searchWords() as $word) {
                    $spaceships->where(function ($query) use ($word) {
                        $query->orWhere("spaceships.name", "like", $word)
                            ->orWhere('pilots.name', 'like', $word);
                    });
                }
            }
        }

        return $this->setCustomTransformer("capacity", function($spaceship) {
                return number_format($spaceship->capacity / 1000, 0) . "k";
            })
            ->setCustomTransformer("type", function($spaceship) {
                return $spaceship->type->label;
            })
            ->setCustomTransformer("pilots.name", function($spaceship) {
                return $spaceship->pilots->pluck("name")->implode("<br>");
            })
            ->setUploadTransformer("picture", 100)
            ->transform(
                $spaceships->with("picture", "type", "pilots")
                    ->paginate(10)
            );
    }
}