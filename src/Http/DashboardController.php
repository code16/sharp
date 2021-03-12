<?php

namespace Code16\Sharp\Http;

class DashboardController extends SharpProtectedController
{
    public function show(string $dashboardKey)
    {
        return view("sharp::dashboard", compact('dashboardKey'));
    }
}