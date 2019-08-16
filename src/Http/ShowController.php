<?php

namespace Code16\Sharp\Http;

class ShowController extends SharpProtectedController
{

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\View\View
     */
    public function show($entityKey, $instanceId)
    {
        return view("sharp::show", compact('entityKey', 'instanceId'));
    }
}