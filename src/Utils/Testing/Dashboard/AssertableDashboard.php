<?php

namespace Code16\Sharp\Utils\Testing\Dashboard;

use Code16\Sharp\Utils\Testing\DelegatesToResponse;
use Code16\Sharp\Utils\Testing\Show\PendingShow;
use Illuminate\Testing\TestResponse;

class AssertableDashboard
{
    use DelegatesToResponse;

    public function __construct(
        protected TestResponse $response,
        protected PendingDashboard $pendingDashboard,
    ) {}

    public function dashboardData(): array
    {
        return $this->pendingDashboard->parent instanceof PendingShow
            ? $this->response->json('data')
            : $this->response->inertiaProps('dashboard.data');
    }
}
