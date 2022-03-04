<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Http\Context\CurrentSharpRequest;

class WebDispatchController extends SharpProtectedController
{
    public function index(CurrentSharpRequest $request)
    {
        if ($request->isShow()) {
            return view('sharp::show', [
                'entityKey'  => $request->entityKey(),
                'instanceId' => $request->instanceId(),
            ]);
        }

        if ($request->isForm()) {
            return view('sharp::form', [
                'entityKey'  => $request->entityKey(),
                'instanceId' => $request->instanceId(),
            ]);
        }

        abort(404);
    }
}
