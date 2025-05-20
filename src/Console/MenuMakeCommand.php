<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;

class MenuMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:menu';
    protected $description = 'Create a new Menu class';
    protected $type = 'Menu';

    protected function getStub(): string
    {
        return __DIR__.'/stubs/menu.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Sharp';
    }
}
