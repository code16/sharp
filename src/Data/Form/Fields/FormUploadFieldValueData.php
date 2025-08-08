<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

/**
 * @internal
 */
final class FormUploadFieldValueData extends Data
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $disk,
        public string $path,
        public string $mime_type,
        public int $size,
        public ?string $thumbnail,
        public ?string $playable_preview_url,
        public ?bool $uploaded,
        public ?bool $transformed,
        public ?bool $not_found,
        public ?bool $exists,
        #[LiteralTypeScriptType('{
            crop: { width:number, height:number, x:number, y:number },
            rotate: { angle:number }
        } | null')]
        public ?array $filters,
        #[Optional]
        #[LiteralTypeScriptType('File')]
        public $nativeFile = null,
    ) {}
}
