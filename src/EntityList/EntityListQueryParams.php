<?php

namespace Code16\Sharp\EntityList;

class EntityListQueryParams
{
    /**
     * @var int
     */
    protected $page;

    /**
     * @var string
     */
    protected $search;

    /**
     * @var string
     */
    protected $sortedBy;

    /**
     * @var string
     */
    protected $sortedDir;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var array
     */
    protected $specificIds;

    /**
     * @param string|null $defaultSortedBy
     * @param string|null $defaultSortedDir
     * @return static
     */
    public static function createFromRequest($defaultSortedBy = null, $defaultSortedDir = null)
    {
        $instance = new static;
        $instance->search = request("search");
        $instance->page = request("page");
        $instance->sortedBy = request("sort") ?: $defaultSortedBy;
        $instance->sortedDir = request("dir") ?: $defaultSortedDir;

        collect(request()->except(["search", "page", "sort", "dir"]))
            ->filter(function($value, $name) {
                return starts_with($name, "filter_");

            })->each(function($value, $name) use($instance) {
               $instance->filters[substr($name, strlen("filter_"))] = $value;
            });

        return $instance;
    }

    public static function createFromArrayOfIds(array $ids)
    {
        $instance = new static;
        $instance->specificIds = $ids;

        return $instance;
    }

    /**
     * @return bool
     */
    public function hasSearch()
    {
        return strlen(trim($this->search)) > 0;
    }

    /**
     * @return string
     */
    public function sortedBy()
    {
        return $this->sortedBy;
    }

    /**
     * @return string
     */
    public function sortedDir()
    {
        return $this->sortedDir;
    }

    /**
     * @param bool $isLike
     * @param bool $handleStar
     * @param string $noStarTermPrefix
     * @param string $noStarTermSuffix
     * @return array
     */
    public function searchWords($isLike = true, $handleStar = true, $noStarTermPrefix = '%', $noStarTermSuffix = '%')
    {
        $terms = [];

        foreach (explode(" ", $this->search) as $term) {
            $term = trim($term);
            if (!$term) {
                continue;
            }

            if ($isLike) {
                if ($handleStar && strpos($term, '*') !== false) {
                    $terms[] = str_replace('*', '%', $term);
                    continue;
                }

                $terms[] = $noStarTermPrefix . $term . $noStarTermSuffix;
                continue;
            }

            $terms[] = $term;
        }

        return $terms;
    }

    /**
     * @param string $filterName
     * @return mixed|null
     */
    public function filterFor(string $filterName)
    {
        return $this->filters[$filterName] ?? null;
    }

    /**
     * @return array
     */
    public function specificIds()
    {
        return (array)$this->specificIds;
    }

}