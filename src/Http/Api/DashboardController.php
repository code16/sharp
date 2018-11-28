<?php

namespace Code16\Sharp\Http\Api;

class DashboardController extends ApiController
{

    /**
     * @param $dashboardKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($dashboardKey = null)
    {
        $dashboard = $this->getDashboardInstance($dashboardKey ?: "company_dashboard");

        if(!$dashboard) {
            abort(404, "Dashboard not found");
        }

        return response()->json([
            "widgets" => $dashboard->widgets(),
            "layout" => $dashboard->widgetsLayout(),
            "data" => $dashboard->data(),
        ]);
    }
}