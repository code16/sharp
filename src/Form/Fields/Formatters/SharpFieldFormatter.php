<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

abstract class SharpFieldFormatter
{

    /**
     * @var string|null
     */
    protected $instanceId;

    /**
     * @param string|null $instanceId
     * @return $this
     */
    function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    abstract function toFront(SharpFormField $field, $value);

    /**
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return mixed
     */
    abstract function fromFront(SharpFormField $field, string $attribute, $value);
}