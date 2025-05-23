<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;

abstract class EntityCommand extends Command
{
    protected ?EntityListQueryParams $queryParams = null;
    protected ?string $instanceSelectionMode = null;
    protected bool $formModalShowSubmitAndReopenButton = false;
    protected ?string $formModalSubmitAndReopenButtonLabel = null;

    public function type(): string
    {
        return 'entity';
    }

    final protected function configureInstanceSelectionRequired(): self
    {
        $this->instanceSelectionMode = 'required';

        return $this;
    }

    final protected function configureInstanceSelectionAllowed(): self
    {
        $this->instanceSelectionMode = 'allowed';

        return $this;
    }

    final protected function configureInstanceSelectionNone(): self
    {
        $this->instanceSelectionMode = null;

        return $this;
    }

    final protected function configureFormModalSubmitAndReopenButton(?string $label = null): self
    {
        $this->formModalShowSubmitAndReopenButton = true;
        $this->formModalSubmitAndReopenButtonLabel = $label;

        return $this;
    }

    final public function initQueryParams(EntityListQueryParams $params): void
    {
        $this->queryParams = $params;
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

    final public function getInstanceSelectionMode(): ?string
    {
        return $this->instanceSelectionMode;
    }

    final public function getFormModalShowSubmitAndReopenButton(): bool
    {
        return $this->formModalShowSubmitAndReopenButton;
    }

    final public function getFormModalSubmitAndReopenButtonLabel(): ?string
    {
        return $this->formModalSubmitAndReopenButtonLabel;
    }

    final public function selectedIds(): array
    {
        return $this->instanceSelectionMode
            ? $this->queryParams->specificIds()
            : [];
    }

    abstract public function execute(array $data = []): array;
}
