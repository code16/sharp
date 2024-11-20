<?php

namespace Code16\Sharp\Data;

final class SearchResultLinkData extends Data
{
    public function __construct(
        public string $link,
        public string $label,
        public ?string $detail,
    ) {
    }

    public static function from(array $resultLink): self
    {
        return new self(...$resultLink);
    }
}
