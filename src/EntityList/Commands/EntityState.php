<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\Exceptions\EntityList\SharpInvalidEntityStateException;
use Exception;

/**
 * Base class for applicative Entity States.
 */
abstract class EntityState extends InstanceCommand
{
    protected array $states = [];

    public function states(): array
    {
        $this->buildStates();

        return $this->states;
    }

    protected function addState(string $key, string $label, string $color = null): self
    {
        $this->states[$key] = [$label, $color];

        return $this;
    }

    protected function view(string $bladeView, array $params = []): array
    {
        throw new Exception('View return type is not supported for a state.');
    }

    protected function info(string $message): array
    {
        throw new Exception('Info return type is not supported for a state.');
    }

    /**
     * @param mixed $instanceId
     * @param array $data
     *
     * @throws SharpInvalidEntityStateException
     *
     * @return array
     */
    public function execute($instanceId, array $data = []): array
    {
        $stateId = $data['value'];
        $this->buildStates();

        if (!in_array($stateId, array_keys($this->states))) {
            throw new SharpInvalidEntityStateException($stateId);
        }

        return $this->updateState($instanceId, $stateId) ?: $this->refresh($instanceId);
    }

    public function label(): ?string
    {
        return null;
    }

    abstract protected function buildStates(): void;

    /**
     * @param mixed  $instanceId
     * @param string $stateId
     *
     * @return mixed
     */
    abstract protected function updateState($instanceId, string $stateId): array;
}
