<?php

namespace Code16\Sharp\Data;

class EntityAuthorizationsData extends Data
{
    public function __construct(
        /** @var array<int|string> */
        public array $view,
        /** @var array<int|string> */
        public array $update,
        public bool $create,
    ) {
    }
}
