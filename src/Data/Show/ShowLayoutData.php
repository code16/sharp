<?php

namespace Code16\Sharp\Data\Show;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\InstanceAuthorizationsData;
use Code16\Sharp\Data\NotificationData;

final class ShowLayoutData extends Data
{
    public function __construct(
        /** @var DataCollection<ShowLayoutSectionData> */
        public DataCollection $sections,
    ) {
    }

    public static function from(array $layout): self
    {
        $layout = [
            ...$layout,
            'sections' => ShowLayoutSectionData::collection($layout['sections'])
        ];

        return new self(...$layout);
    }
}
