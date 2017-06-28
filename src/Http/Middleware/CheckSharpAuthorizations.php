<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Exceptions\Auth\SharpAuthenticationException;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckSharpAuthorizations
{
    /**
     * The authentication factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * The gate instance.
     *
     * @var Gate
     */
    protected $gate;

    /**
     * @param Auth $auth
     * @param Gate $gate
     */
    public function __construct(Auth $auth, Gate $gate)
    {
        $this->auth = $auth;
        $this->gate = $gate;
    }

    public function handle(Request $request, Closure $next)
    {
        if(!$this->auth->guard($this->getSharpGuard())->check()) {
            $this->forbid();
        }

        list($entityKey, $instanceId) = $this->determineEntityKeys($request);
        $ability = $this->determineAbility($request);
        $globalAuthorizations = $this->getGlobalAuthorizations($entityKey);

        // Check global authorization
        if (isset($globalAuthorizations[$ability]) && !$globalAuthorizations[$ability]) {
            // Forbidden
            $this->deny();
        }

        // Check policy authorization
        if($this->hasPolicyFor($entityKey)) {
            $this->gate->authorize("sharp.{$entityKey}.{$ability}", $instanceId);
        }

        if($request->wantsJson()) {
            // Add authorization to the JSON returned
            $response = $next($request);

            return $response->status() == 200
                ? $this->addAuthorizationsToJsonResponse(
                    $response, $globalAuthorizations, $entityKey, $instanceId
                )
                : $response;
        }

        return $next($request);
    }

    protected function getGlobalAuthorizations(string $entityKey)
    {
        return config("sharp.entities.{$entityKey}.authorizations");
    }

    protected function getSharpGuard()
    {
        return config("sharp.auth.guard", config("auth.defaults.guard"));
    }

    protected function forbid()
    {
        throw new SharpAuthenticationException("Unauthenticated user");
    }

    protected function deny()
    {
        throw new SharpAuthorizationException("Unauthorized action");
    }

    protected function determineAbility(Request $request)
    {
        switch($request->route()->getName()) {
            case "code16.sharp.api.list":
            case "code16.sharp.list":
                return "view";

            case "code16.sharp.api.form.create":
            case "code16.sharp.api.form.store":
            case "code16.sharp.create":
                return "create";

            case "code16.sharp.api.form.edit":
            case "code16.sharp.api.form.update":
            case "code16.sharp.edit":
                return "update";

            case "code16.sharp.api.form.delete":
                return "delete";
        }

        return null;
    }

    protected function determineEntityKeys(Request $request)
    {
        if(str_contains($request->route()->getName(), '.api.')) {
            // Api route, eg: code16.sharp.api.form.update
            return [$request->segment(4), $request->segment(5)];
        }

        // Web route, eg: code16.sharp.form
        return [$request->segment(3), $request->segment(4)];
    }

    protected function addAuthorizationsToJsonResponse(
        JsonResponse $jsonResponse, $globalAuthorizations, $entityKey, $instanceId
    )
    {
        $policies = [];
        if($this->hasPolicyFor($entityKey)) {
            $policies = [
                "view" => $this->gate->check("sharp.{$entityKey}.view", $instanceId),
                "create" => $this->gate->check("sharp.{$entityKey}.create"),
                "update" => $this->gate->check("sharp.{$entityKey}.update", $instanceId),
                "delete" => $this->gate->check("sharp.{$entityKey}.delete", $instanceId)
            ];
        }

        $data = $jsonResponse->getData();

        $data->authorizations = array_merge(
            ["view" => true, "create" => true, "update" => true, "delete" => true],
            $policies,
            (array)$globalAuthorizations
        );

        $jsonResponse->setData($data);

        return $jsonResponse;
    }

    private function hasPolicyFor($entityKey)
    {
        return config("sharp.entities.{$entityKey}.policy") != null;
    }

}