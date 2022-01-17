<?php

namespace Code16\Sharp\Tests\Unit\Utils\Transformers;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Transformers\Attributes\MarkdownAttributeTransformer;

class MarkdownAttributeTransformerTest extends SharpTestCase
{
    /** @test */
    public function we_can_transform_a_text_to_markdown()
    {
        $object = (object) [
            'text' => 'some basic *markdown* **test**',
        ];

        $this->assertEquals(
            "<p>some basic <em>markdown</em> <strong>test</strong></p>\n",
            (new MarkdownAttributeTransformer())->apply($object->text, $object, 'text')
        );
    }
}
