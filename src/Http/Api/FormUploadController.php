<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Utils\FileUtil;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Routing\Controller;

class FormUploadController extends Controller
{
    public function store(FileUtil $fileUtil)
    {
        if (! $file = request()->file('file')) {
            throw new FileNotFoundException;
        }

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
