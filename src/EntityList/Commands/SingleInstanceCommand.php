<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class SingleInstanceCommand extends InstanceCommand
{
    public function type(): string
    {
        return 'instance';
    }

    final protected function initialData(mixed $instanceId): array
    {
        return $this->initialSingleData();
    }

    protected function initialSingleData(): array
    {
        return [];
    }

    final public function execute(mixed $instanceId, array $data = []): array
    {
        return $this->executeSingle($data);
    }

    final public function authorizeFor(mixed $instanceId): bool
    {
        return $this->authorize();
    }

    public function getGlobalAuthorization(): bool|array
    {
        return $this->authorize();
    }

    abstract protected function executeSingle(array $data): array;
}
