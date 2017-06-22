<?php

namespace Code16\Sharp\Http;

use Illuminate\Routing\Controller;

class FormController extends Controller
{

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\View\View
     */
    public function edit($entityKey, $instanceId)
    {
        return view("sharp::form", compact('entityKey', 'instanceId'));
    }

    /**
     * @param string $entityKey
     * @return \Illuminate\View\View
     */
    public function create($entityKey)
    {
        return view("sharp::form", compact('entityKey'));
    }
}