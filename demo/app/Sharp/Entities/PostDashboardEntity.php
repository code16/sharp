<?php

namespace App\Sharp\Entities;

use App\Sharp\Dashboard\PostDashboard;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;

class PostDashboardEntity extends SharpDashboardEntity
{
    protected ?string $view = PostDashboard::class;
}
