<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;

abstract class EntityCommand extends Command
{
    public function type(): string
    {
        return 'entity';
    }

    public function formData(): array
    {
        return collect($this->initialData())
            ->only($this->getDataKeys())
            ->all();
    }

    protected function initialData(): array
    {
        return [];
    }

    abstract public function execute(EntityListQueryParams $params, array $data = []): array;
}
