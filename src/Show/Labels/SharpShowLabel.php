<?php

namespace Code16\Sharp\Show\Labels;

use Code16\Sharp\Exceptions\Show\SharpShowLabelValidationException;
use Illuminate\Support\Facades\Validator;

abstract class SharpShowLabel
{
    /** @var string */
    protected $key;

    /** @var string */
    protected $type;

    /**
     * @param string $key
     * @param string $type
     */
    protected function __construct(string $key, string $type)
    {
        $this->key = $key;
        $this->type = $type;
    }

    /**
     * @param array $childArray
     * @return array
     * @throws SharpShowLabelValidationException
     */
    protected function buildArray(array $childArray)
    {
        $array = collect([
                "key" => $this->key,
                "type" => $this->type,
            ] + $childArray)
            ->filter(function($value) {
                return !is_null($value);
            })
            ->all();

        $this->validate($array);

        return $array;
    }

    /**
     * Throw an exception in case of invalid attribute value.
     *
     * @param array $properties
     * @throws SharpShowLabelValidationException
     */
    protected function validate(array $properties)
    {
        $validator = Validator::make($properties, [
            'key' => 'required',
            'type' => 'required',
        ] + $this->validationRules());

        if ($validator->fails()) {
            throw new SharpShowLabelValidationException($validator->errors());
        }
    }

    /**
     * Return specific validation rules.
     *
     * @return array
     */
    protected function validationRules()
    {
        return [];
    }

    /**
     * Create the properties array for the field, using parent::buildArray()
     *
     * @return array
     */
    public abstract function toArray(): array;
}