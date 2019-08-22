<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;

class StoreBreadcrumb
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(
            $request->is('sharp/list/*')
            || $request->is('sharp/dashboard/*')
            || ($request->is('sharp/show/*') && !$request->is('sharp/show/*/*'))
        ) {
            // List, Dashboard, SingleShow: reset breadcrumb
            session()->put("sharp_breadcrumb", [$request->fullUrl()]);

        } elseif(
            $request->is('sharp/show/*/*')
            || $request->is('sharp/form/*/*')
        ) {
            // Show, Form: append to breadcrumb
            session()->push("sharp_breadcrumb", $request->fullUrl());
        }

        return $next($request);
    }
}