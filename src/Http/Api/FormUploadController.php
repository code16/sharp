<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Utils\FileUtil;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FormUploadController extends Controller
{

    /**
     * @param Request $request
     * @param FileUtil $fileUtil
     * @return \Illuminate\Http\JsonResponse
     * @throws FileNotFoundException
     */
    public function store(Request $request, FileUtil $fileUtil)
    {
        $file = $request->file('file');

        if (!$file) {
            throw new FileNotFoundException;
        }

        $filename = $fileUtil->findAvailableName(
            $file->getClientOriginalName(), '', 'sharp_uploads'
        );

        $file->storeAs('', $filename, 'sharp_uploads');

        return response()->json([
            "name" => $filename
        ]);
    }

}