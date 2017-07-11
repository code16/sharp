<?php

namespace Code16\Sharp\Http\Api\Traits;

use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

trait HandleCommandReturn
{

    protected function returnAsJson(SharpEntityList $list, array $returnedValue)
    {
        if($returnedValue["action"] == "refresh") {
            // We have to load and build items from ids
            $returnedValue["items"] = $list->getListData(
                EntityListQueryParams::createFromArrayOfIds(
                    $returnedValue["items"]
                )
            );
        }

        return response()->json(
            $returnedValue
        );
    }
}