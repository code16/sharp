<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class FormUploadControllerTest extends SharpTestCase
{
    protected function setUp()
    {
        parent::setUp();

        config(['filesystems.disks.sharp_uploads' => [
            'driver' => 'local',
            'root' => storage_path('app/tmp'),
        ]]);

        File::deleteDirectory(storage_path("app/tmp"));
    }

    /** @test */
    function we_can_upload_a_file()
    {
        $this->json('post', '/sharp/api/upload', [
                'file' => UploadedFile::fake()->image('image.jpg', 600, 600)
            ])->assertStatus(200)
            ->assertJson(["name" => "image.jpg"]);
    }

    /** @test */
    function when_uploading_an_already_existing_filename_we_change_the_name()
    {
        $this->json('post', '/sharp/api/upload', [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600)
        ])->assertStatus(200)
            ->assertJson(["name" => "image.jpg"]);

        $this->json('post', '/sharp/api/upload', [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600)
        ])->assertStatus(200)
            ->assertJson(["name" => "image-1.jpg"]);
    }

    /** @test */
    function file_is_copied_in_the_wanted_directory()
    {
        $this->json('post', '/sharp/api/upload', [
            'file' => UploadedFile::fake()->image('image.jpg', 600, 600)
        ]);

        $this->assertTrue(File::exists(storage_path("app/tmp/image.jpg")));
    }
}