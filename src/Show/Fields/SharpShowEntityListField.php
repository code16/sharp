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
     * Create the properties array for the field, using parent::buildArray()
     *
     * @return array
     * @throws \Code16\Sharp\Exceptions\Show\SharpShowFieldValidationException
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "entityListKey" => $this->entityListKey,
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
            "filters" => "array",
            "filters.*.display" => "required|boolean",
        ];
    }
}