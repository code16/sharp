<?php

use Code16\Sharp\Form\Layout\FormLayoutColumn;

it('allows to set a size', function () {
    $formTab = new FormLayoutColumn(2);

    expect($formTab->toArray()['size'])
        ->toEqual(2);
});

it('allows to add a field', function () {
    $formTab = new FormLayoutColumn(2);
    $formTab->withSingleField('name');

    expect($formTab->toArray()['fields'])
        ->toHaveCount(1);
});

it('allows to add multiple fields', function () {
    $formTab = new FormLayoutColumn(2);
    $formTab->withFields('name', 'age');

    expect($formTab->toArray()['fields'][0])
        ->toHaveCount(2);
});

it('allows to insert a field', function () {
    $formTab = new FormLayoutColumn(2);
    $formTab->withSingleField('name');
    $formTab->insertSingleFieldAt(0, 'age');

    expect($formTab->toArray()['fields'][0][0]['key'])
        ->toEqual('age')
        ->and($formTab->toArray()['fields'][1][0]['key'])
        ->toEqual('name');
});

it('allows to insert multiple fields', function () {
    $formTab = new FormLayoutColumn(2);
    $formTab->withSingleField('name');
    $formTab->insertFieldsAt(0, 'age', 'size');

    expect($formTab->toArray()['fields'][0][0]['key'])
        ->toEqual('age')
        ->and($formTab->toArray()['fields'][0][1]['key'])
        ->toEqual('size')
        ->and($formTab->toArray()['fields'][1][0]['key'])
        ->toEqual('name');
});

it('allows to add a fieldset', function () {
    $formTab = new FormLayoutColumn(2);
    $formTab->withFieldset('label', function ($layout) {
        $layout->withSingleField('name');
    });

    expect($formTab->toArray()['fields'][0])
        ->toHaveCount(1)
        ->and($formTab->toArray()['fields'][0][0]['fields'])
        ->toHaveCount(1);
});
