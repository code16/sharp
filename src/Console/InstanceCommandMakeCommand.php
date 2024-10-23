<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class InstanceCommandMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:instance-command';
    protected $description = 'Create a new instance Command class';
    protected $type = 'InstanceCommand';

    protected function getStub()
    {
        if ($this->option('wizard') !== false) {
            return __DIR__.'/stubs/instance-command.wizard.stub';
        }

        return $this->option('form') !== false
            ? __DIR__.'/stubs/instance-command.form.stub'
            : __DIR__.'/stubs/instance-command.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    protected function getOptions()
    {
        return [
            ['form', 'f', InputOption::VALUE_NONE, 'Create a command with a form.'],
            ['wizard', 'wi', InputOption::VALUE_NONE, 'Create a command with a wizard.'],
        ];
    }
}
