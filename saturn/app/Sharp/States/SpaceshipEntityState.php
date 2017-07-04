<?php

namespace App\Sharp\States;

use Code16\Sharp\EntitiesList\EntitiesListState;

class SpaceshipEntityState extends EntitiesListState
{

    public function buildStates()
    {
        $this->addState("active", "Active", "green");
        $this->addState("inactive", "Retired", "orange");
        $this->addState("building", "Building process", static::GRAY_COLOR);
        $this->addState("conception", "Conception phase", static::DARKGRAY_COLOR);
    }
}