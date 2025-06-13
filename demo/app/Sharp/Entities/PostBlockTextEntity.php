<?php

namespace App\Sharp\Entities;

use App\Sharp\Posts\Blocks\PostBlockTextForm;

class PostBlockTextEntity extends PostBlockEntity
{
    protected ?string $form = PostBlockTextForm::class;
    protected string $label = 'Text block';
}
