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
        return $this->option('with-form')
            ? __DIR__.'/stubs/instance-command-with-form.stub'
            : __DIR__.'/stubs/instance-command.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    protected function getOptions()
    {
        return [
            ['with-form', null, null, 'Create a command with a form.'],
        ];
    }
}
