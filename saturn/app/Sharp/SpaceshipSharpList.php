<?php

namespace App\Sharp;

use App\Pilot;
use App\Sharp\Commands\SpaceshipExternalLink;
use App\Sharp\Commands\SpaceshipPreview;
use App\Sharp\Commands\SpaceshipReload;
use App\Sharp\Commands\SpaceshipSendMessage;
use App\Sharp\Commands\SpaceshipSynchronize;
use App\Sharp\Filters\CorporationGlobalFilter;
use App\Sharp\Filters\SpaceshipPilotsFilter;
use App\Sharp\Filters\SpaceshipTypeFilter;
use App\Sharp\States\SpaceshipEntityState;
use App\Spaceship;
use App\SpaceshipType;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
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

    function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout->addColumn("picture", 1)
            ->addColumn("name", 2)
            ->addColumn("capacity", 2)
            ->addColumn("type:label", 2)
            ->addColumn("pilots")
            ->addColumn("messages_sent_count");
    }

    function buildListLayoutForSmallScreens(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout->addColumn("picture", 2)
            ->addColumn("name")
            ->addColumn("type:label")
            ->addColumn("messages_sent_count", 2);
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
    
    function getFilters(): ?array
    {
        return [
            SpaceshipTypeFilter::class,
            new SpaceshipPilotsFilter()
        ];
    }

    function buildListConfig(): void
    {
        $this->configureInstanceIdAttribute("id")
            ->configureSearchable()
            ->configureDefaultSort("name", "asc")
            ->configureEntityState("state", SpaceshipEntityState::class)
            ->configurePaginated()
            ->configurePageAlert(
                "Here are the spaceships of type <strong>{{type_label}}</strong><span v-if='pilots'> for pilots {{pilots}}</span>",
            );
    }

    function getGlobalMessageData(): ?array
    {
        $pilots = $this->queryParams->filterFor(SpaceshipPilotsFilter::class);
        
        return [
            "type_label" => SpaceshipType::findOrFail($this->queryParams->filterFor(SpaceshipTypeFilter::class))->label,
            "pilots" => $pilots 
                ? Pilot::whereIn("id", (array)$pilots)
                    ->pluck("name")
                    ->implode(", ")
                : null
        ];
    }

    function getListData(): array|Arrayable
    {
        $spaceships = Spaceship::select("spaceships.*")
            ->where("corporation_id", currentSharpRequest()->globalFilterFor(CorporationGlobalFilter::class))
            ->distinct();

        if($this->queryParams->specificIds()) {
            $spaceships->whereIn("id", $this->queryParams->specificIds());
        }

        if($this->queryParams->sortedBy()) {
            $spaceships->orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir());
        }

        if($type = $this->queryParams->filterFor(SpaceshipTypeFilter::class)) {
            $spaceships->where("type_id", $type);
        }

        if($this->queryParams->hasSearch() || $this->queryParams->filterFor(SpaceshipPilotsFilter::class)) {
            $spaceships->leftJoin("pilot_spaceship", "spaceships.id", "=", "pilot_spaceship.spaceship_id")
                ->leftJoin("pilots", "pilots.id", "=", "pilot_spaceship.pilot_id");

            if ($pilots = $this->queryParams->filterFor(SpaceshipPilotsFilter::class)) {
                $spaceships->whereIn("pilots.id", (array)$pilots);
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
