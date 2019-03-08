<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MediaMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sharp:make:media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the media model for sharp uploads';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Media model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/media.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('table')) {
            $replace = $this->replaceTableName($replace);
        } else {
            $replace = $this->removeTableProperty($replace);
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build replacements required to set the DB table name
     *
     * @param  array  $replace
     * @return array
     */
    protected function replaceTableName(array $replace)
    {
        return array_merge($replace, [
            'DummyTable' => $this->option('table'),
        ]);
    }

    /**
     * Build replacements required to remove the table property
     *
     * @param  array  $replace
     * @return array
     */
    protected function removeTableProperty(array $replace)
    {
        return array_merge($replace, [
            "    protected \$table = 'DummyTable';\n" => '',
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['table', 't', InputOption::VALUE_REQUIRED, 'Database table name'],
        ];
    }
}
