<?php

namespace Code16\Sharp\Data;

final class BreadcrumbItemData extends Data
{
    public function __construct(
        public string $type,
        public string $label,
        public ?string $documentTitleLabel,
        public string $entityKey,
        public string $url,
    ) {}
}
