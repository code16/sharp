<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;

class ValidatorMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:validator';
    protected $description = 'Create a new Form Validator class';
    protected $type = 'Validator';

    protected function getStub()
    {
        return __DIR__.'/stubs/validator.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }
}
