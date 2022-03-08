<?php

namespace App\Sharp\Entities;

use App\Sharp\Dashboard\DemoDashboard;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;

class DemoDashboardEntity extends SharpDashboardEntity
{
    protected ?string $view = DemoDashboard::class;
}
