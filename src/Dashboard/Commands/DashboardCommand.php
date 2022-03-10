<?php

namespace Code16\Sharp\Dashboard\Commands;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\EntityList\Commands\Command;

abstract class DashboardCommand extends Command
{
    protected ?DashboardQueryParams $queryParams;

    final public function type(): string
    {
        return 'dashboard';
    }

    final public function initQueryParams(?DashboardQueryParams $queryParams): void
    {
        $this->queryParams = $queryParams;
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

    protected function refresh($ids): array
    {
        // Refresh has no meaning in the Dashboard; we just do a classic reload.
        return $this->reload();
    }

    abstract public function execute(array $data = []): array;
}
