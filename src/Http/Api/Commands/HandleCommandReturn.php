<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Show\SharpShow;
use Illuminate\Support\Facades\Storage;

trait HandleCommandReturn
{

    /**
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\File\Stream
     */
    protected function returnCommandResult(SharpEntityList|SharpShow|SharpDashboard $commandContainer, array $returnedValue)
    {
        if($returnedValue["action"] == "download") {
            // Download case is specific: we return a File Stream
            return Storage::disk($returnedValue["disk"])->download(
                $returnedValue["file"],
                $returnedValue["name"]
            );
        }

        if($returnedValue["action"] == "refresh" && $commandContainer instanceof SharpEntityList) {
            // We have to load and build items from ids
            $returnedValue["items"] = $commandContainer
                ->initWith(
                    EntityListQueryParams::createFromArrayOfIds(
                        $returnedValue["items"]
                    )
                )
                ->data($commandContainer->getListData())["items"];
        }

        return response()->json($returnedValue);
    }

    /**
     * @return \Code16\Sharp\EntityList\Commands\InstanceCommand|null
     * @throws SharpAuthorizationException
     */
    protected function getInstanceCommandHandler(SharpEntityList|SharpShow|SharpDashboard $commandContainer, string $commandKey, mixed $instanceId)
    {
        $commandHandler = $commandContainer->instanceCommandHandler($commandKey);

        if(!$commandHandler->authorize()
            || !$commandHandler->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}