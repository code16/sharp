<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Http\SharpContext;

class SharpShowEntityListField extends SharpShowField
{
    const FIELD_TYPE = "entityList";

    /** @var string */
    protected $entityListKey;

    /** @var array */
    protected $hiddenFilters = [];

    /** @var array */
    protected $hiddenCommands = [
        "entity" => [],
        "instance" => []
    ];

    /** @var bool */
    protected $showEntityState = true;

    /** @var bool */
    protected $showReorderButton = true;

    /** @var bool */
    protected $showCreateButton = true;

    /** @var bool */
    protected $showSearchField = true;

    /** @var string */
    protected $label = null;

    /**
     * @param string $key
     * @param string $entityListKey
     * @return static
     */
    public static function make(string $key, string $entityListKey)
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
    public function hideFilterWithValue(string $filterName, $value)
    {
        $this->hiddenFilters[$filterName] = $value;

        return $this;
    }

    /**
     * @param array|string $commands
     * @return $this
     */
    public function hideEntityCommand($commands)
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
    public function hideInstanceCommand($commands)
    {
        foreach((array)$commands as $command) {
            $this->hiddenCommands["instance"][] = $command;
        }

        return $this;
    }

    /**
     * @param bool $showEntityState
     * @return $this
     */
    public function showEntityState(bool $showEntityState = true)
    {
        $this->showEntityState = $showEntityState;

        return $this;
    }

    /**
     * @param bool $showCreateButton
     * @return $this
     */
    public function showCreateButton(bool $showCreateButton = true)
    {
        $this->showCreateButton = $showCreateButton;

        return $this;
    }

    /**
     * @param bool $showReorderButton
     * @return $this
     */
    public function showReorderButton(bool $showReorderButton = true)
    {
        $this->showReorderButton = $showReorderButton;

        return $this;
    }

    /**
     * @param bool $showSearchField
     * @return $this
     */
    public function showSearchField(bool $showSearchField = true)
    {
        $this->showSearchField = $showSearchField;

        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Create the properties array for the field, using parent::buildArray()
     *
     * @return array
     * @throws \Code16\Sharp\Exceptions\Show\SharpShowFieldValidationException
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
            "hiddenFilters" => collect($this->hiddenFilters)
                ->map(function($value, $filter) {
                    // Filter value can be a Closure
                    if(is_callable($value)) {
                        // Call it with current instanceId from Context
                        return $value(app(SharpContext::class)->instanceId());
                    }

                    return $value;
                })
                ->all()
        ]);
    }

    /**
     * @return array
     */
    protected function validationRules()
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