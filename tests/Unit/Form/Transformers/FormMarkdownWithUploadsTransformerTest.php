<?php

namespace Code16\Sharp\Tests\Unit\Form\Transformers;

use Code16\Sharp\Tests\SharpTestCase;

class FormMarkdownWithUploadsTransformerTest extends SharpTestCase
{

//    /** @test */
//    function we_can_transform_a_text_without_uploads()
//    {
//        $book = new class() {
//            public $content;
//        };
//        $book->content = "abc";
//
//        $transformer = new FormMarkdownWithUploadsTransformerImpl();
//
//        $this->assertEquals([
//            "text" => "abc"
//        ], $transformer->apply($book, "content"));
//    }
//
//    /** @test */
//    function we_can_transform_a_text_with_an_embedded_upload()
//    {
//        $book = new class() {
//            public $content;
//        };
//        $book->content = "![](test.png)";
//
//        $transformer = new FormMarkdownWithUploadsTransformerImpl();
//
//        $this->assertEquals([
//            ["name" => "test.png"]
//        ], $transformer->apply($book, "content")["files"]);
//    }
//
//    /** @test */
//    function we_can_transform_a_text_with_an_embedded_upload_with_alt_and_title()
//    {
//        $book = new class() {
//            public $content;
//        };
//        $book->content = "![alt](test.png \"test\")";
//
//        $transformer = new FormMarkdownWithUploadsTransformerImpl();
//
//        $this->assertEquals([
//            ["name" => "test.png"]
//        ], $transformer->apply($book, "content")["files"]);
//    }
//
//    /** @test */
//    function we_can_transform_a_text_with_many_embedded_upload()
//    {
//        $book = new class() {
//            public $content;
//        };
//        $book->content = '![](test.png)
//        kdsjhfsdjh
//        sj
//        ![](test2.png)
//        ![](test3.png)';
//
//        $transformer = new FormMarkdownWithUploadsTransformerImpl();
//
//        $this->assertEquals([
//            ["name" => "test.png"],
//            ["name" => "test2.png"],
//            ["name" => "test3.png"],
//        ], $transformer->apply($book, "content")["files"]);
//    }
}
