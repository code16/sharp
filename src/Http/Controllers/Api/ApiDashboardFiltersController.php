<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Http\Controllers\Controller;

class ApiDashboardFiltersController extends Controller
{
    public function store(string $globalFilter, string $dashboardKey)
    {
        $this->authorizationManager->check('entity', $dashboardKey);

        $dashboard = $this->entityManager->entityFor($dashboardKey)->getViewOrFail();
        $dashboard->buildDashboardConfig();

        $dashboard->filterContainer()
            ->putRetainedFilterValuesInSession(
                collect(request()->input('filterValues', []))
                    ->diffKeys(request()->input('hiddenFilters') ?? [])
                    ->toArray()
            );

        return redirect()->route('code16.sharp.api.dashboard', [
            'dashboardKey' => $dashboardKey,
            ...(request()->input('query') ?? []),
            ...$dashboard->filterContainer()->getQueryParamsFromFilterValues([
                ...request()->input('filterValues', []),
                ...(request()->input('hiddenFilters') ?? []),
            ]),
        ]);
    }
}
