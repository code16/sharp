<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Migration;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class CreateUploadsMigration extends Command
{
    /**
     * @var string
     */
    protected $signature = 'sharp:create_uploads_migration {table_name}';

    /**
     * @var string
     */
    protected $description = 'Creates the sharp uploads table migration file.';

    /**
     * @var UploadsMigrationCreator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * Create a new migration install command instance.
     *
     * @param  UploadsMigrationCreator $creator
     * @param  Composer  $composer
     */
    public function __construct(UploadsMigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $table = trim($this->input->getArgument('table_name'));
        $name = "create_{$table}_table";

        $this->writeMigration($name, $table);

        $this->composer->dumpAutoloads();
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string  $name
     * @param  string  $table
     * @return string
     */
    protected function writeMigration($name, $table)
    {
        $file = pathinfo($this->creator->create(
            $name, $this->getMigrationPath(), $table, true
        ), PATHINFO_FILENAME);

        $this->line("<info>Created Migration:</info> {$file}");
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        return $this->laravel->databasePath().DIRECTORY_SEPARATOR.'migrations';
    }
}