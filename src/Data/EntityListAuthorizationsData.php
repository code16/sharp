<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class EntityListAuthorizationsData extends Data
{
    public function __construct(
        /** @var array<int|string> */
        public array $view,
        /** @var array<int|string> */
        public array $delete,
        public bool $create,
        public bool $reorder,
    ) {}
}
