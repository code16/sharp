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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $segments = $request->segments();

        if(count($segments) > 3) {
            if(count($segments) == 7 && $segments[3] == "download") {
                $this->context->setEntityKey($segments[5]);
                $this->context->setIsUpdate($segments[6] ?? null);

            } else {
                $this->context->setEntityKey($segments[3]);

                if ($segments[2] == "form") {
                    $this->context->setIsForm();

                    if (count($segments) == 5) {
                        $this->context->setIsUpdate($segments[4] ?? null);

                    } else {
                        $this->context->setIsCreation();
                    }

                } elseif ($segments[2] == "show") {
                    $this->context->setIsShow($segments[4] ?? null);

                } elseif ($segments[2] == "list") {
                    $this->context->setIsEntityList();

                } elseif ($segments[2] == "dashboard") {
                    $this->context->setIsDashboard();
                }
            }
        }

        return $next($request);
    }
}