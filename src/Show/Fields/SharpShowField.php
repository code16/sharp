<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Exceptions\Show\SharpShowFieldValidationException;
use Illuminate\Support\Facades\Validator;

abstract class SharpShowField
{
    /** @var string */
    public $key;

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
     * @throws SharpShowFieldValidationException
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
     * @throws SharpShowFieldValidationException
     */
    protected function validate(array $properties)
    {
        $validator = Validator::make($properties, 
            array_merge(
                [
                    'key' => 'required',
                    'type' => 'required',
                ], 
                $this->validationRules()
            )
        );

        if ($validator->fails()) {
            throw new SharpShowFieldValidationException($validator->errors());
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