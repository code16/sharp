<?php

namespace App\Sharp;

use App\Passenger;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

class PassengerSharpList extends SharpEntityList
{

    function buildListDataContainers()
    {
        $this->addDataContainer(
            EntityListDataContainer::make("name")
                ->setSortable()
                ->setLabel("Name")
        )->addDataContainer(
            EntityListDataContainer::make("birth_date")
                ->setSortable()
                ->setLabel("Birth date")
        )->addDataContainer(
            EntityListDataContainer::make("travel")
                ->setSortable()
                ->setLabel("Travel")
        );
    }

    function buildListConfig()
    {
        $this->setSearchable()
            ->setDefaultSort("name", "asc")
            ->setPaginated();
    }

    function buildListLayout()
    {
        $this->addColumn("name", 4)
            ->addColumn("birth_date", 4)
            ->addColumn("travel", 4);
    }

    function getListData(EntityListQueryParams $params)
    {
        $passengers = Passenger::distinct();

        if($params->sortedBy()) {
            $passengers->orderBy($params->sortedBy(), $params->sortedDir());
        }

        if ($params->hasSearch()) {
            foreach ($params->searchWords() as $word) {
                $passengers->where('name', 'like', $word);
            }
        }

        return $this
            ->setCustomTransformer("name", function($value, $passenger) {
                return ($passenger->gender=='M' ? 'Mr' : 'Mrs') . " " . $value;
            })
            ->setCustomTransformer("travel", function($value, $passenger) {
                $travel = $passenger->travel;
                $date = $travel->departure_date->format('Y-m-d (H:i)');

                return $travel->spaceship->name
                    . "<br><em>{$travel->destination}</em>"
                    . "<br><small>{$date}</small>";
            })
            ->transform($passengers->with(["travel", "travel.spaceship"])->paginate(30));
    }
}