<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\Form\Fields\Common\FormConditionalDisplayData;
use Code16\Sharp\Enums\FormFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class FormUploadFieldData extends Data
{
    public ?FormUploadFieldValueData $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.FormFieldType::Upload->value.'"')]
        public FormFieldType $type,
        #[LiteralTypeScriptType('[number, number]')]
        public ?array $imageCropRatio = null,
        public bool $imageTransformable = true,
        public bool $imageCompactThumbnail = false,
        public ?bool $imageTransformKeepOriginal = null,
        /** @var array<string> */
        public ?array $imageTransformableFileTypes = null,
        /** @var array<string> */
        public ?array $allowedExtensions = null,
        public ?int $maxFileSize = null,
        /** @var array<string> */
        public ?array $validationRule = null,
        public ?string $storageBasePath = null,
        public ?string $storageDisk = null,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?FormConditionalDisplayData $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
    ) {}

    public static function from(array $field): self
    {
        if (is_string($field['type'])) {
            $field['type'] = FormFieldType::from($field['type']);
        }

        return new self(...$field);
    }
}
