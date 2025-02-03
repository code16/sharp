<?php

namespace Code16\Sharp\Data\Form;

use Code16\Sharp\Data\Data;

/**
 * @internal
 */
final class FormConfigData extends Data
{
    public function __construct(
        public bool $isSingle = false,
    ) {}

    public static function from(array $config): self
    {
        return new self(...$config);
    }
}
