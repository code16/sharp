<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\EntityStateData;
use Code16\Sharp\Data\Filters\FilterData;

final class EntityListConfigData extends Data
{
    public function __construct(
        public string $instanceIdAttribute,
        public ?string $multiformAttribute,
        public bool $searchable,
        public bool $paginated,
        public bool $reorderable,
        public ?string $defaultSort,
        public ?string $defaultSortDir,
        public bool $hasShowPage,
        public string $deleteConfirmationText,
        public bool $deleteHidden,
        /** @var array<array<FilterData>> */
        public array $filters,
        public EntityStateData $state,
    ) {
    }
}
