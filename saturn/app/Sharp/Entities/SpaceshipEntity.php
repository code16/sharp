<?php

namespace App\Sharp\Entities;

use App\Sharp\Policies\SpaceshipPolicy;
use App\Sharp\SpaceshipSharpForm;
use App\Sharp\SpaceshipSharpList;
use App\Sharp\SpaceshipSharpShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class SpaceshipEntity extends SharpEntity
{
    protected ?string $list = SpaceshipSharpList::class;
    protected ?string $show = SpaceshipSharpShow::class;
    protected ?string $form = SpaceshipSharpForm::class;
    protected ?string $policy = SpaceshipPolicy::class;
    protected string $label = "Spaceship";
}