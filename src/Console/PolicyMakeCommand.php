<?php

namespace Code16\Sharp\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class PolicyMakeCommand extends GeneratorCommand
{
    protected $name = 'sharp:make:policy';
    protected $description = 'Create a new Policy class';
    protected $type = 'Policy';

    protected function buildClass($name): string
    {
        if (! Str::endsWith($name, 'Policy')) {
            throw new \InvalidArgumentException('The Policy name should end with "Policy"');
        }

        return parent::buildClass($name);
    }

    protected function getStub(): string
    {
        if ($this->option('single') !== false) {
            return __DIR__.'/stubs/policy.single.stub';
        }

        return $this->option('entity-only') !== false
            ? __DIR__.'/stubs/policy.entity.stub'
            : __DIR__.'/stubs/policy.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return str($rootNamespace)
            ->append('\\Sharp\\')
            ->append(
                str($this->getNameInput())
                    ->substr(0, -6)
                    ->unless($this->option('single') !== false, fn ($name) => $name->plural())
                    ->toString()
            );
    }

    protected function getOptions(): array
    {
        return [
            ['entity-only', 'eo', InputOption::VALUE_NONE, 'When policy only needs an entity rule'],
            ['single', 's', InputOption::VALUE_NONE, 'Relative to a single Entity'],
        ];
    }
}
