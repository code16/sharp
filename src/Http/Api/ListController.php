<?php

namespace Code16\Sharp\Http\Api;

use Illuminate\Routing\Controller;

class ListController extends Controller
{

    /**
     * @param string $entityKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($entityKey)
    {
        $list = $this->getListInstance($entityKey);

        return response()->json([
            "columns" => $list->columns(),
            "layout" => $list->listLayout(),
            "data" => $list->data()
        ]);
    }

    /**
     * @param string $entityKey
     * @return SharpList
     */
    protected function getListInstance(string $entityKey): SharpList
    {
        return app(config("sharp.entities.{$entityKey}.list"));
    }
}