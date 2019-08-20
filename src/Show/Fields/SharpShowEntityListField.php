<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Http\SharpContext;

class SharpShowEntityListField extends SharpShowField
{
    const FIELD_TYPE = "entityList";

    /** @var string */
    protected $entityListKey;

    /** @var array */
    protected $filters = [];

    /** @var array */
    protected $commands = [
        "entity" => [],
        "instance" => []
    ];

    /** @var bool */
    protected $showEntityState = false;

    /** @var bool */
    protected $showReorderButton = false;

    /** @var bool */
    protected $showCreateButton = false;

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
     * @param array $filterNames
     * @return $this
     */
    public function showFilters(array $filterNames)
    {
        foreach($filterNames as $filterName) {
            $this->filters[$filterName]["display"] = true;
        }

        return $this;
    }

    /**
     * @param string $filterName
     * @param $value
     * @return $this
     */
    public function setFilterValue(string $filterName, $value)
    {
        $this->filters[$filterName]["value"] = $value;

        return $this;
    }

    /**
     * @param array $commands
     * @return $this
     */
    public function showEntityCommands(array $commands)
    {
        foreach($commands as $command) {
            $this->commands["entity"][$command]["display"] = true;
        }

        return $this;
    }

    /**
     * @param array $commands
     * @return $this
     */
    public function showInstanceCommands(array $commands)
    {
        foreach($commands as $command) {
            $this->commands["instance"][$command]["display"] = true;
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
     * Create the properties array for the field, using parent::buildArray()
     *
     * @return array
     * @throws \Code16\Sharp\Exceptions\Show\SharpShowFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "entityListKey" => $this->entityListKey,
            "showEntityState" => $this->showEntityState,
            "showCreateButton" => $this->showCreateButton,
            "showReorderButton" => $this->showReorderButton,
            "commands" => $this->commands,
            "filters" => collect($this->filters)
                ->map(function($values, $filter) {
                    // Force display to be set
                    $values["display"] = $values["display"] ?? false;

                    // Filter value can be a Closure
                    if(isset($values["value"]) && is_callable($values["value"])) {
                        // Call it with current instanceId from Context
                        $values["value"] = $values["value"](app(SharpContext::class)->instanceId());
                    }

                    return $values;
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
            "commands" => "array",
            "commands.*.entity" => "array",
            "commands.*.instance" => "array",
            "filters" => "array",
            "filters.*.display" => "required|boolean",
        ];
    }
}