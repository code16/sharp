<?php

use Code16\Sharp\Form\Layout\FormLayoutColumn;

it('allows to set a size', function () {
    $formTab = new FormLayoutColumn(2);

    expect($formTab->toArray()['size'])
        ->toEqual(2);
});

it('allows to add a field', function () {
    $formTab = new FormLayoutColumn(2);
    $formTab->withField('name');

    expect($formTab->toArray()['fields'])
        ->toEqual([[
            ['key' => 'name', 'size' => 12],
        ]]);
});

it('allows to add a field with a specific size', function () {
    $formTabLegacy = new FormLayoutColumn(2);
    $formTabLegacy->withField('name|4');

    expect($formTabLegacy->toArray()['fields'])
        ->toEqual([[
            ['key' => 'name', 'size' => 4],
        ]]);
});

it('allows to add multiple fields with default size', function () {
    $formTab2 = new FormLayoutColumn(2);
    $formTab2->withFields('name', 'age');

    expect($formTab2->toArray()['fields'][0])
        ->toEqual([
            ['key' => 'name', 'size' => 6],
            ['key' => 'age', 'size' => 6],
        ]);

    $formTab3 = new FormLayoutColumn(2);
    $formTab3->withFields('name', 'age', 'weight');

    expect($formTab3->toArray()['fields'][0])
        ->toEqual([
            ['key' => 'name', 'size' => 4],
            ['key' => 'age', 'size' => 4],
            ['key' => 'weight', 'size' => 4],
        ]);

    $formTab4 = new FormLayoutColumn(2);
    $formTab4->withFields('name', 'age', 'weight', 'height');

    expect($formTab4->toArray()['fields'][0])
        ->toEqual([
            ['key' => 'name', 'size' => 3],
            ['key' => 'age', 'size' => 3],
            ['key' => 'weight', 'size' => 3],
            ['key' => 'height', 'size' => 3],
        ]);

    $formTab6 = new FormLayoutColumn(2);
    $formTab6->withFields('name', 'age', 'weight', 'height', 'gender', 'eyes');

    expect($formTab6->toArray()['fields'][0])
        ->toEqual([
            ['key' => 'name', 'size' => 2],
            ['key' => 'age', 'size' => 2],
            ['key' => 'weight', 'size' => 2],
            ['key' => 'height', 'size' => 2],
            ['key' => 'gender', 'size' => 2],
            ['key' => 'eyes', 'size' => 2],
        ]);
});

it('allows to add multiple fields with specific size', function () {
    $formTab = new FormLayoutColumn(2);
    $formTab->withFields(name: 4, age: 8);

    expect($formTab->toArray()['fields'][0])
        ->toEqual([
            ['key' => 'name', 'size' => 4],
            ['key' => 'age', 'size' => 8],
        ]);

    $formTabLegacy = new FormLayoutColumn(2);
    $formTabLegacy->withFields('name|4', 'age|8');

    expect($formTabLegacy->toArray()['fields'][0])
        ->toEqual([
            ['key' => 'name', 'size' => 4],
            ['key' => 'age', 'size' => 8],
        ]);
});

it('allows to insert a field', function () {
    $formTab = new FormLayoutColumn(2);
    $formTab->withField('name');
    $formTab->insertSingleFieldAt(0, 'age');

    expect($formTab->toArray()['fields'][0][0]['key'])
        ->toEqual('age')
        ->and($formTab->toArray()['fields'][1][0]['key'])
        ->toEqual('name');
});

it('allows to insert multiple fields', function () {
    $formTab = new FormLayoutColumn(2);
    $formTab->withField('name');
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
        $layout->withField('name');
    });

    expect($formTab->toArray()['fields'][0])
        ->toHaveCount(1)
        ->and($formTab->toArray()['fields'][0][0]['fields'])
        ->toHaveCount(1);
});
