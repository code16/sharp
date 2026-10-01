<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Commands\Returns\CommandRefreshReturn;
use Code16\Sharp\Commands\Returns\CommandReloadReturn;
use Code16\Sharp\Commands\Returns\CommandReturn;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Http\Controllers\HandlesEntityListItems;
use Code16\Sharp\Show\SharpShow;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HandlesCommandResult
{
    use HandlesEntityListItems;

    protected function returnCommandResult(
        SharpEntityList|SharpShow|SharpDashboard $commandContainer,
        string $entityKey,
        CommandReturn $commandReturn,
        ?array $additionalData = null
    ): StreamedResponse|JsonResponse {
        if ($commandReturn instanceof Responsable) {
            return $commandReturn->toResponse(request());
        }

        if ($commandReturn instanceof CommandRefreshReturn && $commandContainer instanceof SharpDashboard) {
            // Refresh has no meaning in the Dashboard; we just do a classic reload.
            $commandReturn = new CommandReloadReturn();
        }

        $returnedValue = [
            ...$commandReturn->toArray(),
            ...$additionalData ?? [],
        ];

        if ($commandReturn instanceof CommandRefreshReturn && $commandContainer instanceof SharpEntityList) {
            // We have to load and build items from ids
            $returnedValue['items'] = $this->addMetaToItems(
                $commandContainer
                    ->updateQueryParamsWithSpecificIds($commandReturn->getItems())
                    ->data()['items'],
                $entityKey,
                $commandContainer,
            );
        }

        return response()->json($returnedValue);
    }
}
