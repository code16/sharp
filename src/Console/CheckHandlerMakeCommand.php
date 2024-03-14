<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;

class CheckHandlerMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:check-handler';
    protected $description = 'Create a new Check Handler class';
    protected $type = 'CheckHandler';

    protected function getStub()
    {
        return __DIR__.'/stubs/check-handler.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }
}
