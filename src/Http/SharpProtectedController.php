<?php

namespace Code16\Sharp\Http;
use Illuminate\Routing\Controller;

class SharpProtectedController extends Controller
{

    public function __construct()
    {
        $this->middleware('sharp_auth' . (config('sharp.auth.guard') ? ':' . config('sharp.auth.guard') : ''));
    }

}