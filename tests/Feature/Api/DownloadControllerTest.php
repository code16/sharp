<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DownloadControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake("local");
        $this->login();
    }

    /** @test */
    function we_can_download_a_file_from_a_form_field()
    {
        $this->withoutExceptionHandling();
        
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

        $response = $this
            ->withHeader(
                "referer",
                url('/sharp/s-list/person/s-form/download/1')
            )
            ->get(
                route('code16.sharp.api.download.show', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'disk' => 'local',
                    'path' => '/files/test.jpg',
                ])
            )
            ->assertOk();
        
        $this->assertStringEqualsFile(
            Storage::disk('local')->path('files/test.jpg'), 
            $response->content()
        );
    }

    /** @test */
    function we_can_download_a_file_from_a_show_field()
    {
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

        $response = $this
            ->withHeader(
                "referer",
                url('/sharp/s-list/person/s-show/download/1')
            )
            ->get(
                route('code16.sharp.api.download.show', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'disk' => 'local',
                    'path' => '/files/test.jpg',
                ])
            )
            ->assertOk();

        $this->assertStringEqualsFile(
            Storage::disk('local')->path('files/test.jpg'),
            $response->content()
        );
    }

    /** @test */
    function we_get_a_404_for_a_missing_file()
    {
        $this
            ->withHeader(
                "referer",
                url('/sharp/s-list/person/s-form/download/1')
            )
            ->get(
                route('code16.sharp.api.download.show', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'fileName' => 'test.jpg'
                ])
            )
            ->assertStatus(404);
    }

    /** @test */
    function we_can_not_download_a_file_without_authorization()
    {
        $this->app['config']->set(
            'sharp.entities.download.authorizations', [
                "view" => false,
            ]
        );
        
        $this
            ->withHeader(
                "referer",
                url('/sharp/s-list/person/s-show/download/1')
            )
            ->get(
                route('code16.sharp.api.download.show', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'disk' => 'local',
                    'path' => '/files/test.jpg',
                ])
            )
            ->assertStatus(403);
    }
}