<?php

namespace Code16\Sharp\Http;

class ListController extends SharpProtectedController
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