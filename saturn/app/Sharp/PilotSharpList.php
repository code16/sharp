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
use Illuminate\Contracts\Support\Arrayable;

class PilotSharpList extends SharpEntityList
{

    function buildListDataContainers(): void
    {
        $this
            ->addDataContainer(
                EntityListDataContainer::make("name")
                    ->setSortable()
                    ->setLabel("Name")
            )
            ->addDataContainer(
                EntityListDataContainer::make("role")
                    ->setLabel("Role")
            )
            ->addDataContainer(
                EntityListDataContainer::make("xp")
                    ->setLabel("Xp")
            );
    }
    
    public function getInstanceCommands(): ?array
    {
        return [
            PilotDownloadPhoto::class
        ];
    }

    public function getEntityCommands(): ?array
    {
        return [
            PilotUpdateXPCommand::class
        ];
    }

    function buildListConfig(): void
    {
        $this->setSearchable()
            ->setDefaultSort("name", "asc")
            ->setMultiformAttribute("role")
            ->setPaginated()
            ->setEntityState("state", PilotEntityState::class)
            ->addFilter("spaceship", PilotSpaceshipFilter::class)
            ->addFilter("role", PilotRoleFilter::class);
    }

    function buildListLayout(): void
    {
        if($role = $this->queryParams->filterFor("role")) {
            $this->addColumn("name", 6);
            if($role === "sr") {
                $this->addColumn("xp", 6);
            }
        } else {
            $this->addColumn("name", 4)
                ->addColumn("role", 4)
                ->addColumn("xp", 4);
        }
    }

    function getListData(): array|Arrayable
    {
        $pilots = Pilot::select("pilots.*")->distinct();

        if($ids = $this->queryParams->specificIds()) {
            $pilots->whereIn("id", $ids);

        } else {
            if ($spaceship = $this->queryParams->filterFor("spaceship")) {
                $pilots->leftJoin("pilot_spaceship", "pilots.id", "=", "pilot_spaceship.pilot_id")
                    ->leftJoin("spaceships", "spaceships.id", "=", "pilot_spaceship.spaceship_id")
                    ->where("spaceships.id", $spaceship);
            }

            if ($role = $this->queryParams->filterFor("role")) {
                $pilots->where("role", $role);
            }

            if ($this->queryParams->sortedBy()) {
                $pilots->orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir());
            }

            if ($this->queryParams->hasSearch()) {
                foreach ($this->queryParams->searchWords() as $word) {
                    $pilots->where('pilots.name', 'like', $word);
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