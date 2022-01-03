<?php

namespace App\Sharp\Entities;

use App\Sharp\EmbeddedEntityLists\SpaceshipPilotSharpList;

class SpaceshipPilotEntity extends PilotEntity
{
    protected ?string $list = SpaceshipPilotSharpList::class;
}