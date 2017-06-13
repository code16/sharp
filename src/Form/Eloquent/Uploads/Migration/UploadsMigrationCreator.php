<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Migration;

use Illuminate\Database\Migrations\MigrationCreator;

class UploadsMigrationCreator extends MigrationCreator
{

    /**
     * Get the migration stub file.
     *
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    protected function getStub($table, $create)
    {
        return $this->files->get(__DIR__.'/uploads.stub');
    }
}