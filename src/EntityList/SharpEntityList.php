<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Containers\EntityListDataContainer;
use Code16\Sharp\EntityList\Traits\HandleEntityCommands;
use Code16\Sharp\EntityList\Traits\HandleEntityState;
use Code16\Sharp\EntityList\Traits\HandleInstanceCommands;
use Code16\Sharp\Exceptions\EntityList\SharpEntityListLayoutException;
use Code16\Sharp\Utils\Filters\HandleFilters;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class SharpEntityList
{
    use HandleFilters,
        HandleEntityState,
        HandleEntityCommands,
        HandleInstanceCommands,
        WithCustomTransformers;

    protected array $containers = [];
    protected array $columns = [];
    protected bool $listBuilt = false;
    protected bool $layoutBuilt = false;
    protected string $instanceIdAttribute = "id";
    protected ?string $multiformAttribute = null;
    protected bool $searchable = false;
    protected bool $paginated = false;
    protected ?ReorderHandler $reorderHandler = null;
    protected ?string $defaultSort= null;
    protected ?string $defaultSortDir = null;
    protected ?EntityListQueryParams $queryParams;

    public final function initQueryParams(): self
    {
        $this->putRetainedFilterValuesInSession();

        $this->queryParams = EntityListQueryParams::create()
            ->setDefaultSort($this->defaultSort, $this->defaultSortDir)
            ->fillWithRequest()
            ->setDefaultFilters($this->getFilterDefaultValues());
        
        return $this;
    }

    public final function initWith(EntityListQueryParams $customParams): self
    {
        $this->queryParams = $customParams;

        return $this;
    }

    public final function dataContainers(): array
    {
        $this->checkListIsBuilt();

        return collect($this->containers)
            ->map(function(EntityListDataContainer $container) {
                return $container->toArray();
            })
            ->keyBy("key")
            ->all();
    }

    public final function listLayout(): array
    {
        if(!$this->layoutBuilt) {
            $this->buildListLayouts();
            $this->layoutBuilt = true;
        }
        
        // We need this to know if buildListLayoutForSmallScreens() was declared,
        // in order to hide unset columns in XS size.
        $xsSizesWereDefined = collect($this->columns)
                ->pluck("sizeXS")
                ->filter()
                ->count() > 0;

        return collect($this->columns)
            ->map(function($sizes, $key) use ($xsSizesWereDefined) {
                $data = [
                    "key" => $key,
                    "size" => $sizes["size"],
                ];
                
                if(!$xsSizesWereDefined) {
                    $data["hideOnXS"] = false;
                    $data["sizeXS"] = $sizes["size"];
                } else {
                    $data["hideOnXS"] = ($sizes["sizeXS"] ?? null) === null;
                    $data["sizeXS"] = $data["hideOnXS"] 
                        ? null 
                        : (($sizes["sizeXS"] ?? null) ?: $sizes["size"]);
                }
                
                return $data;
            })
            ->values()
            ->toArray();
    }

    public final function data($items = null): array
    {
        $items = $items ?: $this->getListData();

        if($items instanceof LengthAwarePaginator) {
            $page = $items->currentPage();
            $totalCount = $items->total();
            $pageSize = $items->perPage();
            $items = $items->items();
        }

        $this->addInstanceCommandsAuthorizationsToConfigForItems($items);

        $keys = $this->getDataKeys();
        $items = collect($items)
            ->map(function($row) use($keys) {
                // Filter model attributes on actual form fields
                return collect($row)
                    ->only(
                        array_merge(
                            $this->entityStateAttribute ? [$this->entityStateAttribute] : [],
                            $this->multiformAttribute ? [$this->multiformAttribute] : [],
                            [$this->instanceIdAttribute],
                            $keys
                        )
                    )
                    ->toArray();
            })
            ->toArray();

        return collect([
            "items" => $items ?? [],
            "page" => $page ?? null,
            "totalCount" => $totalCount ?? null,
            "pageSize" => $pageSize ?? null,
        ])
            ->filter(function($value) {
                return $value !== null;
            })
            ->toArray();
    }

    public final function listConfig(bool $hasShowPage = false): array
    {
        $config = [
            "instanceIdAttribute" => $this->instanceIdAttribute,
            "multiformAttribute" => $this->multiformAttribute,
            "searchable" => $this->searchable,
            "paginated" => $this->paginated,
            "reorderable" => !is_null($this->reorderHandler),
            "defaultSort" => $this->defaultSort,
            "defaultSortDir" => $this->defaultSortDir,
            "hasShowPage" => $hasShowPage,
        ];
        
        $this->appendFiltersToConfig($config);
        $this->appendEntityStateToConfig($config);
        $this->appendInstanceCommandsToConfig($config);
        $this->appendEntityCommandsToConfig($config);

        return $config;
    }

    public function setInstanceIdAttribute(string $instanceIdAttribute): self
    {
        $this->instanceIdAttribute = $instanceIdAttribute;

        return $this;
    }

    public function setReorderable($reorderHandler): self
    {
        $this->reorderHandler = $reorderHandler instanceof ReorderHandler
            ? $reorderHandler
            : app($reorderHandler);

        return $this;
    }

    public function setNotReorderable(): self
    {
        $this->reorderHandler = null;

        return $this;
    }

    public function setSearchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function setDefaultSort(string $sortBy, string $sortDir = "asc"): self
    {
        $this->defaultSort = $sortBy;
        $this->defaultSortDir = $sortDir;

        return $this;
    }

    public function setPaginated(bool $paginated = true): self
    {
        $this->paginated = $paginated;

        return $this;
    }

    protected function setMultiformAttribute(string $attribute): self
    {
        $this->multiformAttribute = $attribute;

        return $this;
    }

    public function reorderHandler(): ?ReorderHandler
    {
        return $this->reorderHandler;
    }

    protected function addDataContainer(EntityListDataContainer $container): self
    {
        $this->containers[] = $container;
        $this->listBuilt = false;

        return $this;
    }

    protected function addColumn(string $label, int $size = null): self
    {
        $sizeAttr = isset($this->columns[$label]["size"])
            ? "sizeXS"
            : "size";
        
        if(isset($this->columns[$label][$sizeAttr])) {
            throw new SharpEntityListLayoutException("The $label column was defined twice for the $sizeAttr size");
        }
        
        $this->columns[$label][$sizeAttr] = $size ?: "fill";

        return $this;
    }

    private function buildListLayouts(): void
    {
        $this->buildListLayout();
        $this->buildListLayoutForSmallScreens();
    }

    private function checkListIsBuilt(): void
    {
        if (!$this->listBuilt) {
            $this->buildListDataContainers();
            $this->listBuilt = true;
        }
    }

    protected final function getDataKeys(): array
    {
        return collect($this->dataContainers())
            ->pluck("key")
            ->all();
    }

    /**
     * Return all entity commands in an array of class names or instances
     */
    function getEntityCommands(): ?array
    {
        return null;
    }

    /**
     * Return all instance commands in an array of class names or instances
     */
    function getInstanceCommands(): ?array
    {
        return null;
    }

    /**
     * Build layout for small screen. Optional, only if needed.
     */
    function buildListLayoutForSmallScreens(): void
    {
    }

    /**
     * Retrieve all rows data as an array
     */
    abstract function getListData(): array|Arrayable;

    /**
     * Build list containers using ->addDataContainer()
     */
    abstract function buildListDataContainers(): void;

    /**
     * Build list layout using ->addColumn()
     */
    abstract function buildListLayout(): void;

    /**
     * Build list config
     */
    abstract function buildListConfig(): void;
}