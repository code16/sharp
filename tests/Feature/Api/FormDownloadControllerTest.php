<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\SharpForm;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class FormDownloadControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['filesystem.local.root' => storage_path('app')]);
        File::deleteDirectory(storage_path("app/files"));

        $this->app['config']->set(
            'app.key', 'base64:'.base64_encode(random_bytes(
                $this->app['config']['app.cipher'] == 'AES-128-CBC' ? 16 : 32
            ))
        );

        $this->app['config']->set(
            'sharp.entities.download.form',
            FormDownloadControllerTestForm::class
        );

        $this->login();
    }

    /** @test */
    function we_can_download_a_file()
    {
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

        $response = $this->postJson(
            route('code16.sharp.api.form.download', [
                'entityKey' => 'download',
                'instanceId' => 1,
                'formUploadFieldKey' => 'file'
            ]),
            ['fileName' => 'test.jpg']
        )->assertStatus(200);

        $this->assertStringEqualsFile(storage_path('app/files/test.jpg'), $response->content());
    }

    /** @test */
    function we_can_download_a_file_with_the_relative_path()
    {
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

        $this->postJson(
            route('code16.sharp.api.form.download', [
                'entityKey' => 'download',
                'instanceId' => 1,
                'formUploadFieldKey' => 'file'
            ]),
            ['fileName' => 'files/test.jpg']
        )->assertStatus(200);
    }

    /** @test */
    function we_get_a_404_for_a_missing_file()
    {
        $this->postJson(
            route('code16.sharp.api.form.download', [
                'entityKey' => 'download',
                'instanceId' => 1,
                'formUploadFieldKey' => 'file'
            ]),
            ['fileName' => 'files/test.jpg']
        )->assertStatus(404);
    }

    /** @test */
    function we_can_download_a_file_in_a_list()
    {
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/list-files', 'test.jpg', ['disk' => 'local']);

        $response = $this->postJson(
            route('code16.sharp.api.form.download', [
                'entityKey' => 'download',
                'instanceId' => 1,
                'formUploadFieldKey' => 'list.file'
            ]),
            ['fileName' => 'test.jpg']
        )->assertStatus(200);

        $this->assertStringEqualsFile(storage_path('app/list-files/test.jpg'), $response->content());
    }
}

class FormDownloadControllerTestForm extends SharpForm
{
    function buildFormFields()
    {
        $this->addField(
            SharpFormUploadField::make("file")
                ->setStorageDisk("local")
                ->setStorageBasePath("files")
        )->addField(
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