<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Utils\Fields\Formatters\FormatsLocalizedValue;

abstract class SharpFieldFormatter
{
    use FormatsLocalizedValue;

    protected ?string $instanceId = null;

    public function setInstanceId(?string $instanceId): static
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    abstract public function toFront(SharpFormField $field, $value);

    abstract public function fromFront(SharpFormField $field, string $attribute, $value);
}
