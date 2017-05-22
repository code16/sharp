<?php

namespace Code16\Sharp\Form\Eloquent;

use Closure;
use Code16\Sharp\Form\Eloquent\Exceptions\SharpFormEloquentUpdateException;
use Code16\Sharp\Form\Transformers\SharpAttributeUpdater;

trait WithSharpFormEloquentUpdater
{
    /**
     * @var array
     */
    protected $updaters = [];

    /**
     * @param string $attribute
     * @param string|Closure $updater
     * @return $this
     */
    function setCustomUpdater(string $attribute, $updater)
    {
        if($updater instanceof Closure) {
            // Normalize updater to a regular SharpAttributeUpdater instance
            $updater = new class($updater) implements SharpAttributeUpdater {
                private $closure;

                function __construct($closure)
                {
                    $this->closure = $closure;
                }

                function update($instance, string $attribute, $value)
                {
                    return call_user_func($this->closure, $instance, $value);
                }
            };

        } else {
            // Class name given; get an instance
            $updater = app($updater);
        }

        $this->updaters[$attribute] = $updater;

        return $this;
    }

    /**
     * Update an Eloquent Model with $data.
     *
     * @param $instance
     * @param array $data
     * @return bool
     */
    function save($instance, array $data): bool
    {
        $relationships = [];

        foreach($data as $attribute => $value) {
            if (isset($this->updaters[$attribute])) {
                // Handle custom updater
                $instance->$attribute = $this->updaters[$attribute]->update(
                    $instance, $attribute, $value
                );

            } else {
                // Standard updater, depending on field type and relationship
                $value = $this->formatValue($value, $attribute);

                if(method_exists($instance, $attribute)) {
                    // This is an Eloquent relationship
                    $relationships[$attribute] = [
                        "relation_type" => get_class($instance->$attribute()),
                        "value" => $value
                    ];

                } else {
                    $instance->$attribute = $value;
                }
            }
        }

        // End of "normal" attribute, we save the model before handling relationships
        $instance->save();

        // Next, handle relationships
        if(sizeof($relationships)) {
            $instanceMustBeSaved = false;

            foreach ($relationships as $attribute => $relation) {
                $relationshipUpdater = app('Code16\Sharp\Form\Eloquent\Relationships\Update'
                    . (new \ReflectionClass($relation["relation_type"]))->getShortName()
                    . 'Relation');

                $instanceMustBeSaved = $relationshipUpdater->update($instance, $attribute, $relation["value"])
                    || $instanceMustBeSaved;
            }

            if($instanceMustBeSaved) {
                $instance->save();
            }
        }

        return true;
    }

    protected function formatValue($value, string $attribute)
    {
        $fieldType = $this->findFieldTypeByKey($attribute);

        if(!$fieldType) {
            throw new SharpFormEloquentUpdateException("Attribute [$attribute] does not match a form field");
        }

        // TODO handle special fields

        return $value;
    }
}