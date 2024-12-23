<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;

abstract class SharpFieldFormatter
{
    protected ?string $instanceId = null;
    protected ?array $dataLocalizations = null;

    public function setInstanceId(?string $instanceId): self
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    public function setDataLocalizations(array $dataLocalizations): self
    {
        $this->dataLocalizations = $dataLocalizations;

        return $this;
    }

    /**
     * @return mixed
     */
    abstract public function toFront(SharpFormField $field, $value);

    /**
     * @return mixed
     */
    abstract public function fromFront(SharpFormField $field, string $attribute, $value);
}
