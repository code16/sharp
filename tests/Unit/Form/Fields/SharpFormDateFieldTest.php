<?php

use Code16\Sharp\Form\Fields\SharpFormDateField;

it('sets default values', function () {
    $defaultFormField = SharpFormDateField::make('date');

    expect($defaultFormField->toArray())
        ->toEqual([
            'key' => 'date', 'type' => 'date',
            'hasDate' => true, 'hasTime' => false,
            'minTime' => '00:00', 'maxTime' => '23:59',
            'stepTime' => 30,
            'displayFormat' => 'YYYY-MM-DD',
            'mondayFirst' => true,
            'language' => app()->getLocale(),
        ]);
});

it('allows to define hasDate and hasTime', function () {
    $dateFormField = SharpFormDateField::make('date')
        ->setHasDate();

    $dateTimeFormField = SharpFormDateField::make('date')
        ->setHasTime();

    $timeFormField = SharpFormDateField::make('date')
        ->setHasTime()
        ->setHasDate(false);

    expect($dateFormField->toArray())
        ->toHaveKey('hasDate', true)
        ->toHaveKey('hasTime', false)
        ->and($dateTimeFormField->toArray())
        ->toHaveKey('hasDate', true)
        ->toHaveKey('hasTime', true)
        ->and($timeFormField->toArray())
        ->toHaveKey('hasDate', false)
        ->toHaveKey('hasTime', true);
});

it('allows to define min and max time', function () {
    $dateTimeFormField = SharpFormDateField::make('date')
        ->setMinTime(8)
        ->setMaxTime(20, 30);

    expect($dateTimeFormField->toArray())
        ->toHaveKey('minTime', '08:00')
        ->toHaveKey('maxTime', '20:30');
});

it('allows to define a step time', function () {
    $dateFormField = SharpFormDateField::make('date')
        ->setStepTime(45);

    expect($dateFormField->toArray())
        ->toHaveKey('stepTime', 45);
});

it('allows to define monday as first day of week', function () {
    $dateFormField = SharpFormDateField::make('date')
        ->setMondayFirst();

    expect($dateFormField->toArray())
        ->toHaveKey('mondayFirst', true);
});

it('formats default displayFormat regarding date and time configuration', function () {
    $dateFormField = SharpFormDateField::make('date')
        ->setHasDate();

    $dateTimeFormField = SharpFormDateField::make('date')
        ->setHasTime();

    $timeFormField = SharpFormDateField::make('date')
        ->setHasTime()
        ->setHasDate(false);

    expect($dateFormField->toArray())
        ->toHaveKey('displayFormat', 'YYYY-MM-DD')
        ->and($dateTimeFormField->toArray())
        ->toHaveKey('displayFormat', 'YYYY-MM-DD HH:mm')
        ->and($timeFormField->toArray())
        ->toHaveKey('displayFormat', 'HH:mm');
});
