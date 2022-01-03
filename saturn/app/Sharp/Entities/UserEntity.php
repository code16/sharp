<?php

namespace App\Sharp\Entities;

use App\Sharp\Policies\UserPolicy;
use App\Sharp\UserSharpList;
use Code16\Sharp\Utils\Entities\SharpEntity;

class UserEntity extends SharpEntity
{
    protected ?string $list = UserSharpList::class;
    protected ?string $policy = UserPolicy::class;
    protected string $label = "Sharp user";
    protected array $prohibitedActions = [
        "delete", "create", "update", "view"
    ];
}