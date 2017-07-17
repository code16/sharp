<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Routing\Controller;

abstract class ApiController extends Controller
{

    /**
     * @param string $entityKey
     * @return SharpEntityList
     */
    protected function getListInstance(string $entityKey): SharpEntityList
    {
        return app(config("sharp.entities.{$entityKey}.list"));
    }
}