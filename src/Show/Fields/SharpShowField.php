<?php

namespace Code16\Sharp\Show\Fields;

use Code16\Sharp\Exceptions\Show\SharpShowFieldValidationException;
use Illuminate\Support\Facades\Validator;

abstract class SharpShowField
{
    public string $key;
    protected string $type;
    protected bool $emptyVisible = false;

    protected function __construct(string $key, string $type)
    {
        $this->key = $key;
        $this->type = $type;
    }

    public function setShowIfEmpty(bool $showIfEmpty = true): self
    {
        $this->emptyVisible = $showIfEmpty;
        
        return $this;
    }

    protected function buildArray(array $childArray): array
    {
        $array = collect([
                "key" => $this->key,
                "type" => $this->type,
                "emptyVisible" => $this->emptyVisible
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
    protected function validate(array $properties): void
    {
        $validator = Validator::make($properties, 
            array_merge(
                [
                    'key' => 'required',
                    'type' => 'required',
                    'emptyVisible' => 'required|bool'
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
     */
    protected function validationRules(): array
    {
        return [];
    }

    /**
     * Create the properties array for the field, using parent::buildArray()
     */
    public abstract function toArray(): array;
}