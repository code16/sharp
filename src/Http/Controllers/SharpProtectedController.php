<?php

namespace Code16\Sharp\Http\Controllers;

use Illuminate\Routing\Controller;

class SharpProtectedController extends Controller
{
    public function __construct()
    {
        $guardSuffix = sharp()->config()->get('auth.guard') ? ':'.sharp()->config()->get('auth.guard') : '';
        $this->middleware('sharp_auth'.$guardSuffix);
    }
}
