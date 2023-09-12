<?php

use Code16\Sharp\Form\Validator\SharpValidator;
use Illuminate\Validation\ValidationException;

beforeEach(function() {
    // Bind Sharp's Validator
    app()->validator->resolver(function ($translator, $data, $rules, $messages) {
        return new SharpValidator($translator, $data, $rules, $messages);
    });
});

it('converts text suffixed data in the messages bag', function () {
    $validator = $this->app->validator->make([
        'name' => 'John Wayne',
    ], [
        'name' => 'required',
        'bio.text' => 'required',
    ]);

    $validator->passes();

    expect($validator->messages()->toArray())
        ->toEqual(['bio' => ['The bio field is required.']]);
});

it('does not convert non text suffixed data in the messages bag', function () {
    $validator = $this->app->validator->make([
    ], [
        'name' => 'required',
    ]);

    $validator->passes();

    expect($validator->messages()->toArray())
        ->toEqual(['name' => ['The name field is required.']]);
});

it('is compatible with laravel validation exception', function () {
    $exception = ValidationException::withMessages([
        'name' => ['Test'],
    ]);

    expect($exception->errors())
        ->toEqual(['name' => ['Test']]);
});
