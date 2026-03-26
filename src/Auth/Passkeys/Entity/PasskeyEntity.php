<?php

namespace Code16\Sharp\Auth\Passkeys\Entity;

use Code16\Sharp\Utils\Entities\SharpEntity;

class PasskeyEntity extends SharpEntity
{
    protected ?string $list = PasskeyList::class;
    protected string $label = 'Passkeys';
    protected array $prohibitedActions = ['create', 'update'];
}
