<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;

abstract class EntityCommand extends Command
{
    protected ?EntityListQueryParams $queryParams = null;
    
    public final function type(): string
    {
        return "entity";
    }
    
    public final function initQueryParams(EntityListQueryParams $params): void
    {
        $this->queryParams = $params;
    }

    public final function formData(): array
    {
        return collect($this->initialData())
            ->only($this->getDataKeys())
            ->all();
    }

    protected function initialData(): array
    {
        return [];
    }

    public abstract function execute(array $data = []): array;
}