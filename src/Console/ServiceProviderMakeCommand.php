<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;

class ServiceProviderMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:provider';
    protected $description = 'Create Sharp’s ServiceProvider';
    protected $type = 'Provider';

    protected function getStub()
    {
        return __DIR__.'/stubs/provider.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Providers';
    }
}
