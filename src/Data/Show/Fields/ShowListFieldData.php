<?php

namespace Code16\Sharp\Data\Show\Fields;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Enums\ShowFieldType;

final class ShowListFieldData extends Data
{
    public function __construct(
        public string $key,
        public ShowFieldType $type,
        public bool $emptyVisible,
        public ?string $label,
        /** @var DataCollection<string,ShowFieldData> */
        public DataCollection $itemFields,
    ) {
    }

    public static function from(array $list): self
    {
        $list = [
            ...$list,
            'itemFields' => ShowFieldData::collection($list['itemFields']),
        ];

        return new self(...$list);
    }
}
