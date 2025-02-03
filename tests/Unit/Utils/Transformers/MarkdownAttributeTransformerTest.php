<?php

use Code16\Sharp\Utils\Transformers\Attributes\MarkdownAttributeTransformer;

it('transforms a text to markdown', function () {
    $object = (object) [
        'text' => 'some basic *markdown* **test**',
    ];

    $this->assertEquals(
        "<p>some basic <em>markdown</em> <strong>test</strong></p>\n",
        (new MarkdownAttributeTransformer())->apply($object->text, $object, 'text'),
    );
});
