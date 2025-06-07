<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\IconData;

/**
 * @internal
 */
final class EntityListSubEntityData extends Data
{
    public function __construct(
        public string $key,
        public string $entityKey,
        public string $label,
        public ?IconData $icon,
        public ?string $formCreateUrl,
    ) {}

    public static function from(array $subEntity): self
    {
        $subEntity['icon'] = IconData::optional($subEntity['icon']);

        return new self(...$subEntity);
    }
}
