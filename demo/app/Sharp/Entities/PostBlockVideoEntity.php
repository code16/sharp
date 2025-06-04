<?php

namespace App\Sharp\Entities;

use App\Sharp\Posts\Blocks\PostBlockVideoForm;
use Code16\Sharp\Utils\Entities\SharpEntity;

class PostBlockVideoEntity extends SharpEntity
{
    protected ?string $form = PostBlockVideoForm::class;
    protected ?string $icon = 'lucide-video';
    protected string $label = 'Video block';
}
