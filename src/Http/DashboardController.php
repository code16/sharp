<?php

namespace Code16\Sharp\Http;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view("sharp::dashboard");
    }
}