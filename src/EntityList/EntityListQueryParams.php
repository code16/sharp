<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\Filters\Concerns\HasFiltersInQuery;
use Code16\Sharp\Filters\FilterContainer\FilterContainer;
use Code16\Sharp\Utils\StringUtil;

class EntityListQueryParams
{
    use HasFiltersInQuery;

    public function __construct(
        protected FilterContainer $filterContainer,
        protected array $filterValues = [],
        protected ?string $sortedBy = null,
        protected ?string $sortedDir = null,
        protected ?int $page = null,
        protected ?string $search = null,
        protected array $specificIds = [],
    ) {}

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function hasSearch(): bool
    {
        return $this->search !== null && strlen(trim($this->search)) > 0;
    }

    public function sortedBy(): ?string
    {
        return $this->sortedBy;
    }

    public function sortedDir(): ?string
    {
        return $this->sortedDir;
    }

    public function searchWords(bool $isLike = true, bool $handleStar = true, string $noStarTermPrefix = '%', string $noStarTermSuffix = '%'): array
    {
        return app(StringUtil::class)
            ->explodeSearchTerms($this->search, $isLike, $handleStar, $noStarTermPrefix, $noStarTermSuffix)
            ->all();
    }

    final public function setSpecificIds(array $specificIds): self
    {
        $this->specificIds = $specificIds;

        return $this;
    }

    public function specificIds(): array
    {
        return $this->specificIds ?? [];
    }
}
