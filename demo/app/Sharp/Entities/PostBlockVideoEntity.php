<?php

namespace App\Sharp\Entities;

use App\Sharp\Posts\Blocks\PostBlockVideoForm;

class PostBlockVideoEntity extends PostBlockEntity
{
    protected ?string $form = PostBlockVideoForm::class;
    protected string $label = 'Video block';
}
