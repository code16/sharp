<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Utils\UploadUtil;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FormUploadController extends Controller
{

    /**
     * @param Request $request
     * @param UploadUtil $uploadUtil
     * @return \Illuminate\Http\JsonResponse
     * @throws FileNotFoundException
     */
    public function store(Request $request, UploadUtil $uploadUtil)
    {
        $file = $request->file('file');

        if (!$file) {
            throw new FileNotFoundException;
        }

        $filename = $uploadUtil->findAvailableName(
            $uploadUtil->getTmpUploadDirectory(),
            $file->getClientOriginalName(),
            "local"
        );

        $file->move($uploadUtil->getTmpUploadDirectory(), $filename);

        return response()->json([
            "name" => $filename
        ]);
    }

}