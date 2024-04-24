<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Data\Dashboard\DashboardData;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Inertia\Inertia;

class DashboardFiltersController extends SharpProtectedController
{
    public function __construct(
        readonly private SharpEntityManager $entityManager
    ) {
        parent::__construct();
    }

    public function store(string $dashboardKey)
    {
        sharp_check_ability('entity', $dashboardKey);

        $dashboard = $this->entityManager->entityFor($dashboardKey)->getViewOrFail();
        $dashboard->buildDashboardConfig();
        
        $dashboard->filterContainer()->putRetainedFilterValuesInSession(request()->input('filterValues'));
        
        return redirect()->route('code16.sharp.dashboard', [
            'dashboardKey' => $dashboardKey,
            ...request()->input('query', []),
            ...$dashboard->filterContainer()->getQueryParamsFromFilterValues(request()->input('filterValues', [])),
        ]);
    }
}
