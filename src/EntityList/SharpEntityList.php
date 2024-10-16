<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Traits\HandleEntityCommands;
use Code16\Sharp\EntityList\Traits\HandleEntityState;
use Code16\Sharp\EntityList\Traits\HandleInstanceCommands;
use Code16\Sharp\Utils\Filters\FilterContainer;
use Code16\Sharp\Utils\Filters\HandleFilters;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

abstract class SharpEntityList
{
    use HandleFilters;
    use HandleEntityState;
    use HandleEntityCommands;
    use HandleInstanceCommands;
    use HandlePageAlertMessage;
    use WithCustomTransformers;

    private ?EntityListFieldsContainer $fieldsContainer = null;
    protected ?EntityListQueryParams $queryParams;
    protected string $instanceIdAttribute = 'id';
    protected ?string $multiformAttribute = null;
    protected bool $searchable = false;
    protected ?ReorderHandler $reorderHandler = null;
    protected ?string $defaultSort = null;
    protected ?string $defaultSortDir = null;
    protected bool $deleteHidden = false;
    protected ?string $deleteConfirmationText = null;

    final public function initQueryParams(?array $query): self
    {
        $this->queryParams = (new EntityListQueryParams(
            filterContainer: $this->filterContainer(),
            filterValues: $this->filterContainer()->getCurrentFilterValues($query),
            sortedBy: $query['sort'] ?? $this->defaultSort,
            sortedDir: $query['dir'] ?? $this->defaultSortDir,
            page: $query['page'] ?? null,
            search: ($query['search'] ?? null) ? urldecode($query['search']) : null,
            specificIds: $query['ids'] ?? [],
        ));
        
        return $this;
    }

    final public function updateQueryParamsWithSpecificIds(array $specificIds): self
    {
        $this->queryParams->setSpecificIds($specificIds);

        return $this;
    }

    final public function fields(): array
    {
        $this->checkListIsBuilt();

        return $this->fieldsContainer
            ->getFields(shouldHaveStateField: !!$this->entityStateAttribute)
            ->toArray();
    }

    final public function data(): array
    {
        $listItems = $this->getListData();

        $items = $listItems instanceof AbstractPaginator
            ? $listItems->items()
            : $listItems;

        $this->addInstanceCommandsAuthorizationsToConfigForItems($items);

        $items = collect($items)
            // Filter model attributes on actual form fields
            ->map(fn ($row) => collect($row)
                ->only(
                    array_merge(
                        array_keys($this->transformers),
                        $this->entityStateAttribute ? [$this->entityStateAttribute] : [],
                        $this->multiformAttribute ? [$this->multiformAttribute] : [],
                        [$this->instanceIdAttribute],
                        $this->getDataKeys(),
                    ),
                )
                ->toArray()
            )
            ->toArray();

        return [
            'items' => $items,
            'meta' => $listItems instanceof AbstractPaginator
                ? Arr::except($listItems->withQueryString()->toArray(), 'data')
                : null,
        ];
    }

    final public function listConfig(bool $hasShowPage = false): array
    {
        $config = [
            'instanceIdAttribute' => $this->instanceIdAttribute,
            'multiformAttribute' => $this->multiformAttribute,
            'searchable' => $this->searchable,
            'reorderable' => ! is_null($this->reorderHandler),
            'defaultSort' => $this->defaultSort,
            'defaultSortDir' => $this->defaultSortDir,
            'hasShowPage' => $hasShowPage,
            'deleteConfirmationText' => $this->deleteConfirmationText ?: trans('sharp::show.delete_confirmation_text'),
            'deleteHidden' => $this->deleteHidden,
            'filters' => $this->filterContainer()->getFiltersConfigArray(),
        ];

        return tap($config, function (&$config) {
            $this->appendEntityStateToConfig($config);
            $this->appendInstanceCommandsToConfig($config);
            $this->appendEntityCommandsToConfig($config);
        });
    }

    final public function configureInstanceIdAttribute(string $instanceIdAttribute): self
    {
        $this->instanceIdAttribute = $instanceIdAttribute;

        return $this;
    }

    final public function configureReorderable(ReorderHandler|string $reorderHandler): self
    {
        $this->reorderHandler = $reorderHandler instanceof ReorderHandler
            ? $reorderHandler
            : app($reorderHandler);

        return $this;
    }

    final public function configureSearchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;

        return $this;
    }

    final public function configureDelete(bool $hide = false, ?string $confirmationText = null): self
    {
        $this->deleteHidden = $hide;
        $this->deleteConfirmationText = $confirmationText;

        return $this;
    }

    final public function configureDefaultSort(string $sortBy, string $sortDir = 'asc'): self
    {
        $this->defaultSort = $sortBy;
        $this->defaultSortDir = $sortDir;

        return $this;
    }

    final protected function configureMultiformAttribute(string $attribute): self
    {
        $this->multiformAttribute = $attribute;

        return $this;
    }

    final public function reorderHandler(): ?ReorderHandler
    {
        return $this->reorderHandler;
    }

    private function checkListIsBuilt(): void
    {
        if ($this->fieldsContainer === null) {
            $this->fieldsContainer = new EntityListFieldsContainer();
            $this->buildList($this->fieldsContainer);
        }
    }

    final protected function getDataKeys(): array
    {
        return collect($this->fields())
            ->pluck('key')
            ->all();
    }

    /**
     * Build list config.
     */
    public function buildListConfig(): void
    {
    }

    /**
     * Return all entity commands in an array of class names or instances.
     */
    protected function getEntityCommands(): ?array
    {
        return null;
    }

    /**
     * Return all instance commands in an array of class names or instances.
     */
    protected function getInstanceCommands(): ?array
    {
        return null;
    }

    /**
     * Return all filters in an array of class names or instances.
     */
    protected function getFilters(): ?array
    {
        return null;
    }

    /**
     * Delete the given instance. Do not implement this method if you want to delegate
     * the deletion responsibility to the Show Page (then implement it there).
     */
    public function delete(mixed $id): void
    {
    }

    /**
     * Build list fields and layout.
     */
    abstract protected function buildList(EntityListFieldsContainer $fields): void;

    /**
     * Retrieve all rows data as an array.
     */
    abstract public function getListData(): array|Arrayable;

    /**
     * @deprecated no more in use, will be removed in v10.x
     */
    final public function configurePaginated(bool $paginated = true): self
    {
        return $this;
    }
}
