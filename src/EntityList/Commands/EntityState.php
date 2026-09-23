<?php

namespace Code16\Sharp\EntityList\Commands;

use Code16\Sharp\EntityList\Commands\Returns\CommandDownloadReturn;
use Code16\Sharp\EntityList\Commands\Returns\CommandInfoReturn;
use Code16\Sharp\EntityList\Commands\Returns\CommandLinkReturn;
use Code16\Sharp\EntityList\Commands\Returns\CommandRefreshReturn;
use Code16\Sharp\EntityList\Commands\Returns\CommandReloadReturn;
use Code16\Sharp\EntityList\Commands\Returns\CommandReturn;
use Code16\Sharp\EntityList\Commands\Returns\CommandStreamDownloadReturn;
use Code16\Sharp\EntityList\Commands\Returns\CommandViewReturn;
use Code16\Sharp\Exceptions\EntityList\SharpInvalidEntityStateException;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;

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

    protected function addState(string $key, string $label, ?string $color = null): self
    {
        $this->states[$key] = [$label, $color];

        return $this;
    }

    protected function view(string $bladeView, array $params = []): CommandViewReturn
    {
        throw new SharpInvalidConfigException('View return type is not supported for a state.');
    }

    protected function info(string $message, bool $reload = false): CommandInfoReturn
    {
        throw new SharpInvalidConfigException('Info return type is not supported for a state.');
    }

    protected function download(string $filePath, ?string $fileName = null, ?string $diskName = null): CommandDownloadReturn
    {
        throw new SharpInvalidConfigException('Download return type is not supported for a state.');
    }

    protected function streamDownload(string $fileContent, string $fileName): CommandStreamDownloadReturn
    {
        throw new SharpInvalidConfigException('StreamDownload return type is not supported for a state.');
    }

    protected function link(string $link, bool $openInNewTab = false): CommandLinkReturn
    {
        throw new SharpInvalidConfigException('Link return type is not supported for a state.');
    }

    /**
     * @throws SharpInvalidEntityStateException
     */
    public function execute($instanceId, array $data = []): CommandReturn
    {
        $stateId = $data['value'];
        $this->buildStates();

        if (! in_array($stateId, array_keys($this->states))) {
            throw new SharpInvalidEntityStateException($stateId);
        }

        return $this->updateState($instanceId, $stateId) ?: $this->refresh($instanceId);
    }

    public function label(): ?string
    {
        return null;
    }

    abstract protected function buildStates(): void;

    abstract protected function updateState($instanceId, string $stateId): CommandReloadReturn|CommandRefreshReturn|null;
}
