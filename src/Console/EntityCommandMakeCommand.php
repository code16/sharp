<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class EntityCommandMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:entity-command';
    protected $description = 'Create a new Entity List Command class';
    protected $type = 'EntityCommand';

    protected function getStub()
    {
        if ($this->option('wizard') !== false) {
            return __DIR__.'/stubs/entity-command.wizard.stub';
        }

        return $this->option('form') !== false
            ? __DIR__.'/stubs/entity-command.form.stub'
            : __DIR__.'/stubs/entity-command.stub';
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
