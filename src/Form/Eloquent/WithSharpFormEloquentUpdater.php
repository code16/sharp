<?php

namespace Code16\Sharp\Form\Eloquent;

use Closure;
use Code16\Sharp\Form\Eloquent\Request\UpdateRequestData;
use Code16\Sharp\Form\Fields\SharpFormListField;
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
     * @param array $plainData
     * @return bool
     */
    function save(Model $instance, array $plainData): bool
    {
        return app(EloquentModelUpdater::class)
            ->update($instance, $this->buildData($plainData));
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
     * @param array $plainData
     * @return UpdateRequestData
     */
    protected function buildData(array $plainData)
    {
        $data = new UpdateRequestData;

        foreach ($plainData as $attribute => $value) {
            $field = $this->findFieldByKey($attribute);

            if($field instanceof SharpFormListField) {
                $listValue = [];

                foreach ($value as $item) {
                    $itemData = new UpdateRequestData;

                    foreach ($item as $itemAttribute => $itemValue) {
                        $itemField = $this->findFieldByKey("{$attribute}.{$itemAttribute}");

                        $itemData->add($itemAttribute)
                            ->setValue($itemValue)
                            ->setValuator($this->customValuator("{$attribute}.{$itemAttribute}"))
                            ->setFormField($itemField);
                    }

                    $listValue[] = $itemData;
                }

                // The value is an array of UpdateRequestData
                $value = $listValue;
            }

            $data->add($attribute)
                ->setValue($value)
                ->setValuator($this->customValuator($attribute))
                ->setFormField($field);
        }

        return $data;
    }
}