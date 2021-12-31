<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;

abstract class SharpDashboardEntity extends BaseSharpEntity
{
    protected bool $isDashboard = true;
    protected ?string $view = null;

    public final function getViewOrFail(): SharpDashboard
    {
        throw_if(
            !$this->hasView(), 
            new SharpInvalidEntityKeyException("The view for the dashboard entity [{$this->entityKey}] was not found.")
        );
        return app($this->getView());
    }

    public final function hasView(): bool
    {
        return $this->getView() !== null;
    }

    protected function getView(): ?string
    {
        return $this->view;
    }

    public final function isActionProhibited(string $action): bool
    {
        return false;
    }
}