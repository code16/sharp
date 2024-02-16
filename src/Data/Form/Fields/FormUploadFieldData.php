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
        public bool $transformable,
        public bool $compactThumbnail,
        public ?bool $transformKeepOriginal = null,
        /** @var array<string> */
        public ?array $transformableFileTypes = null,
        public ?int $ratioX = null,
        public ?int $ratioY = null,
        public ?float $maxFileSize = null,
        /** @var array<string> */
        public array|null $fileFilter = null,
        public ?string $label = null,
        public ?bool $readOnly = null,
        public ?FormConditionalDisplayData $conditionalDisplay = null,
        public ?string $helpMessage = null,
        public ?string $extraStyle = null,
    ) {
    }

    public static function from(array $field): self
    {
        return new self(...$field);
    }
}
