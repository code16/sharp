<?php

namespace Code16\Sharp\Http\Api;

/** TODO legacy to remove, replaced by @see \Code16\Sharp\Http\DashboardController */
class DashboardController extends ApiController
{
    public function show(string $dashboardKey)
    {
        sharp_check_ability('entity', $dashboardKey);

        if (! $dashboard = $this->getDashboardInstance($dashboardKey)) {
            abort(404, 'Dashboard not found');
        }

        $dashboard->buildDashboardConfig();
        $dashboard->init();

        return response()->json([
            'widgets' => $dashboard->widgets(),
            'config' => $dashboard->dashboardConfig(),
            'layout' => $dashboard->widgetsLayout(),
            'data' => $dashboard->data(),
            'fields' => $dashboard->dashboardMetaFields(),
        ]);
    }
}
