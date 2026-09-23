<?php

namespace Code16\Sharp\Dashboard\Commands;

use Code16\Sharp\Dashboard\DashboardQueryParams;
use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\EntityList\Commands\Returns\CommandRefreshReturn;
use Code16\Sharp\EntityList\Commands\Returns\CommandReloadReturn;
use Code16\Sharp\EntityList\Commands\Returns\CommandReturn;

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

    final public function formData(): array
    {
        return collect()
            ->merge(collect($this->getDataKeys())->mapWithKeys(fn ($key) => [$key => null]))
            ->merge($this->initialData())
            ->only([
                ...$this->getDataKeys(),
                ...array_keys($this->transformers),
            ])
            ->all();
    }

    protected function initialData(): array
    {
        return [];
    }

    protected function refresh($ids): CommandRefreshReturn|CommandReloadReturn
    {
        // Refresh has no meaning in the Dashboard; we just do a classic reload.
        return $this->reload();
    }

    abstract public function execute(array $data = []): CommandReturn;
}
