<?php

namespace Code16\Sharp\EntitiesList;


abstract class SharpEntitiesList
{
    /**
     * @var array
     */
    protected $containers = [];

    /**
     * @var bool
     */
    protected $listBuilt = false;

    /**
     * @var bool
     */
    protected $layoutBuilt = false;

    /**
     * Get the SharpListColumn array representation.
     *
     * @return array
     */
    function columns(): array
    {
        $this->checkListIsBuilt();

        return collect($this->containers)->map(function($column) {
            return $column->toArray();
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

        return [
            "tabbed" => $this->tabbed,
            "tabs" => collect($this->tabs)->map(function($tab) {
                return $tab->toArray();
            })->all()
        ];
    }

    /**
     * Return data, as an array.
     *
     * @param SharpListQueryParams $params
     * @return array
     */
    function data(SharpListQueryParams $params): array
    {
        return collect($this->getListData($params))
            // Filter model attributes on actual form fields
            //->only($this->getFieldKeys())
            ->all();
    }

    /**
     * Add a data container.
     *
     * @param SharpListDataContainer $container
     * @return $this
     */
    protected function addDataContainer(SharpListDataContainer $container)
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



        return $this;
    }

    private function checkListIsBuilt()
    {
        if (!$this->listBuilt) {
            $this->buildListDataContainers();
            $this->listBuilt = true;
        }
    }

    /**
     * Retrieve all rows data as array.
     *
     * @param SharpListQueryParams $params
     * @return array
     */
    abstract function getListData(SharpListQueryParams $params): array;

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