<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Migration;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class CreateUploadsMigration extends Command
{
    protected $signature = 'sharp:create_uploads_migration {table_name}';
    protected $description = 'Creates the sharp uploads table migration file.';

    public function __construct(protected UploadsMigrationCreator $creator, protected Composer $composer)
    {
        parent::__construct();
    }

    public function handle()
    {
        $table = trim($this->input->getArgument('table_name'));
        $name = "create_{$table}_table";

        $this->writeMigration($name, $table);

        $this->composer->dumpAutoloads();
    }

    protected function writeMigration($name, $table)
    {
        $file = pathinfo(
            $this->creator->create($name, $this->getMigrationPath(), $table, true,),
            PATHINFO_FILENAME
        );

        $this->line("<info>Created Migration:</info> {$file}");
    }
    
    protected function getMigrationPath()
    {
        return $this->laravel->databasePath().DIRECTORY_SEPARATOR.'migrations';
    }
}
