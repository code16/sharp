<?php

namespace App\Sharp;

use App\Passenger;
use App\Sharp\Filters\PassengerBirthdateFilter;
use App\Sharp\Filters\PassengerTravelFilter;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

class PassengerSharpList extends SharpEntityList
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
                EntityListDataContainer::make("birth_date")
                    ->setSortable()
                    ->setLabel("Birth date")
            )
            ->addDataContainer(
                EntityListDataContainer::make("travel")
                    ->setSortable()
                    ->setLabel("Travel")
            );
    }

    function buildListConfig(): void
    {
        $this->setSearchable()
            ->setDefaultSort("name", "asc")
            ->addFilter("travel", PassengerTravelFilter::class)
            ->addFilter("birthdate", PassengerBirthdateFilter::class)
            ->setPaginated();
    }

    function buildListLayout(): void
    {
        $this->addColumn("name", 4)
            ->addColumn("birth_date", 4)
            ->addColumn("travel", 4);
    }

    function getListData(): array|Arrayable
    {
        $passengers = Passenger::query();

        if($this->queryParams->sortedBy()) {
            $passengers->orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir());
        }

        if($travelFilter = $this->queryParams->filterFor("travel")) {
            $passengers->where("travel_id", $travelFilter);
        }

        if($birthdateFilter = $this->queryParams->filterFor("birthdate")) {
            $passengers->whereBetween("birth_date", [
                $birthdateFilter['start'],
                $birthdateFilter['end'],
            ]);
        }

        if ($this->queryParams->hasSearch()) {
            foreach ($this->queryParams->searchWords() as $word) {
                $passengers->where('name', 'like', $word);
            }
        }

        return $this
            ->setCustomTransformer("name", function($value, $passenger) {
                return ($passenger->gender=='M' ? 'Mr' : 'Mrs') . " " . $value;
            })
            ->setCustomTransformer("travel", function($value, $passenger) {
                $travel = $passenger->travel;

                if(!$travel) {
                    return "";
                }

                $date = $travel->departure_date->format('Y-m-d (H:i)');

                return '<i class="fas fa-space-shuttle"></i> ' 
                    . ($travel->spaceship->name ?? "")
                    . "<br><em>{$travel->destination}</em>"
                    . "<br><small>{$date}</small>";
            })
            ->transform($passengers->with(["travel", "travel.spaceship"])->paginate(30));
    }
}