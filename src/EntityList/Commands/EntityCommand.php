<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;

abstract class EntityCommand extends Command
{
    protected ?EntityListQueryParams $queryParams = null;
    protected string $instanceSelectionMode = 'none';
    protected ?array $instanceSelectionCriteria = null;

    public function type(): string
    {
        return 'entity';
    }

    final protected function configureInstanceSelectionRequired(?string $attribute = null, array|string|bool $values = true): self
    {
        $this->instanceSelectionMode = 'required';
        $this->setInstanceSelectionCriteria($attribute, $values);

        return $this;
    }

    final protected function configureInstanceSelectionAllowed(?string $attribute = null, array|string|bool $values = true): self
    {
        $this->instanceSelectionMode = 'allowed';
        $this->setInstanceSelectionCriteria($attribute, $values);

        return $this;
    }

    final protected function configureInstanceSelectionNone(): self
    {
        $this->instanceSelectionMode = 'none';

        return $this;
    }

    final public function initQueryParams(EntityListQueryParams $params): void
    {
        $this->queryParams = $params;
    }

    final public function formData(): array
    {
        return collect($this->initialData())
            ->only($this->getDataKeys())
            ->all();
    }

    protected function initialData(): array
    {
        return [];
    }

    final public function getInstanceSelectionMode(): string
    {
        return $this->instanceSelectionMode;
    }

    final public function getInstanceSelectionCriteria(): ?array
    {
        return $this->instanceSelectionCriteria;
    }

    final public function selectedIds(): array
    {
        return $this->instanceSelectionMode === 'none'
            ? []
            : $this->queryParams->specificIds();
    }

    abstract public function execute(array $data = []): array;

    private function setInstanceSelectionCriteria(?string $attribute, bool|array|string $values): void
    {
        if ($attribute === null) {
            $this->instanceSelectionCriteria = null;

            return;
        }

        if (str($attribute)->startsWith('!')) {
            $attribute = substr($attribute, 1);
            $values = false;
        }

        $this->instanceSelectionCriteria = [
            'key' => $attribute,
            'values' => $values,
        ];
    }
}
