<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields;

use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\SharpTestCase;

class SharpFormUploadFieldTest extends SharpTestCase
{
    /** @test */
    function only_default_values_are_set()
    {
        $formField = SharpFormUploadField::make("file");

        $this->assertEquals([
                "key" => "file", "type" => "upload", "compactThumbnail" => false
            ], $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_maxFileSize()
    {
        $formField = SharpFormUploadField::make("text")
            ->setMaxFileSize(.5);

        $this->assertArrayContainsSubset(
            ["maxFileSize" => 0.5],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_compactThumbnail()
    {
        $formField = SharpFormUploadField::make("text")
            ->setCompactThumbnail();

        $this->assertArrayContainsSubset(
            ["compactThumbnail" => true],
            $formField->toArray()
        );
    }


    /** @test */
    function we_can_define_fileFilter()
    {
        $formField = SharpFormUploadField::make("text")
            ->setFileFilter("jpg");

        $this->assertArrayContainsSubset(
            ["fileFilter" => [".jpg"]],
            $formField->toArray()
        );

        $formField = SharpFormUploadField::make("text")
            ->setFileFilter("jpg, gif");

        $this->assertArrayContainsSubset(
            ["fileFilter" => [".jpg", ".gif"]],
            $formField->toArray()
        );

        $formField = SharpFormUploadField::make("text")
            ->setFileFilter(["jpg", "gif "]);

        $this->assertArrayContainsSubset(
            ["fileFilter" => [".jpg", ".gif"]],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_cropRatio()
    {
        $formField = SharpFormUploadField::make("text")
            ->setCropRatio("16:9");

        $this->assertArrayContainsSubset(
            ["ratioX" => 16, "ratioY" => 9],
            $formField->toArray()
        );
    }

    /** @test */
    function we_can_define_croppableFileTypes()
    {
        $formField = SharpFormUploadField::make("text")
            ->setCropRatio("16:9", ["jpg", "jpeg"]);

        $this->assertArrayContainsSubset(
            ["ratioX" => 16, "ratioY" => 9, "croppableFileTypes" => [".jpg", ".jpeg"]],
            $formField->toArray()
        );

        $formField = SharpFormUploadField::make("text")
            ->setCropRatio("16:9", [".jpg", ".jpeg"]);

        $this->assertArrayContainsSubset(
            ["ratioX" => 16, "ratioY" => 9, "croppableFileTypes" => [".jpg", ".jpeg"]],
            $formField->toArray()
        );
    }
}