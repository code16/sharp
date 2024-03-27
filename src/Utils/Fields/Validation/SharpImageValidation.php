<?php

namespace Code16\Sharp\Utils\Fields\Validation;

use Illuminate\Validation\Rules\Dimensions;
use Illuminate\Validation\Rules\ImageFile;

class SharpImageValidation extends SharpFileValidation
{
    public static function make(): static
    {
        return new static(
            new class extends ImageFile
            {
                public function toArray(): array
                {
                    return $this->buildValidationRules();
                }
            }
        );
    }

    public function dimensions(Dimensions $dimensions)
    {
        $this->baseRule->dimensions($dimensions);

        return $this;
    }
}
