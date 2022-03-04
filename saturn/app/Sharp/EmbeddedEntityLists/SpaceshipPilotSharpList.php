<?php

namespace App\Sharp\EmbeddedEntityLists;

use App\Sharp\PilotSharpList;

class SpaceshipPilotSharpList extends PilotSharpList
{
    public function buildListConfig(): void
    {
        parent::buildListConfig();

        $this->setReorderable(SpaceshipPilotReorderHandler::class);
    }
}
