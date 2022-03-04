<?php

namespace Code16\Sharp\Console;

use Code16\Sharp\Console\Utils\WithModel;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ListMakeCommand extends GeneratorCommand
{
    use WithModel;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sharp:make:entity-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new entity list class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'SharpEntityList';

    /**
     * Build the class with the given name.
     *
     * @param string $name
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('model')
            ? __DIR__.'/stubs/sharp-entity-list.stub'
            : __DIR__.'/stubs/sharp-entity-list.plain.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the list displays'],
        ];
    }
}
