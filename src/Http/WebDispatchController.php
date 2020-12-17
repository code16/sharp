<?php

namespace Code16\Sharp\Http;

class WebDispatchController extends SharpProtectedController
{
    public function index()
    {
        for($segments=request()->segments(), $k=count($segments)-1; $k>0; $k--) {
            switch($segments[$k]) {
                case "s-show":
                    return view("sharp::show", [
                        "entityKey" => $segments[$k+1] ?? null,
                        "instanceId" => $segments[$k+2] ?? null,
                    ]);
                case "s-form":
                    return view("sharp::form", [
                        "entityKey" => $segments[$k+1] ?? null,
                        "instanceId" => $segments[$k+2] ?? null,
                    ]);
            }
        }
        
        abort(404);
    }
}