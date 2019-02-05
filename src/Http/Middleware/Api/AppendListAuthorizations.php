<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppendListAuthorizations
{
    /**
     * The gate instance.
     *
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

    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Add authorization to the JSON returned
        return $response->status() == 200
            ? $this->addAuthorizationsToJsonResponse($response)
            : $response;
    }

    /**
     * @param JsonResponse $jsonResponse
     * @return JsonResponse
     */
    protected function addAuthorizationsToJsonResponse(JsonResponse $jsonResponse)
    {
        $entityKey = $this->determineEntityKey();

        $authorizations = $this->getGlobalAuthorizations($entityKey);

        if(sizeof($authorizations) != 3 && $this->hasPolicyFor($entityKey)) {
            // Have to dig into policies

            if(!isset($authorizations["create"])) {
                // Create doesn't need instanceId
                $authorizations["create"] = $this->gate->check("sharp.{$entityKey}.create");
            }

            // Collect instanceIds from response
            $instanceIdAttribute = $jsonResponse->getData()->config->instanceIdAttribute;
            $instanceIds = collect($jsonResponse->getData()->data->items)->pluck($instanceIdAttribute);

            $missingAbilities = array_diff(["view", "update"], array_keys($authorizations));

            foreach($instanceIds as $instanceId) {
                foreach($missingAbilities as $missingAbility) {
                    if($this->gate->check("sharp.{$entityKey}.{$missingAbility}", $instanceId)) {
                        $authorizations[$missingAbility][] = $instanceId;

                    } elseif(!isset($authorizations[$missingAbility])) {
                        $authorizations[$missingAbility] = [];
                    }
                }
            }

            foreach($missingAbilities as $missingAbility) {
                if(isset($authorizations[$missingAbility]) && sizeof($authorizations[$missingAbility]) == 0) {
                    $authorizations[$missingAbility] = false;
                }
            }
        }

        // All missing abilities are true by default
        $authorizations = array_merge(
            ["view" => true, "create" => true, "update" => true],
            $authorizations
        );

        $data = $jsonResponse->getData();

        $data->authorizations = $authorizations;

        $jsonResponse->setData($data);

        return $jsonResponse;
    }

    /**
     * @param $entityKey
     * @return bool
     */
    protected function hasPolicyFor($entityKey)
    {
        return config("sharp.entities.{$entityKey}.policy") != null;
    }

    /**
     * @param string $entityKey
     * @return \Illuminate\Config\Repository|mixed
     */
    protected function getGlobalAuthorizations(string $entityKey)
    {
        return config("sharp.entities.{$entityKey}.authorizations", []);
    }

    /**
     * @return string|null
     */
    protected function determineEntityKey()
    {
        return request()->segment(4);
    }
}