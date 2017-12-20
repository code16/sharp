<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter;
use Illuminate\Support\Facades\Validator;

abstract class SharpFormField
{
    /**
     * @var string
     */
    public $key;

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
    protected $conditionalDisplayOperator = "and";

    /**
     * @var array
     */
    protected $conditionalDisplayFields;

    /**
     * @var bool
     */
    protected $readOnly;

    /**
     * @var string
     */
    protected $extraStyle;

    /**
     * @var SharpFieldFormatter
     */
    protected $formatter;

    /**
     * @param string $key
     * @param string $type
     * @param SharpFieldFormatter|null $formatter
     */
    protected function __construct(string $key, string $type, SharpFieldFormatter $formatter = null)
    {
        $this->key = $key;
        $this->type = $type;
        $this->formatter = $formatter;
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
     * @param string $fieldKey
     * @param array|string|boolean $values
     * @return static
     */
    public function addConditionalDisplay(string $fieldKey, $values = true)
    {
        if(substr($fieldKey, 0, 1) === "!") {
            $fieldKey = substr($fieldKey, 1);
            $values = false;
        }

        $this->conditionalDisplayFields[] = [
            "key" => $fieldKey,
            "values" => $values
        ];

        return $this;
    }

    /**
     * @return static
     */
    public function setConditionalDisplayOrOperator()
    {
        $this->conditionalDisplayOperator = "or";

        return $this;
    }

    /**
     * @return static
     */
    public function setConditionalDisplayAndOperator()
    {
        $this->conditionalDisplayOperator = "and";

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
     * @param SharpFieldFormatter $formatter
     * @return static
     */
    public function setFormatter(SharpFieldFormatter $formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * Create the properties array for the field, using parent::buildArray()
     *
     * @return array
     */
    public abstract function toArray(): array;

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * @return SharpFieldFormatter
     */
    public function formatter()
    {
        return $this->formatter;
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
     * Throw an exception in case of invalid attribute value.
     *
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
     * @throws SharpFormFieldValidationException
     */
    protected function buildArray(array $childArray)
    {
        $array = collect([
            "key" => $this->key,
            "type" => $this->type,
            "label" => $this->label,
            "readOnly" => $this->readOnly,
            "conditionalDisplay" => $this->buildConditionalDisplayArray(),
            "helpMessage" => $this->helpMessage,
            "extraStyle" => $this->extraStyle,
        ] + $childArray)
            ->filter(function($value) {
                return !is_null($value);
            })->all();

        $this->validate($array);

        return $array;
    }

    /**
     * @return array|null
     */
    private function buildConditionalDisplayArray()
    {
        if(!sizeof($this->conditionalDisplayFields)) {
            return null;
        }

        return [
            "operator" => $this->conditionalDisplayOperator,
            "fields" => $this->conditionalDisplayFields
        ];
    }
}