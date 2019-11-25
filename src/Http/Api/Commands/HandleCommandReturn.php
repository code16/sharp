<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\EntityList\Traits\HandleCommands;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Illuminate\Support\Facades\Storage;

trait HandleCommandReturn
{

    /**
     * @param HandleCommands $commandContainer
     * @param array $returnedValue
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\File\Stream
     */
    protected function returnCommandResult($commandContainer, array $returnedValue)
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
            $returnedValue["items"] = $commandContainer->data(
                $commandContainer->getListData(
                    EntityListQueryParams::createFromArrayOfIds(
                        $returnedValue["items"]
                    )
                )
            )["items"];
        }

        return response()->json($returnedValue);
    }

    /**
     * @param HandleCommands $commandContainer
     * @param string $commandKey
     * @param $instanceId
     * @return \Code16\Sharp\EntityList\Commands\InstanceCommand|null
     * @throws SharpAuthorizationException
     */
    protected function getInstanceCommandHandler($commandContainer, $commandKey, $instanceId)
    {
        $commandHandler = $commandContainer->instanceCommandHandler($commandKey);

        if(!$commandHandler->authorize()
            || !$commandHandler->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}