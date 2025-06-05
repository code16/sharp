<?php

namespace App\Sharp\Entities;

use App\Sharp\Posts\Blocks\PostBlockVisualsForm;
use App\Sharp\Posts\Blocks\PostBlockVisualsShow;

class PostBlockVisualsEntity extends PostBlockEntity
{
    protected ?string $form = PostBlockVisualsForm::class;
    protected ?string $show = PostBlockVisualsShow::class;
    protected ?string $icon = 'lucide-images';
    protected string $label = 'Visual block';
}
