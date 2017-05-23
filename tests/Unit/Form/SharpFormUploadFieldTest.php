<?php

namespace Code16\Sharp\Tests\Unit\Form;

use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormUploadFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $formField = SharpFormUploadField::make("file");

        $this->assertEquals([
                "key" => "file", "type" => "upload"
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_maxFileSize()
    {
        $formField = SharpFormUploadField::make("text")
            ->setMaxFileSize(.5);

        $this->assertArraySubset(
            ["maxFileSize" => 0.5],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_fileFilter()
    {
        $formField = SharpFormUploadField::make("text")
            ->setFileFilter("jpg");

        $this->assertArraySubset(
            ["fileFilter" => "jpg"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_thumbnail()
    {
        $formField = SharpFormUploadField::make("text")
            ->setThumbnail("800x600");

        $this->assertArraySubset(
            ["thumbnail" => "800x600"],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_cropRatio()
    {
        $formField = SharpFormUploadField::make("text")
            ->setCropRatio("16:9");

        $this->assertArraySubset(
            ["ratioX" => 16, "ratioY" => 9],
            $formField->toArray()
        );
    }
}