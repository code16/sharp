<?php

namespace Code16\Sharp\Http\Middleware\Api;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * This middleware appends authorizations for GET form request to the response.
 */
class AppendFormAuthorizations
{
    public function __construct(protected Gate $gate) {}

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        return $response->status() == 200
            ? $this->addAuthorizationsToJsonResponse($response)
            : $response;
    }

    protected function addAuthorizationsToJsonResponse(JsonResponse $jsonResponse): JsonResponse
    {
        list($entityKey, $instanceId) = $this->determineEntityKeyAndInstanceId();

        $authorizations = collect(
            [
                "create" => $this->gate->check("sharp.{$entityKey}.create"),
                "view" => true,
                "update" => true,
                "delete" => true
            ])
            ->when($instanceId, function(Collection $collection) use ($entityKey, $instanceId) {
                return $collection->merge([
                    "view" => $this->gate->check("sharp.{$entityKey}.view", $instanceId),
                    "update" => $this->gate->check("sharp.{$entityKey}.update", $instanceId),
                    "delete" => $this->gate->check("sharp.{$entityKey}.delete", $instanceId)
                ]);
            });
            
        $data = $jsonResponse->getData();
        $data->authorizations = $authorizations->toArray();
        $jsonResponse->setData($data);

        return $jsonResponse;
    }

    protected function determineEntityKeyAndInstanceId(): array
    {
        $entityKey = request()->segment(4);
        $instanceId = request()->segment(5) ?? null;

        if(($pos = strpos($entityKey, ':')) !== false) {
            $entityKey = substr($entityKey, 0, $pos);
        }

        return [$entityKey, $instanceId];
    }
}