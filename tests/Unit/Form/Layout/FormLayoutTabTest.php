<?php

use Code16\Sharp\Form\Layout\FormLayoutTab;

it('allows to set a title', function () {
    $formTab = new FormLayoutTab('test');

    expect($formTab->toArray()['title'])
        ->toEqual('test');
});

it('allows to add a column', function () {
    $formTab = new FormLayoutTab('test');
    $formTab->addColumn(2);

    expect($formTab->toArray()['columns'])
        ->toHaveCount(1);
});

it('allows to add several columns', function () {
    $formTab = new FormLayoutTab('test');
    $formTab->addColumn(2)
        ->addColumn(2)
        ->addColumn(2);

    expect($formTab->toArray()['columns'])
        ->toHaveCount(3);
});
