<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\FormLayoutData;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class FormEditorFieldUploadData extends Data
{
    public function __construct(
        #[TypeScriptType([
            'file' => FormUploadFieldData::class,
            'legend' => FormTextFieldData::class.'|null',
        ])]
        public array $fields,
        public FormLayoutData $layout,
    ) {}
}
