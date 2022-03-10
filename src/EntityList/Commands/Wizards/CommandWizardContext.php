<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

class CommandWizardContext
{
    protected array $attributes = [];
    
    public function put(string $name, mixed $value): self
    {
        $this->attributes[$name] = $value;
        
        return $this;
    }

    public function get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function validate(array $rules): void
    {
    }
}
