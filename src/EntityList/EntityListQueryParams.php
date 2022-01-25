<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\Utils\Filters\HasFiltersInQuery;

class EntityListQueryParams
{
    use HasFiltersInQuery;

    protected ?int $page;
    protected ?string $search = null;
    protected ?string $sortedBy = null;
    protected ?string $sortedDir = null;
    protected array $specificIds = [];

    public static function create(): static
    {
        return new static;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setDefaultSort(?string $defaultSortedBy, ?string $defaultSortedDir): self
    {
        $this->sortedBy = $defaultSortedBy;
        $this->sortedDir = $defaultSortedDir;

        return $this;
    }

    public function fillWithRequest(string $queryPrefix = null): self
    {
        $query = $queryPrefix ? request($queryPrefix) : request()->all();

        $this->search = $query['search'] ?? null ? urldecode($query['search']) : null;
        $this->page = $query['page'] ?? null;

        if (isset($query['sort'])) {
            $this->sortedBy = $query['sort'];
            $this->sortedDir = $query['dir'];
        }

        $this->fillFilterWithRequest($query);

        return $this;
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
        $terms = [];

        foreach (explode(' ', $this->search) as $term) {
            $term = trim($term);
            if (! $term) {
                continue;
            }

            if ($isLike) {
                if ($handleStar && str_contains($term, '*')) {
                    $terms[] = str_replace('*', '%', $term);
                    continue;
                }

                $terms[] = $noStarTermPrefix.$term.$noStarTermSuffix;
                continue;
            }

            $terms[] = $term;
        }

        return $terms;
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
