<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class BreadcrumbItemData extends Data
{
    public function __construct(
        public string $label,
        public ?string $documentTitleLabel,
        public string $entityKey,
        public string $url,
    ) {}
}
