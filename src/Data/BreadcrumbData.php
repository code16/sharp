<?php

namespace Code16\Sharp\Data;


final class BreadcrumbData extends Data
{
    public function __construct(
        /** @var DataCollection<BreadcrumbItemData> */
        public DataCollection $items,
        public bool $visible,
    ) {
    }
    
    public static function from(array $breadcrumb): self
    {
        return new self(
            items: BreadcrumbItemData::collection($breadcrumb['items']),
            visible: $breadcrumb['visible'],
        );
    }
}
