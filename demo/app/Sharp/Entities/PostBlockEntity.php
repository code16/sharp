<?php

namespace App\Sharp\Entities;

use App\Sharp\Posts\Blocks\PostBlockList;
use App\Sharp\Posts\Blocks\PostBlockPolicy;
use App\Sharp\Posts\Blocks\PostBlockTextForm;
use App\Sharp\Posts\Blocks\PostBlockVideoForm;
use App\Sharp\Posts\Blocks\PostBlockVisualsForm;
use Code16\Sharp\Utils\Entities\SharpEntity;

class PostBlockEntity extends SharpEntity
{
    protected ?string $list = PostBlockList::class;
    protected ?string $policy = PostBlockPolicy::class;
    protected string $label = 'Block';

    public function getMultiforms(): array
    {
        return [
            'text' => [PostBlockTextForm::class, 'Text'],
            'visuals' => [PostBlockVisualsForm::class, 'Visuals'],
            'video' => [PostBlockVideoForm::class, 'Video'],
        ];
    }
}
