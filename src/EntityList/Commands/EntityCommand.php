<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;

abstract class EntityCommand extends Command
{
    protected ?EntityListQueryParams $queryParams = null;
    protected string $instanceSelectionMode = 'none';

    public function type(): string
    {
        return 'entity';
    }
    
    final protected function configureInstanceSelectionRequired(?bool $required = true): self
    {
        $this->instanceSelectionMode = $required ? 'required' : 'none';

        return $this;
    }

    final protected function configureInstanceSelectionAllowed(?bool $allowed = true): self
    {
        $this->instanceSelectionMode = $allowed ? 'allowed' : 'none';

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
    
    final public function selectedIds(): array
    {
        return $this->instanceSelectionMode === 'none' 
            ? [] 
            : $this->queryParams->specificIds();
    }
    
    abstract public function execute(array $data = []): array;
}
