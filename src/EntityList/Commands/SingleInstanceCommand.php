<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class SingleInstanceCommand extends InstanceCommand
{
    public function type(): string
    {
        return 'instance';
    }

    /**
     * @param mixed $instanceId
     *
     * @return array
     */
    final protected function initialData($instanceId): array
    {
        return $this->initialSingleData();
    }

    protected function initialSingleData(): array
    {
        return [];
    }

    /**
     * @param mixed $instanceId
     * @param array $data
     *
     * @return array
     */
    final public function execute($instanceId, array $data = []): array
    {
        return $this->executeSingle($data);
    }

    /**
     * @param mixed $instanceId
     *
     * @return bool
     */
    final public function authorizeFor($instanceId): bool
    {
        return $this->authorize();
    }

    public function getGlobalAuthorization(): bool|array
    {
        return $this->authorize();
    }

    abstract protected function executeSingle(array $data): array;
}
