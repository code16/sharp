<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;

final class FormEditorFieldUploadData extends Data
{
    public function __construct(
        public bool $transformable,
        public ?bool $transformKeepOriginal = null,
        /** @var array<string> */
        public ?array $transformableFileTypes = null,
        public ?int $ratioX = null,
        public ?int $ratioY = null,
        public ?int $maxFileSize = null,
        public string|array|null $fileFilter = null,
    ) {
    }
}
