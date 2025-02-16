<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class DashboardMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:dashboard';
    protected $description = 'Create a new Dashboard';
    protected $type = 'Dashboard';

    protected function buildClass($name): string
    {
        if (! Str::endsWith($name, 'Dashboard')) {
            throw new \InvalidArgumentException('The Dashboard name should end with "Dashboard"');
        }

        return parent::buildClass($name);
    }

    protected function getStub(): string
    {
        return __DIR__.'/stubs/dashboard.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return str($rootNamespace)
            ->append('\\Sharp\\')
            ->append(
                str($this->getNameInput())
                    ->substr(0, -9)
                    ->toString()
            );
    }
}
