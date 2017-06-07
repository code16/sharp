<?php

namespace Code16\Sharp\Form\Eloquent\Request;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Transformers\SharpAttributeValuator;

class UpdateRequestDataItem
{

    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var
     */
    protected $value;

    /**
     * @var SharpAttributeValuator
     */
    protected $valuator;

    /**
     * @var SharpFormField
     */
    protected $formField;

    /**
     * @param string $attribute
     */
    public function __construct(string $attribute)
    {
        $this->attribute = $attribute;
    }


    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param SharpAttributeValuator $customValuator
     * @return $this
     */
    public function setValuator($customValuator)
    {
        $this->valuator = $customValuator;

        return $this;
    }

    /**
     * @param SharpFormField $field
     * @return $this
     */
    public function setFormField($field)
    {
        $this->formField = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function attribute()
    {
        return $this->attribute;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function valuator()
    {
        return $this->valuator;
    }

    /**
     * @return SharpFormField
     */
    public function formField()
    {
        return $this->formField;
    }

    /**
     * @param Model $instance
     * @return mixed
     */
    public function formattedValue($instance)
    {
        $field = $this->formField();

        if(!$field) return null;

        $formatter = app('Code16\Sharp\Form\Eloquent\Formatters\\'
            . ucfirst($field->type()) . 'Formatter');

        return $formatter->format(
            $this->value(), $this->formField(), $instance
        );
    }
}