<?php

use Code16\Sharp\Form\Fields\SharpFormHtmlField;

it('allows to define inline template', function () {
    $defaultFormField = SharpFormHtmlField::make('html')
        ->setTemplate('<b>test</b>');

    expect($defaultFormField->toArray())
        ->toEqual([
            'key' => 'html',
            'type' => 'html',
            'liveRefresh' => false,
        ]);
});

it('allows to define view template', function () {
    $defaultFormField = SharpFormHtmlField::make('html')
        ->setTemplate(view('fixtures::test'));

    expect($defaultFormField->toArray())
        ->toEqual([
            'key' => 'html',
            'type' => 'html',
            'liveRefresh' => false,
        ]);
});

it('allows to define with live refresh', function () {
    $defaultFormField = SharpFormHtmlField::make('html')
        ->setLiveRefresh(linkedFields: ['field1', 'field2'])
        ->setTemplate(function ($data) {
            return '<b>test</b>';
        });

    expect($defaultFormField->toArray())
        ->toEqual([
            'key' => 'html',
            'type' => 'html',
            'liveRefresh' => true,
            'liveRefreshLinkedFields' => ['field1', 'field2'],
        ]);
});
