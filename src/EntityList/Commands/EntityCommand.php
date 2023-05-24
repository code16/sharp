<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;

abstract class EntityCommand extends Command
{
    protected ?EntityListQueryParams $queryParams = null;

    public function type(): string
    {
        return 'entity';
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
        return $this->requiresSelect()
            ? 'required'
            : ($this->allowsSelect() ? 'allowed' : 'none');
    }
    
    final public function selectedIds(): array
    {
        return $this->getInstanceSelectionMode() === 'none' 
            ? [] 
            : $this->queryParams->specificIds();
    }

    public function requiresSelect(): bool
    {
        return false;
    }

    public function allowsSelect(): bool
    {
        return false;
    }
    
    abstract public function execute(array $data = []): array;
}
