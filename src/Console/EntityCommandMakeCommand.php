<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;

class EntityCommandMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:entity-command';
    protected $description = 'Create a new Entity List Command class';
    protected $type = 'EntityCommand';

    protected function getStub()
    {
        return __DIR__.'/stubs/entity-command.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }
}
