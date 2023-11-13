<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Utils\Entities\SharpEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DownloadControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->buildTheWorld();
        $this->login();
    }

    /** @test */
    public function we_can_download_a_file_from_a_form_field()
    {
        $this->disableSharpAuthorizationChecks();
        $this->withoutExceptionHandling();

        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

        $response = $this
            ->withHeader(
                'referer',
                url('/sharp/s-list/person/s-form/download/1'),
            )
            ->get(
                route('code16.sharp.api.download.show', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'disk' => 'local',
                    'path' => '/files/test.jpg',
                ]),
            )
            ->assertOk();

        $this->assertStringEqualsFile(
            Storage::disk('local')->path('files/test.jpg'),
            $response->content(),
        );
    }

    /** @test */
    public function we_can_download_a_file_from_a_show_field()
    {
        $this->disableSharpAuthorizationChecks();
        $file = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file->storeAs('/files', 'test.jpg', ['disk' => 'local']);

        $response = $this
            ->withHeader(
                'referer',
                url('/sharp/s-list/person/s-show/download/1'),
            )
            ->get(
                route('code16.sharp.api.download.show', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'disk' => 'local',
                    'path' => '/files/test.jpg',
                ]),
            )
            ->assertOk();

        $this->assertStringEqualsFile(
            Storage::disk('local')->path('files/test.jpg'),
            $response->content(),
        );
    }

    /** @test */
    public function we_get_a_404_for_a_missing_file()
    {
        $this->disableSharpAuthorizationChecks();
        $this
            ->withHeader(
                'referer',
                url('/sharp/s-list/person/s-form/download/1'),
            )
            ->get(
                route('code16.sharp.api.download.show', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'fileName' => 'test.jpg',
                ]),
            )
            ->assertStatus(404);
    }

    /** @test */
    public function we_can_not_download_a_file_without_authorization()
    {
        app(SharpEntityManager::class)
            ->entityFor('download')
            ->setProhibitedActions(['view']);

        $this
            ->withHeader(
                'referer',
                url('/sharp/s-list/person/s-show/download/1'),
            )
            ->get(
                route('code16.sharp.api.download.show', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                    'disk' => 'local',
                    'path' => '/files/test.jpg',
                ]),
            )
            ->assertStatus(403);
    }

    protected function buildTheWorld(bool $singleShow = false): void
    {
        parent::buildTheWorld($singleShow);

        $this->app['config']->set(
            'sharp.entities.download',
            DownloadTestEntity::class,
        );
    }
}

class DownloadTestEntity extends SharpEntity
{
    public function setProhibitedActions(array $prohibitedActions): self
    {
        $this->prohibitedActions = $prohibitedActions;

        return $this;
    }
}
