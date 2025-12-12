<?php

namespace Code16\Sharp\Filters;

use Code16\Sharp\Enums\FilterType;

/**
 * @internal
 */
trait SelectFilterTrait
{
    private bool $isMaster = false;
    private bool $isSearchable = false;
    private array $searchKeys = ['label'];

    final public function isMaster(): bool
    {
        return $this->isMaster;
    }

    final public function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    final public function getSearchKeys(): array
    {
        return $this->searchKeys;
    }

    final public function configureSearchable(bool $isSearchable = true): self
    {
        $this->isSearchable = $isSearchable;

        return $this;
    }

    final public function configureSearchKeys(array $searchKeys): self
    {
        $this->searchKeys = $searchKeys;

        return $this;
    }

    final public function configureMaster(bool $isMaster = true): self
    {
        $this->isMaster = $isMaster;

        return $this;
    }

    public function fromQueryParam($value): mixed
    {
        return $value;
    }

    public function toQueryParam($value): mixed
    {
        return $value;
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'type' => FilterType::Select->value,
            'multiple' => $this instanceof SelectMultipleFilter,
            'required' => $this instanceof SelectRequiredFilter,
            'values' => $this->formattedValues(),
            'master' => $this->isMaster(),
            'searchable' => $this->isSearchable(),
            'searchKeys' => $this->getSearchKeys(),
        ]);
    }

    protected function formattedValues(): array
    {
        $values = $this->values();

        if (! is_array(collect($values)->first())) {
            return collect($values)
                ->map(function ($label, $id) {
                    return compact('id', 'label');
                })
                ->values()
                ->all();
        }

        return $values;
    }

    abstract public function values(): array;
}
