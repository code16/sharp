<?php

namespace Code16\Sharp\Http\Api;

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
            "layout" => $dashboard->widgetsLayout(),
            "data" => $dashboard->data(),
        ]);
    }
}