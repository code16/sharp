<?php

namespace Code16\Sharp\Data;


final class EntityStateData extends Data
{
    public function __construct(
        public string $attribute,
        /** @var DataCollection<EntityStateValueData> */
        public DataCollection $values,
        /** @var bool|array<string|int> */
        public mixed $authorization,
    ) {
    }
    
    public static function from(array $state): self
    {
        return new self(
            attribute: $state['attribute'],
            values: EntityStateValueData::collection($state['values']),
            authorization: $state['authorization'],
        );
    }
}
