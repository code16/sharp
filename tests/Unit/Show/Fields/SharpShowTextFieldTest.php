<?php

use Code16\Sharp\Show\Fields\SharpShowTextField;

it('allows to define label', function () {
    $field = SharpShowTextField::make('textfield')
        ->setLabel('Label');

    expect($field->toArray())
        ->toEqual([
            'key' => 'textfield',
            'type' => 'text',
            'emptyVisible' => false,
            'html' => true,
            'sanitize' => true,
            'label' => 'Label',
        ]);
});

it('handles collapseWordCount', function () {
    $field = SharpShowTextField::make('textfield')
        ->collapseToWordCount(15);

    expect($field->toArray()['collapseToWordCount'])->toEqual(15);
});

it('handles showIfEmpty', function () {
    $field = SharpShowTextField::make('textfield')
        ->setShowIfEmpty();

    expect($field->toArray()['emptyVisible'])->toBeTrue();
});

it('handles html', function () {
    $field = SharpShowTextField::make('textfield')
        ->setHtml(false);

    expect($field->toArray()['emptyVisible'])->toBeFalse();
});

it('allows to reset collapseWordCount', function () {
    $field = SharpShowTextField::make('textfield')
        ->collapseToWordCount(15);

    $field->doNotCollapse();

    expect($field->toArray())->not->toHaveKey('collapseToWordCount');
});

it('handle localized attribute', function () {
    $field = SharpShowTextField::make('text')
        ->setLocalized(false);

    expect($field->toArray())->not->toHaveKey('localized');

    $field->setLocalized();

    expect($field->toArray())->toHaveKey('localized', true);
});

it('allows to disable HTML sanitization', function () {
    $field = SharpShowTextField::make('textfield')
        ->setSanitizeHtml(false)
        ->setLabel('Label');

    expect($field->toArray()['sanitize'])->toBe(false);
});
