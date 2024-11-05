<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;

class DashboardMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:dashboard';
    protected $description = 'Create a new Dashboard';
    protected $type = 'Dashboard';

    protected function getStub()
    {
        return __DIR__.'/stubs/dashboard.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }
}
