<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;

abstract class EntityCommand extends Command
{

    /**
     * @return string
     */
    public function type(): string
    {
        return "entity";
    }

    /**
     * @return array
     */
    public function formData(): array
    {
        return collect($this->initialData())
            ->only($this->getDataKeys())
            ->all();
    }

    /**
     * @return array
     */
    protected function initialData(): array
    {
        return [];
    }

    /**
     * @param EntityListQueryParams $params
     * @param array $data
     * @return array
     */
    public abstract function execute(EntityListQueryParams $params, array $data = []): array;
}