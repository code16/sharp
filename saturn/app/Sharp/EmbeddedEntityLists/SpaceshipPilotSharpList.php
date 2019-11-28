<?php

namespace App\Sharp\EmbeddedEntityLists;

use App\Sharp\PilotSharpList;

class SpaceshipPilotSharpList extends PilotSharpList
{

    function buildListConfig()
    {
        parent::buildListConfig();

        $this->setReorderable(SpaceshipPilotReorderHandler::class);
    }
}