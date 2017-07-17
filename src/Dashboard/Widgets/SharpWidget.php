<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Code16\Sharp\Exceptions\Dashboard\SharpWidgetValidationException;
use Illuminate\Support\Facades\Validator;

abstract class SharpWidget
{

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $title;

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
     * @param string $title
     * @return static
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Throw an exception in case of invalid attribute value.
     *
     * @param array $properties
     * @throws SharpWidgetValidationException
     */
    protected function validate(array $properties)
    {
        $validator = Validator::make($properties, [
                'key' => 'required',
                'type' => 'required',
            ] + $this->validationRules());

        if ($validator->fails()) {
            throw new SharpWidgetValidationException($validator->errors());
        }
    }

    /**
     * @param array $childArray
     * @return array
     */
    protected function buildArray(array $childArray)
    {
        $array = collect([
            "key" => $this->key,
            "type" => $this->type,
            "title" => $this->title,
        ] + $childArray)
        ->filter(function($value) {
            return !is_null($value);
        })->all();

        $this->validate($array);

        return $array;
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
}