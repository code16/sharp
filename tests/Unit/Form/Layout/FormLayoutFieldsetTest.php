<?php

use Code16\Sharp\Form\Layout\FormLayoutFieldset;

it('allows to set a legend', function () {
    $formTab = new FormLayoutFieldset('legend');

    expect($formTab->toArray()['legend'])
        ->toEqual('legend');
});

it('allows to add a field', function () {
    $formTab = new FormLayoutFieldset('legend');
    $formTab->withSingleField('name');

    expect($formTab->toArray()['fields'][0])
        ->toHaveCount(1);
});

it('allows to add multiple fields', function () {
    $formTab = new FormLayoutFieldset('legend');
    $formTab->withFields('name', 'age');

    expect($formTab->toArray()['fields'][0])
        ->toHaveCount(2);
});
