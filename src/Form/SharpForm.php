<?php

namespace Code16\Sharp\Form;

interface SharpForm
{

    /**
     * Get the SharpFormField array representation.
     *
     * @return array
     */
    function fields(): array;

    /**
     * Return the form fields layout.
     *
     * @return array
     */
    function formLayout(): array;
}