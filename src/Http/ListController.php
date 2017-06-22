<?php

namespace Code16\Sharp\Http;

use Illuminate\Routing\Controller;

class ListController extends Controller
{

    /**
     * @param string $entityKey
     * @return \Illuminate\View\View
     */
    public function show($entityKey)
    {
        return view("sharp::list", compact('entityKey'));
    }
}