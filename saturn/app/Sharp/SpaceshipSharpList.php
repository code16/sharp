<?php

namespace App\Sharp;

use App\Spaceship;
use Code16\Sharp\EntitiesList\containers\EntitiesListDataContainer;
use Code16\Sharp\EntitiesList\Eloquent\WithSharpEntitiesListEloquentTransformer;
use Code16\Sharp\EntitiesList\EntitiesListQueryParams;
use Code16\Sharp\EntitiesList\SharpEntitiesList;

class SpaceshipSharpList extends SharpEntitiesList
{
    use WithSharpEntitiesListEloquentTransformer;

    function buildListDataContainers()
    {
        $this->addDataContainer(
            EntitiesListDataContainer::make("picture")

        )->addDataContainer(
            EntitiesListDataContainer::make("name")
                ->setLabel("Name")
                ->setSortable()

        )->addDataContainer(
            EntitiesListDataContainer::make("capacity")
                ->setLabel("Capacity")
                ->setSortable()
                ->setHtml(false)

        )->addDataContainer(
            EntitiesListDataContainer::make("type")
                ->setLabel("Type")
                ->setSortable()

        )->addDataContainer(
            EntitiesListDataContainer::make("pilots.name")
                ->setLabel("Pilots")
                ->setHtml()
        );
    }

    function buildListConfig()
    {
        $this->setInstanceIdAttribute("id")
            ->setSearchable()
            ->setDefaultSort("name", "asc")
            ->setPaginated();
    }

    function buildListLayout()
    {
        $this->addColumn("picture", 1, 2)
            ->addColumn("name", 3, 5)
            ->addColumnLarge("capacity", 2)
            ->addColumn("type", 2, 5)
            ->addColumnLarge("pilots", 4);
    }

//    function buildCommands()
//    {
//        $this->addEntityCommand()
//    }

    function getListData(EntitiesListQueryParams $params)
    {
        $spaceships = Spaceship::with("picture", "type", "pilots");

        if($params->hasSearch()) {
            $spaceships->select("spaceships.*")
                ->leftJoin("pilot_spaceship", "spaceships.id", "=", "pilot_spaceship.spaceship_id")
                ->leftJoin("pilots", "pilots.id", "=", "pilot_spaceship.pilot_id");

            foreach($params->searchWords() as $word) {
                $spaceships->where(function ($query) use ($word) {
                    $query->orWhere("spaceships.name", "like", $word)
                        ->orWhere('pilots.name', 'like', $word);
                });
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
                $spaceships->orderBy($params->sortedBy(), $params->sortedDir())
                    ->paginate(10)
            );
    }
}