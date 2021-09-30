<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class SingleInstanceCommand extends InstanceCommand
{
    public function type(): string
    {
        return "instance";
    }

    /**
     * @param mixed $instanceId
     * @return array
     */
    protected final function initialData($instanceId): array
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
     * @return array
     */
    public final function execute($instanceId, array $data = []): array
    {
        return $this->executeSingle($data);
    }

    /**
     * @param mixed $instanceId
     * @return bool
     */
    public final function authorizeFor($instanceId): bool
    {
        return $this->authorize();
    }

    public function getGlobalAuthorization(): bool|array
    {
        return $this->authorize();
    }

    protected abstract function executeSingle(array $data): array;
}