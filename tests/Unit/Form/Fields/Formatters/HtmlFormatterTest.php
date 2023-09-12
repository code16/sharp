<?php

use Code16\Sharp\Form\Fields\Formatters\HtmlFormatter;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Illuminate\Support\Str;

it('allows to format value to front', function () {
    $value = Str::random();

    $this->assertEquals($value, (new HtmlFormatter())->toFront(SharpFormHtmlField::make('html'), $value));
});

it('allows to format value from front', function () {
    $this->assertNull(
        (new HtmlFormatter())->fromFront(SharpFormHtmlField::make('html'), 'attribute', Str::random()),
    );
});
