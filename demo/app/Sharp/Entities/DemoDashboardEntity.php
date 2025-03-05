<?php

namespace App\Sharp\Entities;

use App\Sharp\Dashboard\DemoDashboard;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;

class DemoDashboardEntity extends SharpDashboardEntity
{
    public static string $entityKey = 'dashboard';
    protected ?string $view = DemoDashboard::class;
}
