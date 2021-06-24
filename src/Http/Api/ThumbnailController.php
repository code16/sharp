<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class ThumbnailController extends Controller
{
    public function show()
    {
        abort_if(!($path = request()->get("path")) || !($disk = request()->get("disk")), 404, "Invalid file name");
        abort_if(!Storage::disk($disk)->exists($path), 404, "File not found");
        
        $model = new SharpUploadModel([
            "file_name" => $path,
            "disk" => $disk
        ]);
        
        $width = request()->get('max_width', 800);
        $height = request()->get('max_height', 800);

        return response()->json([
            "thumbnail" => $model->thumbnail($width, $height),
        ]);
    }
}
