<?php

namespace Code16\Sharp\Data\Show;

use Code16\Sharp\Data\Data;

/**
 * @internal
 */
final class ShowLayoutData extends Data
{
    public function __construct(
        /** @var ShowLayoutSectionData[] */
        public array $sections,
    ) {}

    public static function from(array $layout): self
    {
        $layout = [
            ...$layout,
            'sections' => ShowLayoutSectionData::collection($layout['sections']),
        ];

        return new self(...$layout);
    }
}
