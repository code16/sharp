<?php

namespace Code16\Sharp\Data\EntityList;


use Code16\Sharp\Data\Data;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;


#[LiteralTypeScriptType('{
    list: {
        items: Array<{ [key: string]: any }>;
        page?: number;
        pageSize?: number;
        totalCount?: number;
    }
}')]
final class EntityListDataData extends Data
{
    public function __construct(
        public array $data,
    ) {
    }

    public static function from(array $data): self
    {
        return new self($data);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
