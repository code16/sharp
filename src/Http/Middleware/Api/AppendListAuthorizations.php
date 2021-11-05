<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppendListAuthorizations
{
    protected Gate $gate;

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
        $entityKey = $this->determineEntityKey();

        $authorizations = $this->getGlobalAuthorizations($entityKey);

        if ($this->hasPolicyFor($entityKey)) {
            // Have to dig into policies

            if (!isset($authorizations["create"])) {
                // Create doesn't need instanceId
                $authorizations["create"] = $this->gate->check("sharp.{$entityKey}.create");
            }

            // Collect instanceIds from response
            $instanceIdAttribute = $jsonResponse->getData()->config->instanceIdAttribute;
            $instanceIds = collect($jsonResponse->getData()->data->list->items)->pluck($instanceIdAttribute);

            $missingAbilities = array_diff(["view", "update"], array_keys($authorizations));

            foreach ($instanceIds as $instanceId) {
                foreach ($missingAbilities as $missingAbility) {
                    if ($this->gate->check("sharp.{$entityKey}.{$missingAbility}", $instanceId)) {
                        $authorizations[$missingAbility][] = $instanceId;

                    } elseif (!isset($authorizations[$missingAbility])) {
                        $authorizations[$missingAbility] = [];
                    }
                }
            }

            foreach ($missingAbilities as $missingAbility) {
                if (isset($authorizations[$missingAbility]) && sizeof($authorizations[$missingAbility]) == 0) {
                    $authorizations[$missingAbility] = false;
                }
            }
        }

        // All missing abilities are true by default
        $authorizations = array_merge(
            ["view" => true, "create" => true, "update" => true],
            $authorizations
        );

        return tap($jsonResponse, function ($jsonResponse) use ($authorizations) {
            $data = $jsonResponse->getData();
            $data->authorizations = $authorizations;
            $jsonResponse->setData($data);
        });
    }

    protected function hasPolicyFor(string $entityKey): bool
    {
        return config("sharp.entities.{$entityKey}.policy") != null;
    }

    protected function getGlobalAuthorizations(string $entityKey): array
    {
        return config("sharp.entities.{$entityKey}.authorizations", []);
    }

    protected function determineEntityKey(): ?string
    {
        return request()->segment(4);
    }
}