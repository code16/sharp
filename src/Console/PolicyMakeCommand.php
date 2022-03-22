<?php

namespace Code16\Sharp\Console;

use Illuminate\Foundation\Console\PolicyMakeCommand as Base;

class PolicyMakeCommand extends Base
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sharp:make:policy';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('model')
                    ? __DIR__.'/stubs/policy.stub'
                    : __DIR__.'/stubs/policy.plain.stub';
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
}
