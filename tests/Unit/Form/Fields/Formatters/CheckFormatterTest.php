<?php

use Code16\Sharp\Form\Fields\Formatters\CheckFormatter;
use Code16\Sharp\Form\Fields\SharpFormCheckField;

it('allows to format value to front', function () {
    $formatter = new CheckFormatter();
    $field = SharpFormCheckField::make('check', 'text');

    expect($formatter->toFront($field, true))->toBeTrue()
        ->and($formatter->toFront($field, 1))->toEqual(1)
        ->and($formatter->toFront($field, false))->toBeFalse()
        ->and($formatter->toFront($field, 0))->toEqual(0);
});

it('allows to format value from front', function () {
    $formatter = new CheckFormatter();
    $field = SharpFormCheckField::make('check', 'text');
    $attribute = 'attribute';

    expect($formatter->fromFront($field, $attribute, true))->toBeTrue()
        ->and($formatter->fromFront($field, $attribute, 'true'))->toBeTrue()
        ->and($formatter->fromFront($field, $attribute, 1))->toBeTrue()
        ->and($formatter->fromFront($field, $attribute, '1'))->toBeTrue()
        ->and($formatter->fromFront($field, $attribute, 'ok'))->toBeTrue()
        ->and($formatter->fromFront($field, $attribute, 'on'))->toBeTrue()
        ->and($formatter->fromFront($field, $attribute, false))->toBeFalse()
        ->and($formatter->fromFront($field, $attribute, 'false'))->toBeFalse()
        ->and($formatter->fromFront($field, $attribute, '0'))->toBeFalse()
        ->and($formatter->fromFront($field, $attribute, 0))->toBeFalse()
        ->and($formatter->fromFront($field, $attribute, ''))->toBeFalse();
});
