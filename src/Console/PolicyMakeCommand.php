<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;

class PolicyMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:policy';
    protected $description = 'Create a new Policy class';
    protected $type = 'Policy';

    protected function getStub()
    {
        return __DIR__.'/stubs/policy.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }
}
