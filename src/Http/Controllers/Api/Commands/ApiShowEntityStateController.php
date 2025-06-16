<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Show\SharpSingleShow;

class ApiShowEntityStateController extends ApiController
{
    use HandlesCommandResult;
    use HandlesInstanceCommand;

    public function update(string $entityKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowPage($entityKey, $instanceId);
        $stateHandler = $showPage->entityStateHandler();

        if (! $stateHandler->authorize()
            || ! $stateHandler->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $this->returnCommandResult(
            $showPage,
            $entityKey,
            array_merge(
                $stateHandler->execute($instanceId, request()->only('value')),
                ['value' => request('value')],
            ),
        );
    }

    private function getShowPage(string $entityKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowInstance($entityKey);

        abort_if(
            (! $instanceId && ! $showPage instanceof SharpSingleShow)
            || ($instanceId && $showPage instanceof SharpSingleShow),
            404,
        );

        $showPage->buildShowConfig();

        return $showPage;
    }
}
