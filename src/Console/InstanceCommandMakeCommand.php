<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;

class InstanceCommandMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sharp:make:instance-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new instance command class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'InstanceCommand';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/instance-command.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }
}
