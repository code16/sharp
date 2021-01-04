<?php

namespace Code16\Sharp\Http;
use Code16\Sharp\Http\Context\CurrentSharpRequest;
use Illuminate\Routing\Controller;

class SharpProtectedController extends Controller
{
    protected CurrentSharpRequest $currentSharpRequest;

    public function __construct()
    {
        $this->middleware('sharp_auth' . (config('sharp.auth.guard') ? ':' . config('sharp.auth.guard') : ''));
        $this->currentSharpRequest = app(CurrentSharpRequest::class);
    }
}