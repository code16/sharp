<?php

use Code16\Sharp\Utils\FileUtil;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
//    config('sharp.uploads.tmp_disk', 'local');
    Storage::fake('local');
});

it('keeps the file name if it is the first one', function () {
    $fileUtil = new FileUtil();

    $this->assertEquals(
        'test.txt',
        $fileUtil->findAvailableName('test.txt', 'tmp', 'local'),
    );
});

it('adds a number suffix if needed', function () {
    $fileUtil = new FileUtil();

    UploadedFile::fake()
        ->create('test.txt', 1, 'text/plain')
        ->storeAs('tmp', 'test.txt');

    $this->assertEquals(
        'test-1.txt',
        $fileUtil->findAvailableName('test.txt', 'tmp', 'local'),
    );

    UploadedFile::fake()
        ->create('test.txt', 1, 'text/plain')
        ->storeAs('tmp', 'test-1.txt');

    $this->assertEquals(
        'test-2.txt',
        $fileUtil->findAvailableName('test.txt', 'tmp', 'local'),
    );
});

it('normalizes file name', function () {
    $fileUtil = new FileUtil();

    $this->assertEquals(
        'test.txt',
        $fileUtil->findAvailableName('ôéàtest*.txt', 'tmp', 'local'),
    );
});
