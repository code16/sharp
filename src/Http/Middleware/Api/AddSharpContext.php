<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Http\SharpContext;

class AddSharpContext
{

    /**
     * @var SharpContext
     */
    private $context;

    /**
     * @param SharpContext $context
     */
    public function __construct(SharpContext $context)
    {
        $this->context = $context;
    }

    /**
     * Return http 417 on SharpFormException.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $segments = $request->segments();

        if(count($segments) >= 4 && $segments[2] == "form") {
            $this->context->setIsForm();

            if($request->method() == "POST") {
                if(count($segments) == 5) {
                    $this->context->setIsUpdate($segments[4]);

                } else {
                    $this->context->setIsCreation();
                }
            }
        }

        return $next($request);
    }
}