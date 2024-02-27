<?php

namespace Code16\Sharp\Data\Form\Fields;

use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

final class FormUploadFieldValueData extends Data
{
    public function __construct(
        public string $name,
        public string $disk,
        public string $path,
        public int $size,
        public ?string $thumbnail,
        public ?bool $uploaded,
        public ?bool $transformed,
        public ?bool $shouldOptimizeImage,
        public ?bool $transformOriginal,
        #[LiteralTypeScriptType('{
            crop: { width:number, height:number, x:number, y:number },
            rotate: { angle:number }
        } | null')]
        public ?array $filters,
    ) {
    }
}
