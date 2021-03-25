<?php

namespace Code16\Sharp\Http;

class SingleShowController extends SharpProtectedController
{
    public function show(string $entityKey)
    {
        return view("sharp::show", compact('entityKey'));
    }
}