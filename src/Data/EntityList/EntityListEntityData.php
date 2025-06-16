<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\IconData;

/**
 * @internal
 */
final class EntityListEntityData extends Data
{
    public function __construct(
        public string $key,
        public string $entityKey,
        public string $label,
        public ?IconData $icon,
        public ?string $formCreateUrl,
    ) {}

    public static function from(array $listEntity): self
    {
        $listEntity['icon'] = IconData::optional($listEntity['icon']);

        return new self(...$listEntity);
    }
}
