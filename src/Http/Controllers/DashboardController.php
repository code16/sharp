<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Data\Dashboard\DashboardData;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Inertia\Inertia;

class DashboardController extends SharpProtectedController
{
    public function __construct(private SharpEntityManager $entityManager)
    {
        parent::__construct();
    }

    public function show(string $dashboardKey)
    {
        sharp_check_ability('entity', $dashboardKey);

        $dashboard = $this->entityManager->entityFor($dashboardKey)->getViewOrFail();
        $dashboard->buildDashboardConfig();
        $dashboard->initQueryParams();
        $dashboardData = $dashboard->data();

        $data = [
            'widgets' => $dashboard->widgets(),
            'config' => $dashboard->dashboardConfig(),
            'layout' => $dashboard->widgetsLayout(),
            'data' => $dashboardData,
            'pageAlert' => $dashboard->pageAlert($dashboardData),
        ];

        return Inertia::render('Dashboard/Dashboard', [
            'dashboard' => DashboardData::from($data),
        ]);
    }
}
