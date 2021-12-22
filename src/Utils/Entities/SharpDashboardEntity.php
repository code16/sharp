<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;

abstract class SharpDashboardEntity
{
    protected string $entityKey = "dashboard";
    protected ?string $view = null;
    protected ?string $policy = null;

    public function setEntityKey(string $entityKey): self
    {
        $this->entityKey = $entityKey;
        return $this;
    }
    
    public function getViewOrFail(): SharpDashboard
    {
        throw_if(
            !$this->hasView(), 
            new SharpInvalidEntityKeyException("The view for the dashboard entity [{$this->entityKey}] was not found.")
        );
        return app($this->getView());
    }

    public function hasView(): bool
    {
        return $this->getView() !== null;
    }

    protected function getView(): ?string
    {
        return $this->view;
    }

    public final function getPolicyOrDefault(): SharpEntityPolicy
    {
        if(!$policy = $this->getPolicy()) {
            return new SharpEntityPolicy();
        }
        return app($policy);
    }

    protected function getPolicy(): ?string
    {
        return $this->policy;
    }
}