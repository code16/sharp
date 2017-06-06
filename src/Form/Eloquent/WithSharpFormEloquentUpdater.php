<?php

namespace Code16\Sharp\Form\Eloquent;

use Closure;
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
        $data = $this->buildData($plainData);

        return app(SharpModelUpdater::class)->update($instance, $data);
    }
//    function save(Model $instance, array $data): bool
//    {
//        $delayedAttributes = [];
//        $relationships = [];
//
//        foreach($data as $attribute => $value) {
//
//            if($this->isRelationship($instance, $attribute)) {
//                $relationships[$attribute] = [
//                    "relation_type" => get_class($instance->$attribute()),
//                    "value" => $this->formatValueAccordingToFieldType($value, $attribute, $instance)
//                ];
//
//                continue;
//            }
//
//            try {
//                $value = $this->formatValueAccordingToFieldType($value, $attribute, $instance);
//
//            } catch(ModelNotFoundException $ex) {
//                // We try to format a field which needs the instance to be persisted.
//                // For example: the UploadFormatter needs a persisted instance if
//                // its storagePath contains a parameter, like {id}. We delay the valuate.
//                $delayedAttributes[$attribute] = $value;
//
//                continue;
//            }
//
//            $this->valuateAttribute($instance, $attribute, $value);
//        }
//
//        // End of "normal" attributes.
//        $instance->save();
//
//        // Next, handle delayed attributes
//        foreach($delayedAttributes as $attribute => $value) {
//            $this->valuateAttribute(
//                $instance, $attribute, $this->formatValueAccordingToFieldType($value, $attribute, $instance)
//            );
//        }
//        $instance->save();
//
//        // Finally, handle relationships.
//        $this->saveRelationships($instance, $relationships);
//
//        return true;
//    }

//    /**
//     * Valuates the $attribute with $value
//     *
//     * @param Model $instance
//     * @param string $attribute
//     * @param $value
//     */
//    protected function valuateAttribute(Model $instance, string $attribute, $value)
//    {
//        if ($customValuator = $this->customValuator($attribute)) {
//            $instance->$attribute = $customValuator->getValue(
//                $instance, $attribute, $value
//            );
//
//            return;
//        }
//
//        $instance->$attribute = $value;
//    }

//    /**
//     * Return a DB compatible value depending on the field type.
//     *
//     * @param $value
//     * @param string $attribute
//     * @param Model $instance
//     * @return mixed
//     * @throws SharpFormEloquentUpdateException
//     */
//    protected function formatValueAccordingToFieldType($value, string $attribute, Model $instance)
//    {
//        $field = $this->findFieldByKey($attribute);
//
//        if(!$field) {
//            throw new SharpFormEloquentUpdateException("Attribute [$attribute] does not match a form field");
//        }
//
//        $valueFormatter = app('Code16\Sharp\Form\Eloquent\Formatters\\'
//            . ucfirst($field->type()) . 'Formatter');
//
//        return $valueFormatter->format($value, $field, $instance);
//    }

//    /**
//     * @param string $attribute
//     * @return mixed
//     * @throws SharpFormEloquentUpdateException
//     */
//    protected function getFormatterAccordingToFieldType(string $attribute)
//    {
//        $field = $this->findFieldByKey($attribute);
//
//        if(!$field) {
//            throw new SharpFormEloquentUpdateException("Attribute [$attribute] does not match a form field");
//        }
//
//        return app('Code16\Sharp\Form\Eloquent\Formatters\\'
//            . ucfirst($field->type()) . 'Formatter');
//    }

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
     * @return mixed
     */
    protected function buildData(array $plainData)
    {
        $data = [];

        foreach ($plainData as $attribute => $value) {
            $field = $this->findFieldByKey($attribute);

            if($field && $field instanceof SharpFormListField) {
                $itemsData = [];
                foreach($value as $item) {
                    $itemData = [];
                    foreach($item as $itemAttribute => $itemValue) {
                        $itemField = $this->findFieldByKey("{$attribute}.{$itemAttribute}");
                        $itemData[$itemAttribute] = [
                            "value" => $itemValue,
                            "valuator" => null, //$this->customValuator($attribute),
                            "field" => $itemField
                        ];
                    }
                    $itemsData[] = $itemData;
                }

                $value = $itemsData;
            }

            $data[$attribute] = [
                "value" => $value,
                "valuator" => $this->customValuator($attribute),
                "field" => $field
            ];
        }

        return $data;
    }

//    /**
//     * @param Model $instance
//     * @param string $attribute
//     * @return bool
//     */
//    protected function isRelationship($instance, $attribute): bool
//    {
//        return method_exists($instance, $attribute);
//    }
//
//    /**
//     * @param Model $instance
//     * @param array $relationships
//     */
//    protected function saveRelationships(Model $instance, array $relationships)
//    {
//        foreach ($relationships as $attribute => $relation) {
//            $relationshipUpdater = app('Code16\Sharp\Form\Eloquent\Relationships\\'
//                . (new \ReflectionClass($relation["relation_type"]))->getShortName()
//                . 'RelationUpdater');
//
//            $relationshipUpdater->update($instance, $attribute, $relation["value"]);
//        }
//    }
}