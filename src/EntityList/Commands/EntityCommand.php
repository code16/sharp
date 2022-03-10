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

    abstract public function execute(array $data = []): array;
}
