<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;

abstract class SharpDashboardEntity extends BaseSharpEntity
{
    protected bool $isDashboard = true;
    protected ?string $view = null;

    final public function getViewOrFail(): SharpDashboard
    {
        if (! $view = $this->getView()) {
            throw new SharpInvalidEntityKeyException(
                sprintf('The view for the dashboard entity %s was not found.', get_class($this))
            );
        }

        return $view;
    }

    final public function hasView(): bool
    {
        return $this->getView() !== null;
    }

    protected function getLabel(): string
    {
        return $this->label;
    }

    protected function getView(): SharpDashboard
    {
        return app($this->view);
    }

    final public function isActionProhibited(string $action): bool
    {
        return false;
    }
}
