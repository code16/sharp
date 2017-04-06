<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Exceptions\SharpFormFieldValidationException;
use Illuminate\Support\Facades\Validator;

abstract class SharpFormField
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $helpMessage;

    /**
     * @var string
     */
    protected $conditionalDisplay;

    /**
     * @var bool
     */
    protected $readOnly;

    /**
     * @var string
     */
    protected $extraStyle;

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
     * @param string $label
     * @return static
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param string $helpMessage
     * @return static
     */
    public function setHelpMessage(string $helpMessage)
    {
        $this->helpMessage = $helpMessage;

        return $this;
    }

    /**
     * @param string $conditionalDisplay
     * @return static
     */
    public function setConditionalDisplay(string $conditionalDisplay)
    {
        $this->conditionalDisplay = $conditionalDisplay;

        return $this;
    }

    /**
     * @param bool $readOnly
     * @return static
     */
    public function setReadOnly(bool $readOnly = true)
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    /**
     * @param string $style
     * @return static
     */
    public function setExtraStyle(string $style)
    {
        $this->extraStyle = $style;

        return $this;
    }

    /**
     * Create the properties array for the field, using parent::buildArray()
     *
     * @return array
     */
    public abstract function toArray(): array;

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
     * Throw an exception in case of invalid attribute value.
     * @param array $properties
     * @throws SharpFormFieldValidationException
     */
    protected function validate(array $properties)
    {
        $validator = Validator::make($properties, [
            'key' => 'required',
            'type' => 'required',
        ] + $this->validationRules());

        if ($validator->fails()) {
            throw new SharpFormFieldValidationException($validator->errors());
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
            "label" => $this->label,
            "readOnly" => $this->readOnly,
            "conditionalDisplay" => $this->conditionalDisplay,
            "helpMessage" => $this->helpMessage,
            "extraStyle" => $this->extraStyle,
        ] + $childArray)
            ->filter(function($value) {
                return !is_null($value);
            })->all();

        $this->validate($array);

        return $array;
    }
}