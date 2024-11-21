<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Show\SharpShow;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait HandlesInstanceCommand
{

    protected function getInstanceCommandHandler(
        SharpEntityList|SharpShow $commandContainer,
        string $commandKey,
        mixed $instanceId
    ): ?InstanceCommand {
        $commandHandler = $commandContainer->findInstanceCommandHandler($commandKey);
        $commandHandler->buildCommandConfig();

        if (! $commandHandler->authorize() || ! $commandHandler->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}
