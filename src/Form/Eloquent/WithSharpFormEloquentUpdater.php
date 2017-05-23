<?php

namespace Code16\Sharp\Form\Eloquent;

use Closure;
use Code16\Sharp\Form\Eloquent\Exceptions\SharpFormEloquentUpdateException;
use Code16\Sharp\Form\Transformers\SharpAttributeValuator;
use Illuminate\Database\Eloquent\Model;

trait WithSharpFormEloquentUpdater
{
    /**
     * @var array
     */
    protected $valuators = [];

    /**
     * @param string $attribute
     * @param string|Closure $valuator
     * @return $this
     */
    function setCustomValuator(string $attribute, $valuator)
    {
        $valuator = $valuator instanceof Closure
            ? $this->normalizeToSharpAttributeValuator($valuator)
            : app($valuator);

        $this->valuators[$attribute] = $valuator;

        return $this;
    }

    /**
     * Update an Eloquent Model with $data.
     *
     * @param Model $instance
     * @param array $data
     * @return bool
     */
    function save(Model $instance, array $data): bool
    {
        $relationships = [];

        foreach($data as $attribute => $value) {
            $value = $this->formatValue($value, $attribute);

            if ($customValuator = $this->customValuator($attribute)) {
                $instance->$attribute = $customValuator->getValue(
                    $instance, $attribute, $value
                );

                continue;
            }

            if($this->isRelationship($instance, $attribute)) {
                $relationships[$attribute] = [
                    "relation_type" => get_class($instance->$attribute()),
                    "value" => $value
                ];

                continue;
            }

            $instance->$attribute = $value;
        }

        // End of "normal" attributes, we save the model before handling relationships
        $instance->save();

        // Next, handle relationships
        $this->saveRelationships($instance, $relationships);

        return true;
    }

    /**
     * Return a DB compatible value depending on the field type.
     *
     * @param $value
     * @param string $attribute
     * @return mixed
     * @throws SharpFormEloquentUpdateException
     */
    protected function formatValue($value, string $attribute)
    {
        $field = $this->findFieldByKey($attribute);

        if(!$field) {
            throw new SharpFormEloquentUpdateException("Attribute [$attribute] does not match a form field");
        }

        $valueFormatter = app('Code16\Sharp\Form\Eloquent\Formatters\\'
            . ucfirst($field["type"]) . 'Formatter');

        return $valueFormatter->format($value);
    }

    /**
     * @param Closure $closure
     * @return SharpAttributeValuator
     */
    protected function normalizeToSharpAttributeValuator(Closure $closure): SharpAttributeValuator
    {
        return new class($closure) implements SharpAttributeValuator
        {
            private $closure;

            function __construct($closure)
            {
                $this->closure = $closure;
            }

            function getValue($instance, string $attribute, $value)
            {
                return call_user_func($this->closure, $instance, $value);
            }
        };
    }

    /**
     * @param string $attribute
     * @return SharpAttributeValuator|null
     */
    protected function customValuator($attribute)
    {
        return isset($this->valuators[$attribute])
            ? $this->valuators[$attribute]
            : null;
    }

    /**
     * @param Model $instance
     * @param string $attribute
     * @return bool
     */
    protected function isRelationship($instance, $attribute): bool
    {
        return method_exists($instance, $attribute);
    }

    /**
     * @param Model $instance
     * @param array $relationships
     */
    protected function saveRelationships(Model $instance, array $relationships)
    {
        foreach ($relationships as $attribute => $relation) {
            $relationshipUpdater = app('Code16\Sharp\Form\Eloquent\Relationships\\'
                . (new \ReflectionClass($relation["relation_type"]))->getShortName()
                . 'RelationUpdater');

            $relationshipUpdater->update($instance, $attribute, $relation["value"]);
        }
    }
}