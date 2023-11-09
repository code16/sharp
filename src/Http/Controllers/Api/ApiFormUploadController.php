<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Utils\FileUtil;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Routing\Controller;

class ApiFormUploadController extends Controller
{
    public function store(FileUtil $fileUtil)
    {
        throw_if(! request()->hasFile('file'), new FileNotFoundException());

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
        ]);
    }
}
