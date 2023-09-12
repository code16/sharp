<?php

use Carbon\Carbon;
use Code16\Sharp\Form\Fields\Formatters\DateFormatter;
use Code16\Sharp\Form\Fields\SharpFormDateField;

it('allows to format datetime value to front', function () {
    $formatter = new DateFormatter;
    $field = SharpFormDateField::make('date');
    $field->setHasDate();
    $field->setHasTime();

    expect($formatter->toFront($field, '2017-05-31 10:30:00'))->toEqual('2017-05-31T10:30:00.000000Z')
        ->and($formatter->toFront($field, Carbon::create(2017, 05, 31, 10, 30, 0)))->toEqual('2017-05-31T10:30:00.000000Z')
        ->and($formatter->toFront($field, new \DateTime('2017-05-31 10:30:00')))->toEqual('2017-05-31T10:30:00.000000Z');
});

it('allows to format date only value to front', function () {
    $formatter = new DateFormatter;
    $field = SharpFormDateField::make('date');
    $field->setHasDate();
    $field->setHasTime(false);

    expect($formatter->toFront($field, '2017-05-31'))->toEqual('2017-05-31T00:00:00.000000Z')
        ->and($formatter->toFront($field, Carbon::create(2017, 05, 31)->startOfDay()))->toEqual('2017-05-31T00:00:00.000000Z')
        ->and($formatter->toFront($field, new \DateTime('2017-05-31')))->toEqual('2017-05-31T00:00:00.000000Z');
});

it('allows to format time only value to front', function () {
    $formatter = new DateFormatter;
    $field = SharpFormDateField::make('date');
    $field->setHasDate(false);
    $field->setHasTime(true);

    expect($formatter->toFront($field, '10:30:00'))->toEqual('10:30')
        ->and($formatter->toFront($field, Carbon::create(1970, 01, 01, 10, 30, 00)))->toEqual('10:30')
        ->and($formatter->toFront($field, new \DateTime('10:30:00')))->toEqual('10:30');
});

it('allows to format datetime value from front', function () {
    $formatter = new DateFormatter;
    $field = SharpFormDateField::make('date');
    $field->setHasDate();
    $field->setHasTime();

    expect($formatter->fromFront($field, 'attr', '2017-05-31 10:30:00'))
        ->toEqual('2017-05-31 10:30:00');
});

it('allows to format date only value from front', function () {
    $formatter = new DateFormatter;
    $field = SharpFormDateField::make('date');
    $field->setHasDate();
    $field->setHasTime(false);

    expect($formatter->fromFront($field, 'attr', '2017-05-31'))
        ->toEqual('2017-05-31')
        ->and($formatter->fromFront($field, 'attr', '2017-05-31 10:30:00'))
        ->toEqual('2017-05-31');
});

it('allows to format time only value from front', function () {
    $formatter = new DateFormatter;
    $field = SharpFormDateField::make('date');
    $field->setHasDate(false);
    $field->setHasTime();

    expect($formatter->fromFront($field, 'attr', '10:30:00'))
        ->toEqual('10:30:00')
        ->and($formatter->fromFront($field, 'attr', '2017-05-31 10:30:00'))
        ->toEqual('10:30:00');
});

it('handles timezone from front', function () {
    $formatter = new DateFormatter;
    $field = SharpFormDateField::make('date');
    $field->setHasTime();

    config(['app.timezone' => 'Europe/Paris']); //GMT+2
    expect($formatter->fromFront($field, 'attr', '2017-05-31T13:00:00.000000Z'))
        ->toEqual('2017-05-31 15:00:00');

    config(['app.timezone' => 'America/Montreal']); //GMT-4
    expect($formatter->fromFront($field, 'attr', '2017-05-31T13:00:00.000000Z'))
        ->toEqual('2017-05-31 09:00:00');
});
