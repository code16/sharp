<?php

namespace Code16\Sharp\Console;

use Code16\Sharp\Console\Utils\WithModel;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class FormMakeCommand extends GeneratorCommand
{
    use WithModel;

    protected $name = 'sharp:make:form';
    protected $description = 'Create a new Form class';
    protected $type = 'SharpForm';
    private ?string $entityName = null;

    protected function buildClass($name): string
    {
        if (! Str::endsWith($name, 'Form')) {
            throw new \InvalidArgumentException('The form name should end with "Form"');
        }

        $replace = [];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements($replace);
        }

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name),
        );
    }

    protected function getStub(): string
    {
        if ($this->option('single') !== false) {
            return __DIR__.'/stubs/form.single.stub';
        }

        if (! $this->option('model')) {
            return __DIR__.'/stubs/form.stub';
        }

        return __DIR__.'/stubs/form.model.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return str($rootNamespace)
            ->append('\\Sharp\\')
            ->append(
                str($this->getNameInput())
                    ->substr(0, -4)
                    ->unless($this->option('single') !== false, fn ($name) => $name->plural())
                    ->toString()
            );
    }

    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_REQUIRED, 'The model that the form handles'],
            ['single', 'si', InputOption::VALUE_NONE, 'If the form should be a single form'],
        ];
    }
}
