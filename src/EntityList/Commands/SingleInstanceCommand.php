<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class SingleInstanceCommand extends InstanceCommand
{

    /**
     * @return string
     */
    public function type(): string
    {
        return "instance";
    }

    /**
     * @param $instanceId
     * @return array
     */
    protected final function initialData($instanceId): array
    {
        return $this->initialSingleData();
    }

    /**
     * @return array
     */
    protected function initialSingleData(): array
    {
        return [];
    }

    /**
     * @param string $instanceId
     * @param array $data
     * @return array
     */
    public final function execute($instanceId, array $data = []): array
    {
        return $this->executeSingle($data);
    }

    /**
     * @param $instanceId
     * @return bool
     */
    public final function authorizeFor($instanceId): bool
    {
        return $this->authorize();
    }

    /**
     * @return bool|array
     */
    public function getGlobalAuthorization()
    {
        return $this->authorize();
    }

    /**
     * @param array $data
     * @return array
     */
    protected abstract function executeSingle(array $data);
}