<?php

namespace App\Sharp;

use App\Passenger;
use App\Sharp\Filters\PassengerBirthdateFilter;
use App\Sharp\Filters\PassengerTravelFilter;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

class PassengerSharpList extends SharpEntityList
{
    function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make("name")
                    ->setSortable()
                    ->setLabel("Name")
            )
            ->addField(
                EntityListField::make("birth_date")
                    ->setSortable()
                    ->setLabel("Birth date")
            )
            ->addField(
                EntityListField::make("travel")
                    ->setSortable()
                    ->setLabel("Travel")
            );
    }
    
    public function getFilters(): ?array
    {
        return [
            PassengerTravelFilter::class,
            PassengerBirthdateFilter::class
        ];
    }

    function buildListConfig(): void
    {
        $this->configureSearchable()
            ->configureDefaultSort("name", "asc")
            ->configurePaginated();
    }

    function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout->addColumn("name", 4)
            ->addColumn("birth_date", 4)
            ->addColumn("travel", 4);
    }

    function getListData(): array|Arrayable
    {
        $passengers = Passenger::query();

        if($this->queryParams->sortedBy()) {
            $passengers->orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir());
        }

        if($travelFilter = $this->queryParams->filterFor(PassengerTravelFilter::class)) {
            $passengers->where("travel_id", $travelFilter);
        }

        if($birthdateFilter = $this->queryParams->filterFor(PassengerBirthdateFilter::class)) {
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