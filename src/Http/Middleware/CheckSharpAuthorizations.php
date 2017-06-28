<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
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

    public function handle(Request $request, Closure $next, $guard = null)
    {
//        $this->auth->authenticate();

        $entityKey = $request->segment(4);
        $globalAuthorizations = $this->getGlobalAuthorizations($entityKey);
        $ability = $this->determineAbility($request);

        if(isset($globalAuthorizations[$ability]) && !$globalAuthorizations[$ability]) {
            // Forbidden
            $this->deny();
        }
//            $this->gate->authorize("sharp.{$ability}", $request->segment(5));

        return $this->addAuthorizationsToResponse($next($request), $globalAuthorizations);
    }

    private function getGlobalAuthorizations(string $entityKey)
    {
        return config("sharp.entities.{$entityKey}.authorizations");
    }

    private function deny()
    {
        throw new AuthorizationException("Unauthorized action");
    }

    private function determineAbility(Request $request)
    {
        switch($request->route()->getName()) {
            case "code16.sharp.api.list":
                return "view";

            case "code16.sharp.api.form.create":
            case "code16.sharp.api.form.store":
                return "create";

            case "code16.sharp.api.form.edit":
            case "code16.sharp.api.form.update":
                return "update";

            case "code16.sharp.api.form.delete":
                return "delete";
        }

        return null;
    }

    private function addAuthorizationsToResponse(JsonResponse $jsonResponse, $globalAuthorizations)
    {
        $data = $jsonResponse->getData();

        $data->authorizations = array_merge(
            ["view" => true, "create" => true, "update" => true, "delete" => true],
            (array)$globalAuthorizations
        );

        $jsonResponse->setData($data);

        return $jsonResponse;
    }

}