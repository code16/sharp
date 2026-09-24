<?php

namespace Code16\Sharp\Dashboard\Commands;

use Code16\Sharp\Commands\Command;
use Code16\Sharp\Commands\Returns\CommandReturn;
use Code16\Sharp\Dashboard\DashboardQueryParams;

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

    abstract public function execute(array $data = []): CommandReturn;
}
