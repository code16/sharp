<?php

namespace App\Sharp\Entities;

use App\Sharp\Posts\Blocks\PostBlockTextForm;
use Code16\Sharp\Utils\Entities\SharpEntity;

class PostBlockTextEntity extends SharpEntity
{
    protected ?string $form = PostBlockTextForm::class;
    protected ?string $icon = 'lucide-text';
    protected string $label = 'Text block';
}
