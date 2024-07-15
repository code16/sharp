<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Controllers\Api\ApiController;

class ApiEntityListEntityStateController extends ApiController
{
    use HandlesCommandReturn;

    public function update(string $entityKey, mixed $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams(request()->input('query'));

        if (! $list->entityStateHandler()->authorize()
            || ! $list->entityStateHandler()->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $this->returnCommandResult(
            $list,
            array_merge(
                $list->entityStateHandler()->execute($instanceId, request()->only('value')),
                ['value' => request('value')],
            ),
        );
    }
}
