<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Http\Context\CurrentSharpRequest;

class SharpShowEntityListField extends SharpShowField
{
    const FIELD_TYPE = "entityList";

    protected string $entityListKey;
    protected array $hiddenFilters = [];
    protected array $hiddenCommands = [
        "entity" => [],
        "instance" => []
    ];
    protected bool $showEntityState = true;
    protected bool $showReorderButton = true;
    protected bool $showCreateButton = true;
    protected bool $showSearchField = true;
    protected ?string $label = null;

    public static function make(string $key, string $entityListKey): SharpShowEntityListField
    {
        return tap(new static($key, static::FIELD_TYPE), function($instance) use($entityListKey) {
            $instance->entityListKey = $entityListKey;
        });
    }

    /**
     * @param string $filterName
     * @param mixed $value
     * @return $this
     */
    public function hideFilterWithValue(string $filterName, $value): self
    {
        $this->hiddenFilters[$filterName] = $value;

        return $this;
    }

    /**
     * @param array|string $commands
     * @return $this
     */
    public function hideEntityCommand($commands): self
    {
        foreach((array)$commands as $command) {
            $this->hiddenCommands["entity"][] = $command;
        }

        return $this;
    }

    /**
     * @param array|string $commands
     * @return $this
     */
    public function hideInstanceCommand($commands): self
    {
        foreach((array)$commands as $command) {
            $this->hiddenCommands["instance"][] = $command;
        }

        return $this;
    }

    public function showEntityState(bool $showEntityState = true): self
    {
        $this->showEntityState = $showEntityState;

        return $this;
    }

    public function showCreateButton(bool $showCreateButton = true): self
    {
        $this->showCreateButton = $showCreateButton;

        return $this;
    }

    public function showReorderButton(bool $showReorderButton = true): self
    {
        $this->showReorderButton = $showReorderButton;

        return $this;
    }

    public function showSearchField(bool $showSearchField = true): self
    {
        $this->showSearchField = $showSearchField;

        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Create the properties array for the field, using parent::buildArray()
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "label" => $this->label,
            "entityListKey" => $this->entityListKey,
            "showEntityState" => $this->showEntityState,
            "showCreateButton" => $this->showCreateButton,
            "showReorderButton" => $this->showReorderButton,
            "showSearchField" => $this->showSearchField,
            "hiddenCommands" => $this->hiddenCommands,
            "hiddenFilters" => sizeof($this->hiddenFilters) 
                ? collect($this->hiddenFilters)
                    ->map(function($value, $filter) {
                        // Filter value can be a Closure
                        if(is_callable($value)) {
                            // Call it with current instanceId
                            return $value(currentSharpRequest()->instanceId());
                        }
    
                        return $value;
                    })
                    ->all()
                : null
        ]);
    }

    protected function validationRules(): array
    {
        return [
            "entityListKey" => "required",
            "showEntityState" => "required|boolean",
            "showCreateButton" => "required|boolean",
            "showReorderButton" => "required|boolean",
            "hiddenCommands" => "required|array",
            "hiddenCommands.entity" => "array",
            "hiddenCommands.instance" => "array",
            "hiddenFilters" => "array",
        ];
    }
}