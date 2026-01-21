<?php

namespace Code16\Sharp\EntityList\Traits;

trait HandleInstancePrimaryCommands
{
    protected array $primaryInstanceCommandKeys = [];

    protected function configurePrimaryInstanceCommands(array $commandKeyOrClassNames): self
    {
        $this->primaryInstanceCommandKeys = collect($commandKeyOrClassNames)
            ->map(fn ($key) => class_exists($key) ? class_basename($key) : $key)
            ->all();

        return $this;
    }
}
