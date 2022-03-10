<?php

namespace Code16\Sharp\EntityList\Commands\Wizards;

use Code16\Sharp\Exceptions\Commands\SharpInvalidStepException;
use Illuminate\Contracts\Validation\Factory as Validator;

class CommandWizardContext
{
    protected array $attributes = [];
    
    public function setCurrentStep(string $step): self
    {
        $this->attributes["_step"] = $step;
        
        return $this;
    }

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
        $validator = app(Validator::class)->make($this->attributes, $rules);

        if ($validator->fails()) {
            throw new SharpInvalidStepException(
                sprintf(
                    "Illegal step %s for wizard command %s", 
                    $this->attributes['_step'] ?? 'null',
                    class_basename(get_class($this))
                )
            );
        }
    }
}
