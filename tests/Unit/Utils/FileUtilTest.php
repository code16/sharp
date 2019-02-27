<?php

namespace Code16\Sharp\Tests\Unit\Utils;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\FileUtil;
use Illuminate\Support\Facades\File;

class FileUtilTest extends SharpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(storage_path("app/tmp"));
    }

    /** @test */
    function we_keep_the_file_name_if_it_is_the_first_one()
    {
        $fileUtil = new FileUtil();

        $this->assertEquals(
            "test.txt",
            $fileUtil->findAvailableName("test.txt", "tmp", "local")
        );
    }

    /** @test */
    function we_add_a_number_suffix_if_needed()
    {
        $fileUtil = new FileUtil();

        mkdir(storage_path("app/tmp/"));
        touch(storage_path("app/tmp/test.txt"));

        $this->assertEquals(
            "test-1.txt",
            $fileUtil->findAvailableName("test.txt", "tmp", "local")
        );

        touch(storage_path("app/tmp/test-1.txt"));

        $this->assertEquals(
            "test-2.txt",
            $fileUtil->findAvailableName("test.txt", "tmp", "local")
        );
    }

    /** @test */
    function we_normalize_file_name()
    {
        $fileUtil = new FileUtil();

        $this->assertEquals(
            "test.txt",
            $fileUtil->findAvailableName("ôéàtest*.txt", "tmp", "local")
        );
    }
}