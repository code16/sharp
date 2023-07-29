<?php

namespace Code16\Sharp\Data;


final class EntityStateData extends Data
{
    public function __construct(
        public string $attribute,
        /** @var DataCollection<EntityStateValueData> */
        public DataCollection $values,
        /** @var bool|array<string|int> */
        public $authorization,
    ) {
    }
}
