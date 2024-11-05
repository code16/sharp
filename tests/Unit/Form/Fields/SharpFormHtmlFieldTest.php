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
        ]);
});

it('allows to define view template', function () {
    $defaultFormField = SharpFormHtmlField::make('html')
        ->setTemplate(view('fixtures::test'));
    
    expect($defaultFormField->toArray())
        ->toEqual([
            'key' => 'html',
            'type' => 'html',
        ]);
});

