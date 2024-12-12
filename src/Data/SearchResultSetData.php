<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class SearchResultSetData extends Data
{
    public function __construct(
        public string $label,
        /** @var SearchResultLinkData[] */
        public DataCollection $resultLinks,
        public ?IconData $icon,
        public ?string $emptyStateLabel = null,
        /** @var string[] */
        public array $validationErrors = [],
        public bool $hideWhenEmpty = false,
    ) {}

    public static function from(array $resultSet): self
    {
        $resultSet['resultLinks'] = SearchResultLinkData::collection($resultSet['resultLinks']);
        $resultSet['icon'] = IconData::optional($resultSet['icon']);

        return new self(...$resultSet);
    }
}
