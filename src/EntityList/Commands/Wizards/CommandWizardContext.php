<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

class CommandWizardContext
{

    public function put(string $name, mixed $value): self
    {
        return $this;
    }

    public function get(string $name): mixed
    {
        return null;
    }

    public function validate(array $rules): void
    {
    }
}