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

        return collect($this->getListData($params))
            ->map(function($row) use($keys) {
                // Filter model attributes on actual form fields
                return collect($row)->only($keys)->all();
            })
            ->all();
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
}