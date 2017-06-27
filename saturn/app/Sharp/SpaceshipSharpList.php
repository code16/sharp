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
            EntitiesListDataContainer::make("pilots")
                ->setLabel("Pilots")
                ->setHtml()
        );
    }

    function buildListConfig()
    {
        $this->setInstanceIdAttribute("id")
            ->setSearchable()
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

//    function commands()
//    {
//    }

    function getListData(EntitiesListQueryParams $params)
    {
        return $this->setCustomTransformer("capacity", function($spaceship) {
                return number_format($spaceship->capacity / 1000, 0) . "k";
            })
            ->setCustomTransformer("type", function($spaceship) {
                return $spaceship->type->label;
            })
            ->setCustomTransformer("pilots", function($spaceship) {
                return $spaceship->pilots->pluck("name")->implode("<br>");
            })
            ->setUploadTransformer("picture", 100)
            ->transform(
                Spaceship::with("picture", "type", "pilots")->paginate(10)
            );
    }
}