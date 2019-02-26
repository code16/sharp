<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Uploads;

use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;
use Code16\Sharp\Tests\Unit\Form\Eloquent\Utils\TestWithSharpUploadModel;
use Illuminate\Support\Facades\Storage;

class SharpUploadModelTest extends SharpFormEloquentBaseTest
{
    use TestWithSharpUploadModel;

    /** @test */
    function all_thumbnails_are_destroyed_if_we_set_transformed_to_true()
    {
        $file = $this->createImage();

        $upload = $this->createSharpUploadModel($file);

        $upload->thumbnail(100, 100);

        $this->assertTrue(
            Storage::disk('public')->exists("thumbnails/data/100-100/" . basename($file))
        );

        $upload->transformed = true;

        $this->assertFalse(
            Storage::disk('public')->exists("thumbnails/data/100-100/" . basename($file))
        );
    }

    /** @test */
    function when_setting_the_magic_file_attribute_we_can_fill_several_attributes()
    {
        $file = $this->createImage();

        $upload = $this->createSharpUploadModel($file);

        $upload->file = [
            "file_name" => "test/test.png",
            "mime_type" => "test_mime",
            "size" => 1,
        ];

        $this->assertEquals("test/test.png", $upload->file_name);
        $this->assertEquals("test_mime", $upload->mime_type);
        $this->assertEquals(1, $upload->size);
    }

    /** @test */
    function a_thumbnail_is_created_when_asked()
    {
        $file = $this->createImage();

        $upload = $this->createSharpUploadModel($file);

        $this->assertStringStartsWith(
            "/storage/thumbnails/data/-150/" . basename($file),
            $upload->thumbnail(null, 150)
        );

        $this->assertTrue(
            Storage::disk('public')->exists("thumbnails/data/-150/" . basename($file))
        );
    }
}
