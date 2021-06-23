<?php

namespace Code16\Sharp\Http\Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ThumbnailController extends Controller
{
    public function show()
    {
        abort_if(!($path = request()->get("path")) || !($disk = request()->get("disk")), 404, "Invalid file name");
        abort_if(!Storage::disk($disk)->exists($path), 404, "File not found");
//        
//        $image = ->get(request()->get("path"));
//        
    }
}