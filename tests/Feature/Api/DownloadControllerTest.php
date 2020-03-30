<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\Fields\SharpShowFileField;
use Code16\Sharp\Show\Fields\SharpShowListField;
use Code16\Sharp\Show\SharpShow;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DownloadControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake("local");

        $this->app['config']->set('sharp.entities.download.form', DownloadControllerTestForm::class);
        $this->app['config']->set('sharp.entities.download.show', DownloadControllerTestShow::class);

        $this->login();
    }

    /** @test */
    function we_can_download_a_file_from_a_form_field()
    {
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

        $response = $this
            ->getJson(
                route('code16.sharp.api.form.download', [
                    'fieldKey' => 'file',
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'fileName' => 'test.jpg'
                ])
            )
            ->assertStatus(200);
        
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
            ->getJson(
                route('code16.sharp.api.show.download', [
                    'fieldKey' => 'file',
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'fileName' => 'test.jpg'
                ])
            )
            ->assertStatus(200);

        $this->assertStringEqualsFile(
            Storage::disk('local')->path('files/test.jpg'),
            $response->content()
        );
    }

    /** @test */
    function we_can_download_a_file_from_a_single_form_field()
    {
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

        $response = $this
            ->getJson(
                route('code16.sharp.api.form.download', [
                    'fieldKey' => 'file',
                    'entityKey' => 'download',
                    'fileName' => 'test.jpg'
                ])
            )
            ->assertStatus(200);

        $this->assertStringEqualsFile(
            Storage::disk('local')->path('files/test.jpg'),
            $response->content()
        );
    }

    /** @test */
    function we_can_download_a_file_from_a_single_show_field()
    {
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

        $response = $this
            ->getJson(
                route('code16.sharp.api.show.download', [
                    'fieldKey' => 'file',
                    'entityKey' => 'download',
                    'fileName' => 'test.jpg'
                ])
            )
            ->assertStatus(200);

        $this->assertStringEqualsFile(
            Storage::disk('local')->path('files/test.jpg'),
            $response->content()
        );
    }

    /** @test */
    function we_can_download_a_file_with_a_relative_path()
    {
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

        $this
            ->getJson(
                route('code16.sharp.api.form.download', [
                    'fieldKey' => 'file',
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'fileName' => 'test.jpg'
                ])
            )
            ->assertStatus(200);
    }

    /** @test */
    function we_get_a_404_for_a_missing_file()
    {
        $this
            ->getJson(
                route('code16.sharp.api.form.download', [
                    'fieldKey' => 'file',
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'fileName' => 'test.jpg'
                ])
            )
            ->assertStatus(404);
    }

    /** @test */
    function we_can_download_a_file_from_a_form_list_field()
    {
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/list-files', 'test.jpg', ['disk' => 'local']);

        $response = $this
            ->getJson(
                route('code16.sharp.api.form.download', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'fieldKey' => 'list.file',
                    'fileName' => 'test.jpg'
                ]),
            )
            ->assertStatus(200);

        $this->assertStringEqualsFile(
            Storage::disk('local')->path('list-files/test.jpg'),
            $response->content()
        );
    }

    /** @test */
    function we_can_download_a_file_from_a_show_list_field()
    {
        $this->withoutExceptionHandling();
        
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/list-files', 'test.jpg', ['disk' => 'local']);

        $response = $this
            ->getJson(
                route('code16.sharp.api.show.download', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'fieldKey' => 'list.file',
                    'fileName' => 'test.jpg'
                ]),
            )
            ->assertStatus(200);

        $this->assertStringEqualsFile(
            Storage::disk('local')->path('list-files/test.jpg'),
            $response->content()
        );
    }
}

class DownloadControllerTestForm extends SharpForm
{
    function buildFormFields()
    {
        $this
            ->addField(
                SharpFormUploadField::make("file")
                    ->setStorageDisk("local")
                    ->setStorageBasePath("files")
            )
            ->addField(
                SharpFormListField::make("list")
                    ->addItemField(
                        SharpFormUploadField::make("file")
                            ->setStorageDisk("local")
                            ->setStorageBasePath("list-files")
                    )
            );
    }
    function buildFormLayout()
    {
    }
    function find($id): array
    {
    }
    function update($id, array $data): bool
    {
    }
    function delete($id): bool
    {
    }
    function create(): array
    {
    }
}

class DownloadControllerTestShow extends SharpShow
{
    function buildShowFields()
    {
        $this
            ->addField(
                SharpShowFileField::make("file")
                    ->setStorageDisk("local")
                    ->setStorageBasePath("files")
            )
            ->addField(
                SharpShowListField::make("list")
                    ->addItemField(
                        SharpShowFileField::make("file")
                            ->setStorageDisk("local")
                            ->setStorageBasePath("list-files")
                    )
            );
    }
    function find($id): array
    {
    }
    function buildShowLayout()
    {
    }
}