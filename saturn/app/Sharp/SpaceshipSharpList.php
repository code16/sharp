<?php

namespace App\Sharp;

use App\Sharp\Commands\SpaceshipExternalLink;
use App\Sharp\Commands\SpaceshipPreview;
use App\Sharp\Commands\SpaceshipReload;
use App\Sharp\Commands\SpaceshipSendMessage;
use App\Sharp\Commands\SpaceshipSynchronize;
use App\Sharp\Filters\SpaceshipPilotsFilter;
use App\Sharp\Filters\SpaceshipTypeFilter;
use App\Sharp\States\SpaceshipEntityState;
use App\Spaceship;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\Eloquent\Transformers\SharpUploadModelAttributeTransformer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\LinkToEntity;

class SpaceshipSharpList extends SharpEntityList
{

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

        )->addDataContainer(
            EntityListDataContainer::make("pilots")
                ->setLabel("Pilots")
                ->setHtml()

        )->addDataContainer(
            EntityListDataContainer::make("messages_sent_count")
                ->setLabel("Messages sent")
        );
    }

    function buildListConfig()
    {
        $this->setInstanceIdAttribute("id")
            ->setSearchable()
            ->setDefaultSort("name", "asc")
            ->addFilter("type", SpaceshipTypeFilter::class)
            ->addFilter("pilots", SpaceshipPilotsFilter::class)

            ->addEntityCommand("synchronize", SpaceshipSynchronize::class)
            ->addEntityCommand("reload", SpaceshipReload::class)
            ->addInstanceCommand("message", SpaceshipSendMessage::class)
            ->addInstanceCommand("preview", SpaceshipPreview::class)
            ->addInstanceCommandSeparator()
            ->addInstanceCommand("external", SpaceshipExternalLink::class)
            ->setEntityState("state", SpaceshipEntityState::class)

            ->setPaginated();
    }

    function buildListLayout()
    {
        $this->addColumn("picture", 1, 2)
            ->addColumn("name", 2, 4)
            ->addColumnLarge("capacity", 2)
            ->addColumn("type", 2, 4)
            ->addColumnLarge("pilots", 3)
            ->addColumn("messages_sent_count", 2);
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
            $spaceships->leftJoin("pilot_spaceship", "spaceships.id", "=", "pilot_spaceship.spaceship_id")
                ->leftJoin("pilots", "pilots.id", "=", "pilot_spaceship.pilot_id");

            if ($params->filterFor("pilots")) {
                $spaceships->whereIn("pilots.id", (array)$params->filterFor("pilots"));
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

        return $this
            ->setCustomTransformer("name", function($name, $spaceship) {
                return $spaceship->name;
            })
            ->setCustomTransformer("capacity", function($capacity) {
                return number_format($capacity / 1000, 0) . "k";
            })
            ->setCustomTransformer("type", function($type, $spaceship) {
                return $spaceship->type->label;
            })
            ->setCustomTransformer("pilots", function($pilots, $spaceship) {
                return $spaceship->pilots->map(function($pilot) {
                    return (new LinkToEntity($pilot->name, "pilot"))
                        ->setTooltip("See related pilot")
                        ->setSearch($pilot->name)
                        ->render();
                })->implode("<br>");
            })
            ->setCustomTransformer("picture", new SharpUploadModelAttributeTransformer(100))
            ->transform(
                $spaceships->with("picture", "type", "pilots")
                    ->paginate(10, ["spaceships.*"])
            );
    }
}