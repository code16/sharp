<?php

namespace Code16\Sharp\Http\Controllers;

class DashboardFiltersController extends SharpProtectedController
{
    public function store(string $globalFilter, string $dashboardKey)
    {
        $this->authorizationManager->check('entity', $dashboardKey);

        $dashboard = $this->entityManager->entityFor($dashboardKey)->getViewOrFail();
        $dashboard->buildDashboardConfig();

        $dashboard->filterContainer()->putRetainedFilterValuesInSession(request()->input('filterValues'));

        return redirect()->route('code16.sharp.dashboard', [
            'dashboardKey' => $dashboardKey,
            ...(request()->input('query') ?? []),
            ...$dashboard->filterContainer()->getQueryParamsFromFilterValues(request()->input('filterValues', [])),
        ]);
    }
}
