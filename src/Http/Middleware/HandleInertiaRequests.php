<?php

namespace Code16\Sharp\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'sharp::app';

    public function share(Request $request)
    {
        return array_merge(
            parent::share($request),
            [
            ]
        );
    }
}
