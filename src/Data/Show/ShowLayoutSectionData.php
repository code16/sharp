<?php

namespace Code16\Sharp\Data\Show;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\InstanceAuthorizationsData;
use Code16\Sharp\Data\NotificationData;


final class ShowLayoutSectionData extends Data
{
    public function __construct(
        public ?string $key,
        public string $title,
        public bool $collapsable,
        /** @var DataCollection<ShowLayoutColumnData> */
        public DataCollection $columns
    ) {
    }

    public static function from(array $section): self
    {
        $section = [
            ...$section,
            'columns' => ShowLayoutColumnData::collection($section['columns'])
        ];

        return new self(...$section);
    }
}
