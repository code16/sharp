<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class BreadcrumbData extends Data
{
    public function __construct(
        /** @var BreadcrumbItemData[] */
        public array $items,
    ) {}

    public static function from(array $breadcrumb): self
    {
        return new self(
            items: BreadcrumbItemData::collection($breadcrumb['items']),
        );
    }
}
