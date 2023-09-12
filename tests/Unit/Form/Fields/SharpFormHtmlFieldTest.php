<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;

it('allows to define inline template', function () {
    $defaultFormField = SharpFormHtmlField::make('html')
        ->setInlineTemplate('<b>test</b>');

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

it('allows to define templateData', function () {
    $formField = SharpFormHtmlField::make('html')
        ->setInlineTemplate('<b>test</b>')
        ->setAdditionalTemplateData([
            'lang' => ['fr', 'de'],
        ]);

    expect($formField->toArray())
        ->toHaveKey('templateData', [
            'lang' => ['fr', 'de'],
        ]);
});
