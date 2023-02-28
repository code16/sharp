<?php

namespace Code16\Sharp\Http;

use Inertia\Inertia;

class ListController extends SharpProtectedController
{
    public function show(string $entityKey)
    {
        return Inertia::render('List', compact('entityKey'));
    }
}
