<?php

namespace Code16\Sharp\Http;

class DashboardController extends SharpProtectedController
{

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view("sharp::dashboard");
    }
}