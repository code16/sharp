<?php

namespace Code16\Sharp\Data\Show\Fields;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\ShowFieldType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

/**
 * @internal
 */
final class ShowListFieldData extends Data
{
    #[Optional]
    #[LiteralTypeScriptType('Array<{ [key: string]: ShowFieldData["value"] }>')]
    public array $value;

    public function __construct(
        public string $key,
        #[LiteralTypeScriptType('"'.ShowFieldType::List->value.'"')]
        public ShowFieldType $type,
        public bool $emptyVisible,
        public ?string $label,
        /** @var array<string,ShowFieldData> */
        public array $itemFields,
    ) {}

    public static function from(array $list): self
    {
        $list = [
            ...$list,
            'itemFields' => ShowFieldData::collection($list['itemFields']),
        ];

        return new self(...$list);
    }
}
