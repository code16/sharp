<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;

class InstanceCommandMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:instance-command';
    protected $description = 'Create a new instance Command class';
    protected $type = 'InstanceCommand';

    protected function getStub()
    {
        return __DIR__.'/stubs/instance-command.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }
}
