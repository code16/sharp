<?php

namespace Code16\Sharp\Http;

class ListController extends SharpProtectedController
{

    public function show(string $entityKey)
    {
        return view("sharp::list", compact('entityKey'));
    }
}