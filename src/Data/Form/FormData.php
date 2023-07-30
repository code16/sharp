<?php

namespace Code16\Sharp\Data\Form;


use Code16\Sharp\Data\Data;

final class FormData extends Data
{
    public function __construct(
        public FormConfigData $config,
    ) {
    }
    
    public static function from(array $config): self
    {
        return new self(
            config: FormConfigData::from($config['config']),
        );
    }
}
