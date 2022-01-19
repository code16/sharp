<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Form\Validator\SharpValidator;

class BindSharpValidationResolver
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        app()->validator->resolver(function ($translator, $data, $rules, $messages) {
            return new SharpValidator($translator, $data, $rules, $messages);
        });

        return $next($request);
    }
}
