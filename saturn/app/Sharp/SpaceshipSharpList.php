<?php

namespace App\Sharp;

use App\Pilot;
use App\Sharp\Commands\SpaceshipExternalLink;
use App\Sharp\Commands\SpaceshipPreview;
use App\Sharp\Commands\SpaceshipReload;
use App\Sharp\Commands\SpaceshipSendMessage;
use App\Sharp\Commands\SpaceshipSynchronize;
use App\Sharp\Filters\SpaceshipPilotsFilter;
use App\Sharp\Filters\SpaceshipTypeFilter;
use App\Sharp\States\SpaceshipEntityState;
use App\Spaceship;
use App\SpaceshipType;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpUploadModelThumbnailUrlTransformer;
use Illuminate\Contracts\Support\Arrayable;

class SpaceshipSharpList extends SharpEntityList
{
    function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make("picture")
            )
            ->addField(
                EntityListField::make("name")
                    ->setLabel("Name")
                    ->setSortable()
            )
            ->addField(
                EntityListField::make("capacity")
                    ->setLabel("Capacity")
                    ->setSortable()
                    ->setHtml(false)
            )
            ->addField(
                EntityListField::make("type:label")
                    ->setLabel("Type")
            )
            ->addField(
                EntityListField::make("pilots")
                    ->setLabel("Pilots")
                    ->setHtml()
            )
            ->addField(
                EntityListField::make("messages_sent_count")
                    ->setLabel("Messages sent")
        );
    }
    
    function getEntityCommands(): ?array
    {
        return [
            "synchronize" => new SpaceshipSynchronize(),
            SpaceshipReload::class
        ];
    }

    function getInstanceCommands(): ?array
    {
        return [
            SpaceshipSendMessage::class,
            new SpaceshipPreview(),
            "---",
            SpaceshipExternalLink::class
        ];
    }
    
//    function getFilters(): ?array
//    {
//        return [
//            "type" => SpaceshipTypeFilter::class,
//            new SpaceshipPilotsFilter()
//        ];
//    }

    function buildListConfig(): void
    {
        $this->configureInstanceIdAttribute("id")
            ->configureSearchable()
            ->configureDefaultSort("name", "asc")
            ->addFilter("type", SpaceshipTypeFilter::class)
            ->addFilter("pilots", SpaceshipPilotsFilter::class)
            ->configureEntityState("state", SpaceshipEntityState::class)
            ->configurePaginated()
            ->configureGlobalMessage(
                "Here are the spaceships of type <strong>{{type_label}}</strong><span v-if='pilots'>for pilots {{pilots}}</span>",
            );
    }

    function buildListLayout(): void
    {
        $this->addColumn("picture", 1)
            ->addColumn("name", 2)
            ->addColumn("capacity", 2)
            ->addColumn("type:label", 2)
            ->addColumn("pilots")
            ->addColumn("messages_sent_count");
    }

    function buildListLayoutForSmallScreens(): void
    {
        $this->addColumn("picture", 2)
            ->addColumn("name")
            ->addColumn("type:label")
            ->addColumn("messages_sent_count", 2);
    }
    
    function getGlobalMessageData(): ?array
    {
        $pilots = $this->queryParams->filterFor('pilots');
        
        return [
            "type_label" => SpaceshipType::findOrFail($this->queryParams->filterFor('type'))->label,
            "pilots" => $pilots 
                ? Pilot::whereIn($pilots)
                    ->pluck("name")
                    ->implode(", ")
                : null
        ];
    }

    function getListData(): array|Arrayable
    {
        $spaceships = Spaceship::select("spaceships.*")
            ->where("corporation_id", currentSharpRequest()->globalFilterFor("corporation"))
            ->distinct();

        if($this->queryParams->specificIds()) {
            $spaceships->whereIn("id", $this->queryParams->specificIds());
        }

        if($this->queryParams->sortedBy()) {
            $spaceships->orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir());
        }

        if($this->queryParams->filterFor("type")) {
            $spaceships->where("type_id", $this->queryParams->filterFor("type"));
        }

        if($this->queryParams->hasSearch() || $this->queryParams->filterFor("pilots")) {
            $spaceships->leftJoin("pilot_spaceship", "spaceships.id", "=", "pilot_spaceship.spaceship_id")
                ->leftJoin("pilots", "pilots.id", "=", "pilot_spaceship.pilot_id");

            if ($this->queryParams->filterFor("pilots")) {
                $spaceships->whereIn("pilots.id", (array)$this->queryParams->filterFor("pilots"));
            }

            if ($this->queryParams->hasSearch()) {
                foreach ($this->queryParams->searchWords() as $word) {
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
            ->setCustomTransformer("pilots", function($pilots, $spaceship) {
                return $spaceship->pilots
                    ->map(function($pilot) {
                        return LinkToEntityList::make("pilot")
                            ->setSearch($pilot->name)
                            ->setTooltip("See related pilot")
                            ->renderAsText($pilot->name);
                    })
                    ->implode("<br>");
            })
            ->setCustomTransformer("picture", (new SharpUploadModelThumbnailUrlTransformer(100))->renderAsImageTag())
            ->transform(
                $spaceships->with("picture", "type", "pilots")
                    ->paginate(10, ["spaceships.*"])
            );
    }
}
