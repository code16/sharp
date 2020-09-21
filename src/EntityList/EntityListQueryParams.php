<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\Utils\Filters\HasFiltersInQuery;

class EntityListQueryParams
{
    use HasFiltersInQuery;

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
    protected $specificIds;

    /**
     * @return static
     */
    public static function create()
    {
        return new static;
    }

    /**
     * @return int|null
     */
    public function getPage()
    {
        return $this->page;
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
     * @param string|null $queryPrefix
     * @return $this
     */
    public function fillWithRequest(string $queryPrefix = null)
    {
        $query = $queryPrefix ? request($queryPrefix) : request()->all();

        $this->search = $query["search"] ?? null ? urldecode($query["search"]) : null;
        $this->page = $query["page"] ?? null;

        if(isset($query["sort"])) {
            $this->sortedBy = $query["sort"];
            $this->sortedDir = $query["dir"];
        }

        $this->fillFilterWithRequest($query);

        return $this;
    }

    /**
     * @param array $ids
     * @return static
     */
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
     * @return array
     */
    public function specificIds()
    {
        return (array)$this->specificIds;
    }
}
