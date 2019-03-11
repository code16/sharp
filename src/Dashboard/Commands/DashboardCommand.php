<?php

namespace Code16\Sharp\Dashboard\Commands;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\EntityList\Commands\Command;

abstract class DashboardCommand extends Command
{

    /**
     * @return string
     */
    public function type(): string
    {
        return "dashboard";
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
     * @param mixed $ids
     * @return array
     */
    protected function refresh($ids)
    {
        // Refresh has no meaning in the Dashboard; we just do a classic reload.
        return $this->reload();
    }

    /**
     * @param DashboardQueryParams $params
     * @param array $data
     * @return array
     */
    public abstract function execute(DashboardQueryParams $params, array $data = []): array;
}