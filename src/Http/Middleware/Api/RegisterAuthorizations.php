<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\Request;

class RegisterAuthorizations
{
    public function __construct(protected SharpEntityManager $entityManager, protected Gate $gate) {}

    public function handle(Request $request, Closure $next)
    {
        collect(array_keys(config("sharp.entities")))
            ->merge(array_keys(config("sharp.dashboards")))
            ->each(function(string $entityKey) {
                $policy = $this->entityManager->entityFor($entityKey)->getPolicyOrDefault()::class;
                foreach(['entity', 'view', 'update', 'create', 'delete'] as $action) {
                    \Illuminate\Support\Facades\Gate::define("sharp.{$entityKey}.{$action}", "{$policy}@{$action}");
                }
            });
        
        return $next($request);
    }
}