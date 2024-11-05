<?php

namespace App\Sharp\Entities;

use App\Sharp\Authors\AuthorList;
use Code16\Sharp\Utils\Entities\SharpEntity;

class AuthorEntity extends SharpEntity
{
    protected ?string $list = AuthorList::class;
    protected array $prohibitedActions = ['view', 'update', 'delete', 'create'];
}
