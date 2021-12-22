<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppendListAuthorizations
{
    public function __construct(protected Gate $gate) {}

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

        $authorizations["create"] = $this->gate->check("sharp.{$entityKey}.create");

        // Collect instanceIds from response
        collect($jsonResponse->getData()->data->list->items)
            ->pluck($jsonResponse->getData()->config->instanceIdAttribute)
            ->each(function($instanceId) use (&$authorizations, $entityKey) {
                if($this->gate->check("sharp.{$entityKey}.view", $instanceId)) {
                    $authorizations["view"][] = $instanceId;
                }
                if($this->gate->check("sharp.{$entityKey}.update", $instanceId)) {
                    $authorizations["update"][] = $instanceId;
                }
            });

        return tap($jsonResponse, function ($jsonResponse) use ($authorizations) {
            $data = $jsonResponse->getData();
            $data->authorizations = $authorizations;
            $jsonResponse->setData($data);
        });
    }
    
    protected function determineEntityKey(): ?string
    {
        return request()->segment(4);
    }
}