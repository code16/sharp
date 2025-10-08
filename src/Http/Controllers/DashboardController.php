<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Data\BreadcrumbData;
use Code16\Sharp\Data\Dashboard\DashboardData;
use Inertia\Inertia;

class DashboardController extends SharpProtectedController
{
    public function show(string $dashboardKey)
    {
        $this->authorizationManager->check('entity', $dashboardKey);

        $dashboard = $this->entityManager->entityFor($dashboardKey)->getViewOrFail();
        $dashboard->buildDashboardConfig();
        $dashboard->initQueryParams(request()->all());
        $dashboardData = $dashboard->data();

        $data = [
            'widgets' => $dashboard->widgets(),
            'config' => $dashboard->dashboardConfig(),
            'layout' => $dashboard->widgetsLayout(),
            'data' => $dashboardData,
            'pageAlert' => $dashboard->pageAlert($dashboardData),
            'filterValues' => $dashboard->filterContainer()->getCurrentFilterValuesForFront(request()->all()),
        ];

        if (request()->routeIs('code16.sharp.api.dashboard')) {
            // EmbeddedDashboard case, need to return JSON
            return response()->json(DashboardData::from($data)->toArray());
        }

        return Inertia::render('Dashboard/Dashboard', [
            'dashboard' => DashboardData::from($data),
            'breadcrumb' => BreadcrumbData::from([
                'items' => sharp()->context()->breadcrumb()->allSegments(),
            ]),
        ]);
    }
}
