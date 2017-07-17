<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Dashboard\SharpDashboard;

class DashboardController extends ApiController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $dashboard = $this->getDashboardInstance();

        if(!$dashboard) {
            return response()->json("", 404);
        }

        return response()->json([
            "widgets" => $dashboard->widgets(),
//            "layout" => $dashboard->widgetsLayout(),
//            "data" => $dashboard->data(),
        ]);
    }

    /**
     * @return SharpDashboard|null
     */
    protected function getDashboardInstance()
    {
        $dashboardClass = config("sharp.dashboard");

        return $dashboardClass ? app($dashboardClass) : null;
    }
}