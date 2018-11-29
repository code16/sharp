<?php

namespace Code16\Sharp\Http\Api;

class DashboardController extends ApiController
{

    /**
     * @param $dashboardKey
     * @return \Illuminate\Http\JsonResponse
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     */
    public function show($dashboardKey)
    {
        sharp_check_ability("view", $dashboardKey);

        $dashboard = $this->getDashboardInstance($dashboardKey);

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