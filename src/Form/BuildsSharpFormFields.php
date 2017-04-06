<?php

namespace Code16\Sharp\Form;

use Code16\Sharp\Form\Fields\SharpFormField;

trait BuildsSharpFormFields
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * Get the fields array representation.
     *
     * @return array
     */
    function buildForm(): array
    {
        return collect($this->fields)->map(function($field) {
            return $field->toArray();
        })->all();
    }

    /**
     * Add a field.
     *
     * @param SharpFormField $field
     */
    function addField(SharpFormField $field)
    {
        $this->fields[] = $field;
    }
}