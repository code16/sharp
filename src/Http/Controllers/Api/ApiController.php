<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Http\Controllers\SharpProtectedController;
use Code16\Sharp\Show\SharpShow;

abstract class ApiController extends SharpProtectedController
{
    protected function getListInstance(string $entityKey): SharpEntityList
    {
        return $this->entityManager->entityFor($entityKey)->getListOrFail();
    }

    protected function getShowInstance(string $entityKey): SharpShow
    {
        return $this->entityManager->entityFor($entityKey)->getShowOrFail();
    }

    protected function getDashboardInstance(string $dashboardKey): ?SharpDashboard
    {
        return $this->entityManager->entityFor($dashboardKey)->getViewOrFail();
    }
}
