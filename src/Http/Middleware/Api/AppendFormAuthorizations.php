<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * This middleware appends authorizations for GET form request to the response.
 */
class AppendFormAuthorizations
{

    /**
     * @var Gate
     */
    protected $gate;

    /**
     * @param Gate $gate
     */
    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add authorization to the JSON returned
        return $response->status() == 200
            ? $this->addAuthorizationsToJsonResponse($response)
            : $response;
    }

    protected function addAuthorizationsToJsonResponse(JsonResponse $jsonResponse)
    {
        list($entityKey, $instanceId) = $this->determineEntityKeys();

        $policies = [];

        if($this->hasPolicyFor($entityKey)) {
            $policies = [
                "create" => $this->gate->check("sharp.{$entityKey}.create"),
            ];

            if($instanceId) {
                $policies += [
                    "view" => $this->gate->check("sharp.{$entityKey}.view", $instanceId),
                    "update" => $this->gate->check("sharp.{$entityKey}.update", $instanceId),
                    "delete" => $this->gate->check("sharp.{$entityKey}.delete", $instanceId)
                ];
            }
        }

        $globalAuthorizations = $this->getGlobalAuthorizations($entityKey);
        $data = $jsonResponse->getData();

        $data->authorizations = array_merge(
            ["view" => true, "create" => true, "update" => true, "delete" => true],
            $policies,
            (array)$globalAuthorizations
        );

        $jsonResponse->setData($data);

        return $jsonResponse;
    }

    protected function hasPolicyFor($entityKey)
    {
        return config("sharp.entities.{$entityKey}.policy") != null;
    }

    protected function getGlobalAuthorizations(string $entityKey)
    {
        return config("sharp.entities.{$entityKey}.authorizations", []);
    }

    protected function determineEntityKeys()
    {
        list($entityKey, $instanceId) = [
            request()->segment(4),
            request()->segment(5)
        ];

        if(($pos = strpos($entityKey, ':')) !== false) {
            $entityKey = substr($entityKey, 0, $pos);
        }

        return [$entityKey, $instanceId];
    }
}