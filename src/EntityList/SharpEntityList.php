<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\EntityList\Commands\ReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\Traits\HandleEntityCommands;
use Code16\Sharp\EntityList\Traits\HandleEntityState;
use Code16\Sharp\EntityList\Traits\HandleInstanceCommands;
use Code16\Sharp\Utils\Filters\HandleFilters;
use Code16\Sharp\Utils\Traits\HandlePageAlertMessage;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class SharpEntityList
{
    use HandleFilters,
        HandleEntityState,
        HandleEntityCommands,
        HandleInstanceCommands,
        HandlePageAlertMessage,
        WithCustomTransformers;

    private ?EntityListFieldsContainer $fieldsContainer = null;
    protected ?EntityListQueryParams $queryParams;
    protected string $instanceIdAttribute = 'id';
    protected ?string $multiformAttribute = null;
    protected bool $searchable = false;
    protected bool $paginated = false;
    protected ?ReorderHandler $reorderHandler = null;
    protected ?string $defaultSort = null;
    protected ?string $defaultSortDir = null;
    protected bool $deleteHidden = false;
    protected ?string $deleteConfirmationText = null;

    final public function initQueryParams(): self
    {
        $this->putRetainedFilterValuesInSession();

        $this->queryParams = EntityListQueryParams::create()
            ->setDefaultSort($this->defaultSort, $this->defaultSortDir)
            ->fillWithRequest()
            ->setDefaultFilters($this->getFilterDefaultValues());

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
            ->getFields()
            ->toArray();
    }

    final public function data($items = null): array
    {
        $items = $items ?: $this->getListData();
        $page = $totalCount = $pageSize = null;

        if ($items instanceof LengthAwarePaginator) {
            $page = $items->currentPage();
            $totalCount = $items->total();
            $pageSize = $items->perPage();
            $items = $items->items();
        }

        $this->addInstanceCommandsAuthorizationsToConfigForItems($items);

        $keys = $this->getDataKeys();
        $items = collect($items)
            ->map(function ($row) use ($keys) {
                // Filter model attributes on actual form fields
                return collect($row)
                    ->only(
                        array_merge(
                            array_keys($this->transformers),
                            $this->entityStateAttribute ? [$this->entityStateAttribute] : [],
                            $this->multiformAttribute ? [$this->multiformAttribute] : [],
                            [$this->instanceIdAttribute],
                            $keys,
                        ),
                    )
                    ->toArray();
            })
            ->toArray();

        return collect(
            [
                'list' => collect(['items' => $items ?? []])
                    ->when($page !== null, function (Collection $collection) use ($page, $totalCount, $pageSize) {
                        $collection['page'] = $page;
                        $collection['totalCount'] = $totalCount;
                        $collection['pageSize'] = $pageSize;

                        return $collection;
                    })
                    ->toArray(),
            ])
            ->toArray();
    }

    final public function listConfig(bool $hasShowPage = false): array
    {
        $config = [
            'instanceIdAttribute' => $this->instanceIdAttribute,
            'multiformAttribute' => $this->multiformAttribute,
            'searchable' => $this->searchable,
            'paginated' => $this->paginated,
            'reorderable' => ! is_null($this->reorderHandler),
            'defaultSort' => $this->defaultSort,
            'defaultSortDir' => $this->defaultSortDir,
            'hasShowPage' => $hasShowPage,
            'deleteConfirmationText' => $this->deleteConfirmationText ?: trans('sharp::show.delete_confirmation_text'),
            'deleteHidden' => $this->deleteHidden,
        ];

        return tap($config, function (&$config) {
            $this->appendFiltersToConfig($config);
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

    final public function configurePaginated(bool $paginated = true): self
    {
        $this->paginated = $paginated;

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
     * Return global message data if needed.
     */
    protected function getGlobalMessageData(): ?array
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
     * Retrieve all rows data as an array.
     */
    abstract public function getListData(): array|Arrayable;

    /**
     * Build list fields and layout.
     */
    protected function buildList(EntityListFieldsContainer $fields): void
    {
    }
}
