<?php

namespace App\Sharp\Entities;

use App\Sharp\Posts\PostForm;
use App\Sharp\Posts\PostList;
use App\Sharp\Posts\PostPolicy;
use App\Sharp\Posts\PostShow;
use Code16\Sharp\Utils\Entities\SharpEntity;

class PostEntity extends SharpEntity
{
    protected ?string $list = PostList::class;
    protected ?string $show = PostShow::class;
    protected ?string $form = PostForm::class;
    protected ?string $policy = PostPolicy::class;
}
