<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;

it('allows to define inline template', function () {
    $defaultFormField = SharpFormHtmlField::make('html')
        ->setTemplate('<b>test</b>');

    expect($defaultFormField->toArray())
        ->toEqual([
            'key' => 'html',
            'type' => 'html',
            'template' => '<b>test</b>',
        ]);
});

it('ensures that inline template is mandatory', function () {
    $defaultFormField = SharpFormHtmlField::make('html');

    $this->expectException(SharpFormFieldValidationException::class);
    $defaultFormField->toArray();
});

