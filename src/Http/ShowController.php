<?php

namespace Code16\Sharp\Http;

class ShowController extends SharpProtectedController
{

    /**
     * @param string $entityKey
     * @param string|null $instanceId
     * @return \Illuminate\View\View
     */
    public function show($entityKey, $instanceId = null)
    {
        return view("sharp::show", compact('entityKey', 'instanceId'));
    }
}