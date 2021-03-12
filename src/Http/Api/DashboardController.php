<?php

namespace Code16\Sharp\Http\Api;

class DashboardController extends ApiController
{
    public function show(string $dashboardKey)
    {
        sharp_check_ability("view", $dashboardKey);

        if(!$dashboard = $this->getDashboardInstance($dashboardKey)) {
            abort(404, "Dashboard not found");
        }

        $dashboard->buildDashboardConfig();

        return response()->json([
            "widgets" => $dashboard->widgets(),
            "config" => $dashboard->dashboardConfig(),
            "layout" => $dashboard->widgetsLayout(),
            "data" => $dashboard->data(),
        ]);
    }
}