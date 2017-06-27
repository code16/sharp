<?php

namespace Code16\Sharp\EntitiesList;

use Code16\Sharp\EntitiesList\containers\EntitiesListDataContainer;
use Code16\Sharp\EntitiesList\layout\EntitiesListLayoutColumn;

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
     * @param EntitiesListQueryParams $params
     * @return array
     */
    function data(EntitiesListQueryParams $params): array
    {
        $keys = $this->getDataContainersKeys();

        return [
            "items" =>
                collect($this->getListData($params))
                    ->map(function($row) use($keys) {
                        // Filter model attributes on actual form fields
                        return collect($row)->only(
                            array_merge([$this->instanceIdAttribute], $keys)
                        )->all();
                    })->all()
        ];
    }

    /**
     * Return the data config values.
     *
     * @return array
     */
    function listConfig(): array
    {
        $this->buildListConfig();

        return [
            "instanceIdAttribute" => $this->instanceIdAttribute,
            "displayMode" => $this->displayMode,
            "searchable" => $this->searchable,
            "paginated" => $this->paginated,
        ];
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