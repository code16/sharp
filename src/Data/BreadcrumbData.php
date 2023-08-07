<?php

namespace Code16\Sharp\Data;


final class BreadcrumbData extends Data
{
    public function __construct(
        /** @var DataCollection<BreadcrumbItemData> */
        public DataCollection $items,
    ) {
    }

    public static function from(array $breadcrumb): self
    {
        return new self(
            items: BreadcrumbItemData::collection($breadcrumb['items']),
        );
    }
}
