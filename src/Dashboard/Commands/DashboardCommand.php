<?php

namespace Code16\Sharp\Dashboard\Commands;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\EntityList\Commands\Command;

abstract class DashboardCommand extends Command
{
    public function type(): string
    {
        return 'dashboard';
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

    /**
     * @param mixed $ids
     *
     * @return array
     */
    protected function refresh($ids): array
    {
        // Refresh has no meaning in the Dashboard; we just do a classic reload.
        return $this->reload();
    }

    /**
     * @param DashboardQueryParams $params
     * @param array                $data
     *
     * @return array
     */
    abstract public function execute(DashboardQueryParams $params, array $data = []): array;
}
