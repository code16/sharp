<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Illuminate\Routing\Controller;

class SharpProtectedController extends Controller
{
    protected CurrentSharpRequest $currentSharpRequest;

    public function __construct()
    {
        $guardSuffix = sharpConfig()->get('auth.guard') ? ':'.sharpConfig()->get('auth.guard') : '';
        $this->middleware('sharp_auth'.$guardSuffix);
        $this->currentSharpRequest = app(CurrentSharpRequest::class);
    }
}
