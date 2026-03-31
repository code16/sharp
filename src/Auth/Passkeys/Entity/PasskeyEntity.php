<?php

namespace Code16\Sharp\Auth\Passkeys\Entity;

use Code16\Sharp\Utils\Entities\SharpEntity;

class PasskeyEntity extends SharpEntity
{
    protected ?string $list = PasskeyList::class;
    protected array $prohibitedActions = ['create', 'update'];

    protected function getLabel(): string
    {
        return trans('sharp::auth.passkeys.entity_label');
    }
}
