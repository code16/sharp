<?php

namespace App\Sharp\Entities;

use App\Sharp\Posts\Blocks\PostBlockVisualsForm;
use Code16\Sharp\Utils\Entities\SharpEntity;

class PostBlockVisualsEntity extends SharpEntity
{
    protected ?string $form = PostBlockVisualsForm::class;
    protected ?string $icon = 'lucide-images';
    protected string $label = 'Visual block';
}
