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

    public static function create()
    {
        return new static;
    }

    /**
     * @param string $defaultSortedBy
     * @param string $defaultSortedDir
     * @return $this
     */
    public function setDefaultSort($defaultSortedBy, $defaultSortedDir)
    {
        $this->sortedBy = $defaultSortedBy;
        $this->sortedDir = $defaultSortedDir;

        return $this;
    }

    /**
     * @param array $filters
     * @return $this
     */
    public function setDefaultFilters($filters)
    {
        $this->filters = (array)$filters;

        return $this;
    }

    /**
     * @return $this
     */
    public function fillWithRequest()
    {
        $this->search = request("search");
        $this->page = request("page");

        if(request("sort")) {
            $this->sortedBy = request("sort");
            $this->sortedDir = request("dir");
        }

        collect(request()->except(["search", "page", "sort", "dir"]))
            ->filter(function($value, $name) {
                return starts_with($name, "filter_");

            })->each(function($value, $name) {
               $this->filters[substr($name, strlen("filter_"))] = $value;
            });

        return $this;
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
        if(!isset($this->filters[$filterName])) {
            return null;
        }

        return str_contains($this->filters[$filterName], ",")
            ? explode(",", $this->filters[$filterName])
            : $this->filters[$filterName];
    }

    /**
     * @return array
     */
    public function specificIds()
    {
        return (array)$this->specificIds;
    }

}