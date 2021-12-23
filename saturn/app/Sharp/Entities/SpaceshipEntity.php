<?php

namespace App\Sharp\Entities;

use App\Sharp\SpaceshipSharpForm;
use App\Sharp\SpaceshipSharpList;
use App\Sharp\SpaceshipSharpShow;
use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Utils\Entities\SharpEntity;

class SpaceshipEntity extends SharpEntity
{
    protected ?string $list = SpaceshipSharpList::class;
    protected ?string $show = SpaceshipSharpShow::class;
    protected ?string $form = SpaceshipSharpForm::class;
    protected string $label = "Spaceship";

    protected function getPolicy(): string|SharpEntityPolicy|null
    {
        return new class extends SharpEntityPolicy
        {
            public function view($user, $instanceId): bool
            {
                return $instanceId%2 == 0 || $instanceId > 10;
            }

            public function update($user, $instanceId): bool
            {
                return $instanceId%2 == 0;
            }
        };
    }
}