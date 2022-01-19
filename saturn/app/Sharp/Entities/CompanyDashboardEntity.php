<?php

namespace App\Sharp\Entities;

use App\Sharp\CompanyDashboard;
use App\Sharp\Policies\CompanyDashboardPolicy;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;

class CompanyDashboardEntity extends SharpDashboardEntity
{
    protected ?string $view = CompanyDashboard::class;
//    protected ?string $policy = CompanyDashboardLegacyPolicy::class;
    protected ?string $policy = CompanyDashboardPolicy::class;
}
