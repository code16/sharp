<?php

namespace Code16\Sharp\Console;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ListFilterMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sharp:make:list-filter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new entity list filter class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'EntityListFilter';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $namespace = $this->getNamespace($name);

        $replace = [];
        if (! $this->option('multiple')) {
            $replace = $this->removeMultipleInterface($replace);
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * Build replacements required to remove the EntityListMultipleFilter interface
     *
     * @param  array  $replace
     * @return array
     */
    protected function removeMultipleInterface(array $replace)
    {
        return array_merge($replace, [
            "use Code16\Sharp\EntityList\EntityListMultipleFilter;\n" => '',
            ', EntityListMultipleFilter' => '',
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('required')
                    ? __DIR__.'/stubs/entity-list-filter.required.stub'
                    : __DIR__.'/stubs/entity-list-filter.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Sharp';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['required', 'r', InputOption::VALUE_NONE, 'Create a filter whoes value cannot be null'],
            ['multiple', 'm', InputOption::VALUE_NONE, 'Create a filter that can have multiple values'],
        ];
    }
}
