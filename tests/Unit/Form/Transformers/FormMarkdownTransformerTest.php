<?php

namespace Code16\Sharp\Tests\Unit\Form\Transformers;

use Code16\Sharp\Form\Transformers\FormMarkdownTransformer;
use Code16\Sharp\Tests\SharpTestCase;

class FormMarkdownTransformerTest extends SharpTestCase
{

    /** @test */
    function we_can_transform_a_regular_attribute()
    {
        $book = new class() {
            public $content;
        };
        $book->content = "abc";

        $transformer = new FormMarkdownTransformer();

        $this->assertEquals([
            "text" => "abc",
        ], $transformer->apply($book, "content"));
    }
}