<?php

namespace App\Sharp;

use App\Spaceship;
use Code16\Sharp\EntitiesList\SharpEntitiesList;

class SpaceshipSharpList extends SharpEntitiesList
{

    function buildListDataContainers()
    {
        $this->addDataContainer(
            SharpListDataContainer::make("picture")
                ->setLabel("")
                ->setRenderer(SharpUploadListRenderer::class, ["width" => 100])

        )->addDataContainer(
            SharpListDataContainer::make("name")
                ->setLabel("Name")
                ->setSortable()

        )->addDataContainer(
            SharpListDataContainer::make("capacity")
                ->setLabel("Capacity")
                ->setSortable()
                ->setRenderer(function($spaceship) {
                    return number_format($spaceship->capacity / 1000, 0) . "k";
                })

        )->addDataContainer(
            SharpListDataContainer::make("type")
                ->setLabel("Type")
                ->setSortable()
                ->setRenderer(function($spaceship) {
                    return $spaceship->type->label;
                })

        )->addDataContainer(
            SharpListDataContainer::make("pilots")
                ->setLabel("Pilots")
                ->setHtml()
                ->setRenderer(function($spaceship) {
                    return $spaceship->pilots->pluck("name")->implode("<br>");
                })
        );
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

    function getListData(SharpListQueryParams $params)
    {
        return $this->transform(
            Spaceship::with("picture", "type", "pilots")->get()
        );
    }
}