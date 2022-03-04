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
     *
     * @return $this
     */
    public function setInstanceId($instanceId)
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    /**
     * @param SharpFormField $field
     * @param $value
     *
     * @return mixed
     */
    abstract public function toFront(SharpFormField $field, $value);

    /**
     * @param SharpFormField $field
     * @param string         $attribute
     * @param $value
     *
     * @return mixed
     */
    abstract public function fromFront(SharpFormField $field, string $attribute, $value);
}
