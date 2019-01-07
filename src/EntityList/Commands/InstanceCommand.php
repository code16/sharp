<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class InstanceCommand extends Command
{

    /**
     * @var array
     */
    protected $authorizedInstances = [];

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
    public function formData($instanceId): array
    {
        return collect($this->initialData($instanceId))
            ->only($this->getDataKeys())
            ->all();
    }

    /**
     * @param $instanceId
     * @return array
     */
    protected function initialData($instanceId): array
    {
        return [];
    }

    /**
     * @param string $instanceId
     * @param array $data
     * @return array
     */
    public abstract function execute($instanceId, array $data = []): array;

    /**
     * Check if the current user is allowed to use this Command for this instance
     *
     * @param $instanceId
     * @return bool
     */
    public function authorizeFor($instanceId): bool
    {
        return true;
    }

    /**
     * @param $instanceId
     */
    public function checkAndStoreAuthorizationFor($instanceId)
    {
        if($this->authorizeFor($instanceId)) {
            $this->authorizedInstances[] = $instanceId;
        }
    }

    /**
     * @return bool|array
     */
    public function getGlobalAuthorization()
    {
        if(!$this->authorize()) {
            return false;
        }

        return $this->authorizedInstances;
    }
}