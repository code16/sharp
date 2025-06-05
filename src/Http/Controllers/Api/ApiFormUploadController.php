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
            'validation_rule' => ['nullable', 'array'],
            'validation_rule.*' => [
                'string',
                'regex:/^(file$|image:?|mimes:|mimetypes:|extensions:|dimensions:|size:|between:|min:|max:)/',
            ],
        ]);

        $this->validate(request(), [
            'file' => [
                'required',
                ...request()->input('validation_rule') ?? ['file'],
            ],
        ]);

        $file = request()->file('file');
        $baseDir = sharp()->config()->get('uploads.tmp_dir');
        $baseDisk = sharp()->config()->get('uploads.tmp_disk');

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
