<?php

namespace Code16\Sharp\Http;

class DashboardController extends SharpProtectedController
{

    /**
     * @param string $dashboardKey
     * @return \Illuminate\View\View
     */
    public function show($dashboardKey)
    {
        return view("sharp::dashboard", compact('dashboardKey'));
    }
}