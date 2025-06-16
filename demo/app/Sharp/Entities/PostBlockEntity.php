<?php

namespace App\Sharp\Entities;

use App\Sharp\Posts\Blocks\PostBlockList;
use App\Sharp\Posts\Blocks\PostBlockPolicy;
use Code16\Sharp\Utils\Entities\SharpEntity;

class PostBlockEntity extends SharpEntity
{
    protected ?string $list = PostBlockList::class;
    protected ?string $policy = PostBlockPolicy::class;
    protected string $label = 'Block';
}
