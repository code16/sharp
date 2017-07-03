<?php

namespace Code16\Sharp\EntitiesList;

use Code16\Sharp\EntitiesList\Containers\EntitiesListDataContainer;
use Code16\Sharp\EntitiesList\Layout\EntitiesListLayoutColumn;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class SharpEntitiesList
{
    /**
     * @var array
     */
    protected $containers = [];

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var bool
     */
    protected $listBuilt = false;

    /**
     * @var bool
     */
    protected $layoutBuilt = false;

    /**
     * @var string
     */
    protected $instanceIdAttribute = "id";

    /**
     * @var string
     */
    protected $displayMode = "list";

    /**
     * @var bool
     */
    protected $searchable = false;

    /**
     * @var bool
     */
    protected $paginated = false;

    /**
     * @var string
     */
    protected $defaultSort;

    /**
     * @var string
     */
    protected $defaultSortDir;

    /**
     * @var array
     */
    protected $filterHandlers;

    /**
     * Get the SharpListDataContainer array representation.
     *
     * @return array
     */
    function dataContainers(): array
    {
        $this->checkListIsBuilt();

        return collect($this->containers)->map(function(EntitiesListDataContainer $container) {
            return $container->toArray();
        })->keyBy("key")->all();
    }

    /**
     * Return the list fields layout.
     *
     * @return array
     */
    function listLayout(): array
    {
        if(!$this->layoutBuilt) {
            $this->buildListLayout();
            $this->layoutBuilt = true;
        }

        return collect($this->columns)->map(function(EntitiesListLayoutColumn $column) {
            return $column->toArray();
        })->all();
    }

    /**
     * Return data, as an array.
     *
     * @return array
     */
    function data(): array
    {
        $keys = $this->getDataContainersKeys();
        $config = $this->listConfig();
        $items = $this->getListData(
            EntitiesListQueryParams::createFromRequest($config["defaultSort"], $config["defaultSortDir"])
        );

        if($items instanceof LengthAwarePaginator) {
            $page = $items->currentPage();
            $totalCount = $items->total();
            $pageSize = $items->perPage();
            $items = $items->items();
        }

        return [
            "items" =>
                collect($items)
                    ->map(function($row) use($keys) {
                        // Filter model attributes on actual form fields
                        return collect($row)->only(
                            array_merge([$this->instanceIdAttribute], $keys)
                        )->all();
                    })->all()
        ] + (isset($page) ? compact('page', 'totalCount', 'pageSize') : []);
    }

    /**
     * Return the data config values.
     *
     * @return array
     */
    function listConfig(): array
    {
        $this->buildListConfig();

        $config = [
            "instanceIdAttribute" => $this->instanceIdAttribute,
            "displayMode" => $this->displayMode,
            "searchable" => $this->searchable,
            "paginated" => $this->paginated,
            "defaultSort" => $this->defaultSort,
            "defaultSortDir" => $this->defaultSortDir,
        ];

        foreach((array)$this->filterHandlers as $filterName => $handler) {
            $config["filter_$filterName"] = $handler->values();
        }

        return $config;
    }

    /**
     * @param string $instanceIdAttribute
     * @return $this
     */
    public function setInstanceIdAttribute(string $instanceIdAttribute)
    {
        $this->instanceIdAttribute = $instanceIdAttribute;

        return $this;
    }

    /**
     * @param string $displayMode
     * @return $this
     */
    public function setDisplayMode(string $displayMode)
    {
        $this->displayMode = $displayMode;

        return $this;
    }

    /**
     * @param bool $searchable
     * @return $this
     */
    public function setSearchable(bool $searchable = true)
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * @param string $sortBy
     * @param string $sortDir
     * @return $this
     */
    public function setDefaultSort(string $sortBy, string $sortDir = "asc")
    {
        $this->defaultSort = $sortBy;
        $this->defaultSortDir = $sortDir;

        return $this;
    }

    /**
     * @param bool $paginated
     * @return $this
     */
    public function setPaginated(bool $paginated = true)
    {
        $this->paginated = $paginated;

        return $this;
    }

    /**
     * Add a data container.
     *
     * @param EntitiesListDataContainer $container
     * @return $this
     */
    protected function addDataContainer(EntitiesListDataContainer $container)
    {
        $this->containers[] = $container;
        $this->listBuilt = false;

        return $this;
    }

    /**
     * @param string $label
     * @param int $size
     * @param null $sizeXS
     * @return $this
     */
    protected function addColumn(string $label, int $size, $sizeXS = null)
    {
        $this->layoutBuilt = false;

        $this->columns[] = new EntitiesListLayoutColumn($label, $size, $sizeXS);

        return $this;
    }

    /**
     * @param string $label
     * @param int $size
     * @return $this
     */
    protected function addColumnLarge(string $label, int $size)
    {
        $this->layoutBuilt = false;

        $column = new EntitiesListLayoutColumn($label, $size);
        $column->setLargeOnly(true);
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @param string $filterName
     * @param EntitiesListFilter $filterHandler
     * @return $this
     */
    protected function addFilter(string $filterName, EntitiesListFilter $filterHandler)
    {
        $this->filterHandlers[$filterName] = $filterHandler;

        return $this;
    }

    /**
     * @param string $filterName
     * @return string|array
     */
    protected function filterValue(string $filterName)
    {
        return $this->filterHandlers[$filterName]->currentValue();
    }

    private function checkListIsBuilt()
    {
        if (!$this->listBuilt) {
            $this->buildListDataContainers();
            $this->listBuilt = true;
        }
    }

    protected function getDataContainersKeys()
    {
        return collect($this->dataContainers())
            ->pluck("key")
            ->all();
    }

    /**
     * Retrieve all rows data as array.
     *
     * @param EntitiesListQueryParams $params
     * @return array
     */
    abstract function getListData(EntitiesListQueryParams $params);

    /**
     * Build list containers using ->addDataContainer()
     *
     * @return void
     */
    abstract function buildListDataContainers();

    /**
     * Build list layout using ->addColumn()
     *
     * @return void
     */
    abstract function buildListLayout();

    /**
     * Build list config
     *
     * @return void
     */
    abstract function buildListConfig();
}