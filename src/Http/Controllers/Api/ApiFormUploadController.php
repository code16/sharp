<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Utils\FileUtil;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class ApiFormUploadController extends Controller
{
    use ValidatesRequests;

    public function store(FileUtil $fileUtil)
    {
        $this->validate(request(), [
            'file' => request()->input('rule') ?: ['required', 'file'],
        ]);

        $file = request()->file('file');
        $baseDir = config('sharp.uploads.tmp_dir', 'tmp');
        $baseDisk = config('sharp.uploads.tmp_disk', 'local');

        $filename = $fileUtil->findAvailableName(
            $file->getClientOriginalName(),
            $baseDir,
            $baseDisk
        );

        $file->storeAs($baseDir, $filename, $baseDisk);

        return response()->json([
            'name' => $filename,
            'uploaded' => true,
        ]);
    }
}
