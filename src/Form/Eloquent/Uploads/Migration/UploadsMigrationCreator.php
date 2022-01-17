<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Migration;

use Illuminate\Database\Migrations\MigrationCreator;

class UploadsMigrationCreator extends MigrationCreator
{
    public function __construct()
    {
        parent::__construct(app('files'), app()->basePath('stubs'));
    }

    /**
     * Get the migration stub file.
     *
     * @param string $table
     * @param bool   $create
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return string
     */
    protected function getStub($table, $create)
    {
        return $this->files->get(__DIR__.'/uploads.stub');
    }
}
