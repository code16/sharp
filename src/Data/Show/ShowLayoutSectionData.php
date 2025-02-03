<?php

namespace Code16\Sharp\Data\Show;

use Code16\Sharp\Data\Data;

/**
 * @internal
 */
final class ShowLayoutSectionData extends Data
{
    public function __construct(
        public ?string $key,
        public string $title,
        public bool $collapsable,
        /** @var ShowLayoutColumnData[] */
        public array $columns
    ) {}

    public static function from(array $section): self
    {
        $section = [
            ...$section,
            'columns' => ShowLayoutColumnData::collection($section['columns']),
        ];

        return new self(...$section);
    }
}
