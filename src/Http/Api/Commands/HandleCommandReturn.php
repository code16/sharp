<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

trait HandleCommandReturn
{

    /**
     * @param SharpEntityList $list
     * @param array $returnedValue
     * @return \Illuminate\Http\JsonResponse
     */
    protected function returnAsJson(SharpEntityList $list, array $returnedValue)
    {
        if($returnedValue["action"] == "refresh") {
            // We have to load and build items from ids
            $returnedValue["items"] = $list->data(
                $list->getListData(
                    EntityListQueryParams::createFromArrayOfIds(
                        $returnedValue["items"]
                    )
                )
            )["items"];
        }

        return response()->json(
            $returnedValue
        );
    }
}