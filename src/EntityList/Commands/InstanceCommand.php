<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class InstanceCommand extends Command
{
    protected array $authorizedInstances = [];

    public function type(): string
    {
        return 'instance';
    }

    /**
     * @param  mixed  $instanceId
     * @return array
     */
    public function formData($instanceId): array
    {
        return collect($this->initialData($instanceId))
            ->only($this->getDataKeys())
            ->all();
    }

    /**
     * @param  mixed  $instanceId
     * @return array
     */
    protected function initialData($instanceId): array
    {
        return [];
    }

    /**
     * @param  mixed  $instanceId
     * @param  array  $data
     * @return array
     */
    abstract public function execute($instanceId, array $data = []): array;

    /**
     * Check if the current user is allowed to use this Command for this instance.
     *
     * @param  mixed  $instanceId
     */
    public function authorizeFor($instanceId): bool
    {
        return true;
    }

    /**
     * @param  mixed  $instanceId
     */
    public function checkAndStoreAuthorizationFor($instanceId)
    {
        if ($this->authorizeFor($instanceId)) {
            $this->authorizedInstances[] = $instanceId;
        }
    }

    /**
     * @return bool|array
     */
    public function getGlobalAuthorization()
    {
        if (! $this->authorize()) {
            return false;
        }

        return $this->authorizedInstances;
    }

    public function getDataLocalizations(): array
    {
        return [];
    }
}
