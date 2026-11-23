<?php

use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Http\Api\Embeds\Fixtures\ApiEmbedsFormControllerTestUploadEmbed;
use Code16\Sharp\Tests\Unit\Utils\FakesBreadcrumb;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(FakesBreadcrumb::class);

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1/s-form/person/1');
    Storage::fake('local');
    Storage::fake('public');
    UploadedFile::fake()->create('secret.pdf', 10, 'application/pdf')->storeAs('private', 'secret.pdf', ['disk' => 'local']);
});

function postEmbedFormInit(array $data)
{
    return test()->postJson(
        route('code16.sharp.api.embed.instance.form.show', [
            'embedKey' => ApiEmbedsFormControllerTestUploadEmbed::$key,
            'entityKey' => 'person',
            'instanceId' => 1,
        ]),
        $data
    );
}

function postEmbedFormUpdate(array $data)
{
    return test()->postJson(
        route('code16.sharp.api.embed.instance.form.update', [
            'embedKey' => ApiEmbedsFormControllerTestUploadEmbed::$key,
            'entityKey' => 'person',
            'instanceId' => 1,
        ]),
        $data
    );
}

it('does not sign an unsigned file path sent to the embed form init endpoint', function () {
    postEmbedFormInit([
        'visual' => ['disk' => 'local', 'path' => 'private/secret.pdf'],
    ])
        ->assertJsonValidationErrorFor('visual')
        ->assertJsonMissingPath('data.visual.path_signature')
        ->assertJsonMissingPath('data.visual.download_url');
});

it('rejects a file path with a forged signature sent to the embed form init endpoint', function () {
    postEmbedFormInit([
        'visual' => ['disk' => 'local', 'path' => 'private/secret.pdf', 'path_signature' => uploadPathSignature('local', 'other.pdf')],
    ])
        ->assertJsonValidationErrorFor('visual');
});

it('accepts a signed file path sent to the embed form init endpoint', function () {
    postEmbedFormInit([
        'visual' => signedUpload(['disk' => 'local', 'path' => 'private/secret.pdf']),
    ])
        ->assertOk()
        ->assertJsonPath('data.visual.path', 'private/secret.pdf')
        ->assertJsonPath('data.visual.path_signature', uploadPathSignature('local', 'private/secret.pdf'));
});

it('does not sign the path of a file flagged as uploaded', function () {
    postEmbedFormInit([
        'visual' => ['name' => 'secret.pdf', 'uploaded' => true, 'disk' => 'local', 'path' => 'private/secret.pdf'],
    ])
        ->assertOk()
        ->assertJsonMissingPath('data.visual.path')
        ->assertJsonMissingPath('data.visual.path_signature');
});

it('rejects an unsigned file path in a list item sent to the embed form init endpoint', function () {
    postEmbedFormInit([
        'pictures' => [
            ['id' => 1, 'file' => ['disk' => 'local', 'path' => 'private/secret.pdf']],
        ],
    ])
        ->assertJsonValidationErrorFor('pictures');
});

it('rejects an unsigned file in editor content sent to the embed form init endpoint', function () {
    postEmbedFormInit([
        'slot' => sprintf('<x-sharp-file file="%s"></x-sharp-file>', e(json_encode(['disk' => 'local', 'file_name' => 'private/secret.pdf']))),
    ])
        ->assertJsonValidationErrorFor('slot');

    postEmbedFormInit([
        'slot' => sprintf('<p><x-sharp-file file="%s"></x-sharp-file></p>', e(json_encode(['disk' => 'local', 'file_name' => 'private/secret.pdf']))),
    ])
        ->assertJsonValidationErrorFor('slot');
});

it('accepts a signed file in editor content sent to the embed form init endpoint', function () {
    postEmbedFormInit([
        'slot' => sprintf('<x-sharp-file file="%s"></x-sharp-file>', e(json_encode([
            'disk' => 'local',
            'file_name' => 'private/secret.pdf',
            'path_signature' => uploadPathSignature('local', 'private/secret.pdf'),
        ]))),
    ])
        ->assertOk()
        ->assertJsonPath('data.slot.uploads.0.file.path', 'private/secret.pdf');
});

it('does not sign an unsigned file path sent to the embed form update endpoint', function () {
    postEmbedFormUpdate([
        'visual' => ['disk' => 'local', 'path' => 'private/secret.pdf'],
    ])
        ->assertJsonValidationErrorFor('visual')
        ->assertJsonMissingPath('visual.download_url');

    postEmbedFormUpdate([
        'pictures' => [
            ['id' => 1, 'file' => ['disk' => 'local', 'path' => 'private/secret.pdf']],
        ],
    ])
        ->assertJsonValidationErrorFor('pictures');
});

it('returns signed files from the embed form update endpoint', function () {
    postEmbedFormUpdate([
        'visual' => signedUpload(['disk' => 'local', 'path' => 'private/secret.pdf']),
        'pictures' => [
            ['id' => 1, 'file' => signedUpload(['disk' => 'local', 'path' => 'private/secret.pdf'])],
        ],
    ])
        ->assertOk()
        ->assertJsonPath('visual.path_signature', uploadPathSignature('local', 'private/secret.pdf'))
        ->assertJsonPath('pictures.0.file.path_signature', uploadPathSignature('local', 'private/secret.pdf'));
});
