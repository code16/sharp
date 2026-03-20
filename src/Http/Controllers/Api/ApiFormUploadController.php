<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Code16\Sharp\Utils\FileUtil;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ApiFormUploadController extends ApiController
{
    use HandlesFieldContainer;
    use ValidatesRequests;

    public function store(string $globalFilter, EntityKey $entityKey, string $uploadFieldKey, FileUtil $fileUtil)
    {
        $field = $this->getFieldContainer($entityKey)
            ->findFieldByKey($uploadFieldKey);

        if ($field instanceof SharpFormEditorField) {
            $field = $field->uploadsConfig();
        }

        if (! $field) {
            throw new SharpInvalidConfigException('Upload field '.$uploadFieldKey.' was not found in form.');
        }

        $this->validate(request(), [
            'file' => [
                'required',
                ...$field->toArray()['validationRule'],
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
