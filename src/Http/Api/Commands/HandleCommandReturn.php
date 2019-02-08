<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Support\Facades\Storage;

trait HandleCommandReturn
{

    /**
     * @param SharpEntityList|SharpDashboard $commandContainer
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
}