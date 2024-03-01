<?php

namespace Code16\Sharp\Utils\Fields\Validation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class SharpFileValidation
{
    use Conditionable;

    protected function __construct(protected File $baseRule)
    {
    }

    public static function make(): static
    {
        return new static(
            new class extends File
            {
                public function toArray(): array
                {
                    return $this->buildValidationRules();
                }
            }
        );
    }

    public function extensions($extensions)
    {
        $this->baseRule->extensions($extensions);

        return $this;
    }

    public function size($size)
    {
        $this->baseRule->size($size);

        return $this;
    }

    public function between($minSize, $maxSize)
    {
        $this->baseRule->between($minSize, $maxSize);

        return $this;
    }

    public function min($size)
    {
        $this->baseRule->min($size);

        return $this;
    }

    public function max($size)
    {
        $this->baseRule->max($size);

        return $this;
    }
    
    public static function getRulesArrayFrom(File $file): array
    {
        $fakeValidator = Validator::make([], []);
        
        $fileRules = [];
        
        Validator::resolver(function ($translator, $data, $rules, $messages, $attributes) use (&$fileRules) {
            $fileRules = $rules['file'];
            return new \Illuminate\Validation\Validator($translator, $data, $rules, $messages, $attributes);
        });
        
        (clone $file)
            ->setValidator($fakeValidator)
            ->setData([])
            ->passes('file', null);
        
        Validator::resolver(function ($translator, $data, $rules, $messages, $attributes) {
            return new \Illuminate\Validation\Validator($translator, $data, $rules, $messages, $attributes);
        });
        
        return collect($fileRules)
            ->map(fn ($value) => is_object($value) ? $value->__toString() : $value)
            ->toArray();
    }

    public function toArray(): array
    {
        return collect($this->baseRule->toArray())
            ->map(fn ($value) => is_object($value) ? $value->__toString() : $value)
            ->toArray();
    }
}
