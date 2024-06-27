<?php

namespace Code16\Sharp\Data;

final class EntityAuthorizationsData extends Data
{
    public function __construct(
        /** @var array<int|string> */
        public array $view,
        /** @var array<int|string> */
        public array $update,
        /** @var array<int|string> */
        public array $delete,
        public bool $create,
    ) {
    }
}
