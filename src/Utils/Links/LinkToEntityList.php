<?php

namespace Code16\Sharp\Utils\Links;

use Code16\Sharp\Utils\Filters\Filter;
use Illuminate\Support\Collection;

class LinkToEntityList extends SharpLinkTo
{
    protected array $filters = [];
    protected ?string $searchText = null;
    protected ?string $sortAttribute = null;
    protected ?string $sortDir = null;
    protected ?array $fullQuerystring = null;

    public static function make(string $entityKey): self
    {
        return new static($entityKey);
    }

    public function addFilter(string $filterFullClassNameOrKey, string $value): self
    {
        if (class_exists($filterFullClassNameOrKey)) {
            $key = tap(
                app($filterFullClassNameOrKey),
                function (Filter $filter) {
                    $filter->buildFilterConfig();
                }
            )
                ->getKey();
        } else {
            $key = $filterFullClassNameOrKey;
        }

        $this->filters[$key] = $value;

        return $this;
    }

    public function setSort(string $attribute, string $dir = 'asc'): self
    {
        $this->sortAttribute = $attribute;
        $this->sortDir = $dir;

        return $this;
    }

    public function setSearch(string $searchText): self
    {
        $this->searchText = $searchText;

        return $this;
    }

    public function setFullQuerystring(array $querystring): self
    {
        $this->fullQuerystring = $querystring;

        return $this;
    }

    public function renderAsUrl(): string
    {
        return route(
            'code16.sharp.list',
            array_merge(
                ['entityKey' => $this->entityKey],
                $this->generateQuerystring()
            )
        );
    }

    private function generateQuerystring(): array
    {
        if ($this->fullQuerystring !== null) {
            return $this->fullQuerystring;
        }

        return collect()
            ->when($this->searchText, function (Collection $qs) {
                return $qs->put('search', $this->searchText);
            })
            ->when(count($this->filters), function (Collection $qs) {
                collect($this->filters)
                    ->each(function ($value, $name) use ($qs) {
                        $qs->put("filter_$name", $value);
                    });

                return $qs;
            })
            ->when($this->sortAttribute, function (Collection $qs) {
                $qs->put('sort', $this->sortAttribute);
                $qs->put('dir', $this->sortDir);

                return $qs;
            })
            ->all();
    }
}
